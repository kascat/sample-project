<?php

namespace Users;

/**
 * Class UserRepository
 * @package Users
 */
class UserRepository
{
    /**
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function searchFromEmail(string $email)
    {
        return User::query()->whereEmail($email);
    }

    /**
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function index($filters = [])
    {
        return User::query()
            ->when($filters['name'] ?? null, function ($query, $name) {
                return $query->where('name', 'like', "%$name%");
            })->when($filters['permission_id'] ?? null, function ($query, $permissionId) {
                return $query->where('permission_id', $permissionId);
            })->when($filters['role'] ?? null, function ($query, $role) {
                return $query->where('role', $role);
            });
    }
}
