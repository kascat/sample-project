<?php

namespace Users;

use App\Providers\AuthServiceProvider;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Permissions\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Users\Enums\UserRoleEnum;
use Users\Enums\UserStatusEnum;

/**
 * Class User
 * @property int             $id
 * @property int             $permission_id
 * @property string          $name
 * @property string|null     $email
 * @property string|null     $phone
 * @property UserRoleEnum    $role
 * @property UserStatusEnum  $status
 * @property Carbon|null     $email_verified_at
 * @property string          $password
 * @property Carbon|null     $expires_in
 * @property int|null        $login_time
 * @property string|null     $remember_token
 * @property Carbon|null     $created_at
 * @property Carbon|null     $updated_at
 *
 * @property-read Permission $permission
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ID = 'id';
    const PERMISSION_ID = 'permission_id';
    const NAME = 'name';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const ROLE = 'role';
    const STATUS = 'status';
    const EMAIL_VERIFIED_AT = 'email_verified_at';
    const PASSWORD = 'password';
    const EXPIRES_IN = 'expires_in';
    const LOGIN_TIME = 'login_time';
    const REMEMBER_TOKEN = 'remember_token';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const RELATION_PERMISSION = 'permission';

    const TABLE = 'users';

    protected $table = self::TABLE;

    protected $guarded = [
        self::ID,
        self::EMAIL_VERIFIED_AT,
        self::REMEMBER_TOKEN,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
    ];

    protected $casts = [
        self::ROLE => UserRoleEnum::class,
        self::STATUS => UserStatusEnum::class,
        self::EMAIL_VERIFIED_AT => 'datetime',
        self::EXPIRES_IN => 'datetime:d/m/Y H:i',
    ];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, self::PERMISSION_ID, Permission::ID);
    }

    public static function loggedUserFilters(): array
    {
        /** @var self|null $loggedUser */
        $loggedUser = Auth::guard(AuthServiceProvider::GUARD_USER)->user();

        if (null === $loggedUser) {
            return [];
        }

        return array_filter([
            'user_id' => $loggedUser->id,
        ]);
    }
}
