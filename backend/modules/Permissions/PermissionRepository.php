<?php

namespace Permissions;

/**
 * Class PermissionRepository
 * @package Permissions
 */
class PermissionRepository
{
    /**
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\HigherOrderWhenProxy|\Illuminate\Support\Traits\Conditionable|mixed
     */
    public static function index(array $filters = [])
    {
        return Permission::query()->when($filters['name'] ?? null, function ($query, $name) {
            return $query->where('name', 'like', "%$name%");
        });
    }
}
