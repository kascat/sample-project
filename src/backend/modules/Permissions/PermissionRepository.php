<?php

namespace Permissions;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class PermissionRepository
 */
class PermissionRepository
{
    public static function defautFiltersQuery(mixed $filters = []): Builder
    {
        return Permission::query()->when($filters[Permission::NAME] ?? null, function ($query, $value) {
            return $query->where(Permission::NAME, 'like', "%$value%");
        });
    }
}
