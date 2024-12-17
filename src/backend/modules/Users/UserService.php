<?php

namespace Users;

use App\Utils\Formatter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Kascat\EasyModule\Core\Service;
use Laravel\Sanctum\NewAccessToken;
use Permissions\Enums\AbilitiesEnum;
use Permissions\Permission;
use Throwable;
use Users\Enums\UserRoleEnum;
use Users\Enums\UserStatusEnum;
use Users\Mail\UserPasswordSettingMail;

/**
 * Class UserService
 */
class UserService extends Service
{
    public function register(array $data): array
    {
        DB::beginTransaction();

        try {
            /** @var Permission $defaultPermission */
            $defaultPermission = Permission::query()
                ->where(Permission::DEFAULT, true)
                ->firstOrFail();

            $user = User::query()->create([
                User::PERMISSION_ID => $defaultPermission->id,
                User::ROLE => UserRoleEnum::ACCOUNT_OWNER,
                User::NAME => $data[User::NAME],
                User::EMAIL => $data[User::EMAIL],
                User::PASSWORD => bcrypt($data[User::PASSWORD]),
                User::STATUS => UserStatusEnum::ACTIVE,
            ]);

            DB::commit();

            $token = $this->generateUserToken($user);

            return self::buildReturn([
                'token' => $token->plainTextToken
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('UserService: Failed to register user', [
                'errorMessage' => $exception->getMessage(),
            ]);

            return self::buildReturn([
                'message' => __('unexpected_error')
            ], 500);
        }
    }

    public function login(array $userData): array
    {
        /** @var User|null $user */
        $user = UserRepository::searchFromEmail($userData[User::EMAIL])->first();

        if (!Hash::check($userData[User::PASSWORD], $user->password ?? null)) {
            throw self::exception([
                'message' => __('invalid_email_or_password')
            ], 403);
        }

        if ($user->status !== UserStatusEnum::ACTIVE) {
            throw self::exception([
                'message' => __('inactive_user'),
            ], 403);
        }

        if (!!$user->expires_in && Carbon::now() > $user->expires_in) {
            throw self::exception([
                'message' => __('expired_access'),
            ], 403);
        }

        $token = $this->generateUserToken($user);

        if ($user->login_time && !$user->expires_in) {
            $time = $user->login_time ?: 0;
            $expiresIn = (new \DateTime())->add(new \DateInterval("PT{$time}M"));

            $user->update([User::EXPIRES_IN => $expiresIn]);
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

    public function loggedUser(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $user->load(User::RELATION_PERMISSION,);

        $permission = $user->permission;

        return self::buildReturn(
            [
                User::ID => $user->id,
                User::PERMISSION_ID => $user->permission_id,
                User::NAME => $user->name,
                User::EMAIL => $user->email,
                User::PHONE => $user->phone,
                User::ROLE => $user->role,
                User::STATUS => $user->status,
                User::EXPIRES_IN => $user->expires_in,
                User::LOGIN_TIME => $user->login_time,
                Formatter::camelToSnakeCase(User::RELATION_PERMISSION) => [
                    Permission::ID => $permission->id,
                    Permission::NAME => $permission->name,
                    Permission::ABILITIES => $permission->abilities,
                ],
            ]
        );
    }

    public function index(array $filters): array
    {
        $usersQuery = UserRepository::defautFiltersQuery($filters);

        return self::buildReturn(
            $usersQuery
                ->with(request(self::WITH_RELATIONSHIP) ?? [])
                ->paginate(request(self::PER_PAGE))
        );
    }

    public function store(array $data): array
    {
        DB::beginTransaction();

        try {
            $definedPassword = !!($data[User::PASSWORD] ?? false);
            $randomPassword = Carbon::now()->timestamp;
            $data[User::PASSWORD] = bcrypt(!$definedPassword ? $randomPassword : $data[User::PASSWORD]);

            $data[User::STATUS] = $definedPassword ? UserStatusEnum::ACTIVE : UserStatusEnum::PENDING_PASSWORD;

            /** @var User $user */
            $user = User::query()->create($data);

            if (!$definedPassword) {
                $token = $user->createToken('Create password', [AbilitiesEnum::PASSWORD_SETTING]);
                Mail::to($user->email)->send(new UserPasswordSettingMail($user, $token->plainTextToken));
            }

            DB::commit();

            return self::buildReturn($user);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('UserService: Failed to create user', [
                'errorMessage' => $exception->getMessage(),
            ]);

            return self::buildReturn([
                'message' => __('unexpected_error')
            ], 500);
        }
    }

    public function update(User $user, array $data): array
    {
        if (isset($data[User::PASSWORD]) && $data[User::PASSWORD]) {
            $data[User::PASSWORD] = bcrypt($data[User::PASSWORD]);
            if ($user->status === UserStatusEnum::PENDING_PASSWORD) {
                $data[User::STATUS] = UserStatusEnum::ACTIVE;
            }
        } else {
            unset($data[User::PASSWORD]);
        }

        $user->update($data);

        return self::buildReturn($user);
    }

    public function updateStatus(User $user, array $data): array
    {
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
        $user = UserRepository::searchFromEmail($userData[User::EMAIL])->first();

        if (
            !$user ||
            !in_array($user->status->value, [UserStatusEnum::ACTIVE->value, UserStatusEnum::PENDING_PASSWORD->value])
        ) {
            throw self::exception([
                'message' => __('user_not_found_or_blocked')
            ], 403);
        }

        $token = $user->createToken('Forgot password', [AbilitiesEnum::PASSWORD_SETTING]);

        $user->update([User::STATUS => UserStatusEnum::PENDING_PASSWORD]);

        Mail::to($user->email)->send(new UserPasswordSettingMail($user, $token->plainTextToken));

        return self::buildReturn();
    }

    public function passwordSetting(array $userData): array
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->status !== UserStatusEnum::PENDING_PASSWORD) {
            throw self::exception([
                'message' => __('user_status_does_not_allow_password_change')
            ], 403);
        }

        $user->update([
            User::STATUS => UserStatusEnum::ACTIVE,
            User::PASSWORD => bcrypt($userData[User::PASSWORD])
        ]);

        return self::buildReturn([]);
    }

    private function generateUserToken(User $user): NewAccessToken
    {
        $abilities = $user->permission->abilities ?? [];

        $isApiUser = UserRoleEnum::API->value === $user->role->value;
        $tokenName = $isApiUser ? 'Api token' : 'User token';
        $tokenExpiration = $isApiUser ? null : now()->addWeek();

        return $user->createToken($tokenName, $abilities, $tokenExpiration);
    }
}
