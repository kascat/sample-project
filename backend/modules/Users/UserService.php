<?php

namespace Users;

use App\Mail\SendEmailToResetPassword;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Kascat\EasyModule\Core\Service;
use Permissions\Enums\AbilitiesEnum;
use Users\Enums\UserRoleEnum;

/**
 * Class UserService
 */
class UserService extends Service
{
    public function login(array $userData): array
    {
        /** @var User $user */
        $user = UserRepository::searchFromEmail($userData['email'])->first();
        if (!Hash::check($userData['password'], $user->password ?? null)) {
            throw self::exception([
                'message' => 'E-mail ou senha inválidos'
            ], 403);
        }

        if ($user->status !== User::STATUS_ACTIVE) {
            throw self::exception([
                'message' => 'Usuário inativo'
            ], 403);
        }

        $abilities = $user->permission->abilities ?? [];

        $isApiUser = UserRoleEnum::API->value === $user->role;
        $tokenName = $isApiUser ? 'Api token' : 'Login token';
        $tokenExpiration = $isApiUser ? null : now()->addWeek();

        $token = $user->createToken($tokenName, $abilities, $tokenExpiration);

        if ($user->login_time && !$user->expires_in) {
            $time      = $user->login_time ?: 0;
            $expiresIn = (new \DateTime())->add(new \DateInterval("PT${time}M"));

            $user->update(['expires_in' => $expiresIn]);
        }

        return self::buildReturn([
            'token' => $token->plainTextToken
        ]);
    }

    public function logout($token): array
    {
        $tokenId = explode('|', $token)[0];

        Auth::user()->tokens()->where('id', $tokenId)->delete();

        return self::buildReturn(['message' => 'Token Revoked']);
    }

    public function logoutAll(): array
    {
        Auth::user()->tokens()->delete();

        return self::buildReturn(['message' => 'Tokens Revoked']);
    }

    public function index(array $filters): array
    {
        $usersQuery = UserRepository::defautFiltersQuery($filters);

        return self::buildReturn(
            $usersQuery
                ->with(\request(self::WITH_RELATIONSHIP) ?? [])
                ->paginate(\request(self::PER_PAGE))
        );
    }

    public function store(array $data): array
    {
        $definedPassword  = $data['password'] ?? false;
        $randomPassword   = Carbon::now()->timestamp;
        $data['password'] = bcrypt(!$definedPassword ? $randomPassword : $data['password']);

        if ($definedPassword) {
            $data['status'] = User::STATUS_ACTIVE;
        }

        $data = self::prepareData($data, [
            'expires_in' => fn($value) => self::formatDatetimeToSave($value),
        ]);

        $user = User::create($data);

        if (!$definedPassword) {
            $token = $user->createToken('Create password', [ AbilitiesEnum::RESET_PASSWORD ]);
            Mail::to($user->email)->send(new SendEmailToResetPassword($user, $token));
        }

        return self::buildReturn($user);
    }

    public function update(User $user, array $data): array
    {
        if (isset($data['email'])) {
            /** @var User $userWithEmail */
            $userWithEmail = UserRepository::searchFromEmail($data['email'])->first();

            if ($userWithEmail && $userWithEmail->id !== $user->id) {
                throw self::exception([
                    'message' => 'E-mail já está em uso'
                ]);
            }
        }

        if ($data['password'] ?? false) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $data = self::prepareData($data, [
            'expires_in' => fn($value) => self::formatDatetimeToSave($value),
        ]);

        $user->update($data);

        return self::buildReturn($user);
    }

    public function destroy(User $user): array
    {
        $user->delete();

        return self::buildReturn();
    }

    public function forgotPassword(array $userData): array
    {
        /** @var User $user */
        $user = UserRepository::searchFromEmail($userData['email'])->first();

        if (!$user || !in_array($user->status, [User::STATUS_ACTIVE, User::STATUS_PENDING_PASSWORD])) {
            throw self::exception([
                'message' => 'Usuário não encontrado ou bloqueado'
            ], 403);
        }

        $token = $user->createToken('Forgot password', [ AbilitiesEnum::RESET_PASSWORD ]);
        $user->status = User::STATUS_PENDING_PASSWORD;
        $user->save();

        Mail::to($user->email)->send(new SendEmailToResetPassword($user, $token));

        return self::buildReturn($token->plainTextToken);
    }

    public function resetPassword(array $userData): array
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->status !== User::STATUS_PENDING_PASSWORD) {
            throw self::exception([
                'message' => 'O status do usuário não permite a alteração de senha'
            ], 403);
        }

        $user->update([
            'status'   => User::STATUS_ACTIVE,
            'password' => bcrypt($userData['password'])
        ]);

        return self::buildReturn([]);
    }
}
