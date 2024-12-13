<?php

namespace Permissions;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * @property int         $id
 * @property string      $name
 * @property array|null  $abilities
 * @property bool        $default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Permission extends Model
{
    const ID = 'id';
    const NAME = 'name';
    const ABILITIES = 'abilities';
    const DEFAULT = 'default';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const TABLE = 'permissions';

    protected $table = self::TABLE;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::ABILITIES => 'array',
        self::DEFAULT => 'boolean',
    ];
}
