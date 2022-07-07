<?php

namespace Permissions;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * @package Permissions
 */
class Permission extends Model
{
    const PERMISSIONS = [
        'users',
        'permissions'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'abilities',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'abilities' => 'array',
    ];
}
