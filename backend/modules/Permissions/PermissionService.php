<?php

namespace Permissions;

use Kascat\EasyModule\Core\Service;

/**
 * Class PermissionService
 * @package Permissions
 */
class PermissionService extends Service
{
    /**
     * @param array $filters
     * @return array
     */
    public function index(array $filters)
    {
        $permissionsQuery = PermissionRepository::index($filters);

        return self::buildReturn(
            $permissionsQuery
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
        $permission = Permission::create($data);

        return self::buildReturn($permission);
    }

    /**
     * @param Permission $permission
     * @param array $data
     * @return array
     */
    public function update(Permission $permission, array $data)
    {
        $permission->update($data);

        return self::buildReturn($permission);
    }

    /**
     * @param Permission $permission
     * @return array
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return self::buildReturn();
    }
}
