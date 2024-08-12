<?php

namespace Permissions;

use Kascat\EasyModule\Core\Service;

/**
 * Class PermissionService
 */
class PermissionService extends Service
{
    public function index(array $filters): array
    {
        $permissionsQuery = PermissionRepository::defautFiltersQuery($filters);

        return self::buildReturn(
            $permissionsQuery
                ->with(\request(self::WITH_RELATIONSHIP) ?? [])
                ->paginate(\request(self::PER_PAGE))
        );
    }

    public function show(Permission $permission): array
    {
        return self::buildReturn(
            $permission
                ->load(\request(self::WITH_RELATIONSHIP) ?? [])
                ->toArray()
        );
    }

    public function store(array $data): array
    {
        $permission = Permission::query()->create($data);

        return self::buildReturn($permission);
    }

    public function update(Permission $permission, array $data): array
    {
        $permission->update($data);

        return self::buildReturn($permission);
    }

    public function destroy(Permission $permission): array
    {
        $permission->delete();

        return self::buildReturn();
    }
}
