<?php

namespace Users;

use App\Http\Services\Service;
use App\Mail\SendEmailToResetPassword;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Class UserService
 * @package Users
 */
class UserService extends Service
{
    /**
     * @param array $userData
     * @return array
     */
    public function login(array $userData)
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

        $token = $user->createToken('Api token', $abilities);

        if ($user->login_time && !$user->expires_in) {
            $time      = $user->login_time ?: 0;
            $expiresIn = (new \DateTime())->add(new \DateInterval("PT${time}M"));

            $user->update(['expires_in' => $expiresIn]);
        }

        return self::buildReturn([
            'token' => $token->plainTextToken
        ]);
    }

    /**
     * @param $token
     * @return string[]
     */
    public function logout($token)
    {
        $tokenId = explode('|', $token)[0];

        Auth::user()->tokens()->where('id', $tokenId)->delete();

        return self::buildReturn(['message' => 'Token Revoked']);
    }

    /**
     * @return string[]
     */
    public function logoutAll()
    {
        Auth::user()->tokens()->delete();

        return self::buildReturn(['message' => 'Tokens Revoked']);
    }

    /**
     * @param array $filters
     * @return array
     */
    public function index(array $filters)
    {
        $filters = UserService::injectLoggedUserFilters($filters);
        $usersQuery = UserRepository::index($filters);

        return self::buildReturn(
            $usersQuery
                ->with(\request()->with ?? [])
                ->paginate(\request()->perPage)
        );
    }

    /**
     * @param array $data
     * @return array
     */
    public function store(array $data)
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
            $token = $user->createToken('Create password');
            Mail::to($user->email)->send(new SendEmailToResetPassword($user, $token));
        }

        return self::buildReturn($user);
    }

    /**
     * @param User $user
     * @param array $data
     * @return array
     */
    public function update(User $user, array $data)
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

    /**
     * @param User $user
     * @return array
     */
    public function destroy(User $user)
    {
        $user->delete();

        return self::buildReturn();
    }

    /**
     * @param array $userData
     * @return array
     */
    public function forgotPassword(array $userData)
    {
        /** @var User $user */
        $user = UserRepository::searchFromEmail($userData['email'])->first();

        if (!$user || !in_array($user->status, [User::STATUS_ACTIVE, User::STATUS_PENDING_PASSWORD])) {
            throw self::exception([
                'message' => 'Usuário não encontrado ou bloqueado'
            ], 403);
        }

        $abilities = $user->permission->abilities ?? [];

        $token        = $user->createToken('Forgot password', $abilities);
        $user->status = User::STATUS_PENDING_PASSWORD;
        $user->save();

        Mail::to($user->email)->send(new SendEmailToResetPassword($user, $token));

        return self::buildReturn($token->plainTextToken);
    }

    /**
     * @param array $userData
     * @return array
     */
    public function resetPassword(array $userData)
    {
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

    /**
     * @param array $filters
     * @return array
     */
    public static function injectLoggedUserFilters(array $filters = [])
    {
        $loggedUser = Auth::user();

        if (!$loggedUser) {
            return $filters;
        }

        // Injetar filtros de acordo com usuário logado
        // if ($loggedUser->role === User::USER_ROLE_MEMBER) {
        //     $filters['member_id'] = $loggedUser->id;
        // }

        return $filters;
    }
}
