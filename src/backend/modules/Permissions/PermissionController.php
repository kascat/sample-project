<?php

namespace Permissions;

use App\Http\Controllers\Controller;
use Permissions\Enums\AbilitiesEnum;

/**
 * Class PermissionController
 */
class PermissionController extends Controller
{
    public function __construct(private readonly PermissionService $permissionService)
    {
        //
    }

    public function index(PermissionRequest $request): mixed
    {
        $result = $this->permissionService->index($request->validated());

        return response($result['response'], $result['status']);
    }

    public function store(PermissionRequest $request): mixed
    {
        $result = $this->permissionService->store($request->validated());

        return response($result['response'], $result['status']);
    }

    public function show(Permission $permission): mixed
    {
        $result = $this->permissionService->show($permission);

        return response($result['response'], $result['status']);
    }

    public function update(PermissionRequest $request, Permission $permission): mixed
    {
        $result = $this->permissionService->update($permission, $request->validated());

        return response($result['response'], $result['status']);
    }

    public function destroy(Permission $permission): mixed
    {
        $result = $this->permissionService->destroy($permission);

        return response($result['response'], $result['status']);
    }

    public function abilities (): mixed
    {
        return response(AbilitiesEnum::availableAbilities());
    }
}
