<?php

namespace Permissions;

use App\Http\Controllers\Controller;
use Permissions\Enums\AbilitiesEnum;

/**
 * Class PermissionController
 */
class PermissionController extends Controller
{
    use PermissionResponse;

    public function __construct(private readonly PermissionService $permissionService)
    {
        //
    }

    public function index(PermissionRequest $request): mixed
    {
        $result = $this->permissionService->index($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function store(PermissionRequest $request): mixed
    {
        $result = $this->permissionService->store($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function show(Permission $permission): mixed
    {
        $result = $this->permissionService->show($permission);

        return $this->response($result['response'], $result['status']);
    }

    public function update(PermissionRequest $request, Permission $permission): mixed
    {
        $result = $this->permissionService->update($permission, $request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function destroy(Permission $permission): mixed
    {
        $result = $this->permissionService->destroy($permission);

        return $this->response($result['response'], $result['status']);
    }

    public function allPermissions (): mixed
    {
        return $this->response(AbilitiesEnum::availableAbilities());
    }
}
