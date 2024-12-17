<?php

namespace Permissions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Kascat\EasyModule\Core\Service;
use Throwable;

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
        DB::beginTransaction();

        try {
            if (true === ($data[Permission::DEFAULT] ?? null)) {
                Permission::query()->update([Permission::DEFAULT => false]);
            }

            $permission = Permission::query()->create($data);

            DB::commit();

            return self::buildReturn($permission);
        } catch (Throwable) {
            DB::rollBack();

            return self::buildReturn([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(Permission $permission, array $data): array
    {
        DB::beginTransaction();

        try {
            if (true === ($data[Permission::DEFAULT] ?? null)) {
                Permission::query()
                    ->where(Permission::ID, '!=', $permission->id)
                    ->update([Permission::DEFAULT => false]);
            }

            $permission->update($data);

            DB::commit();

            return self::buildReturn($permission);
        } catch (Throwable) {
            DB::rollBack();

            return self::buildReturn([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(Permission $permission): array
    {
        $permission->delete();

        return self::buildReturn();
    }
}
