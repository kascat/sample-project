<?php

namespace Permissions;

use App\Http\Controllers\Controller;

/**
 * Class PermissionController
 * @package Permissions
 */
class PermissionController extends Controller
{
    use PermissionResponse;

    /** @var PermissionService */
    private PermissionService $permissionService;

    /**
     * PermissionController constructor.
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * @param PermissionRequest $request
     * @return mixed
     */
    public function index(PermissionRequest $request)
    {
        $result = $this->permissionService->index($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param PermissionRequest $request
     * @return mixed
     */
    public function store(PermissionRequest $request)
    {
        $result = $this->permissionService->store($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param Permission $permission
     * @return mixed
     */
    public function show(Permission $permission)
    {
        return $this->response($permission->load(\request('with') ?? [])->toArray());
    }

    /**
     * @param PermissionRequest $request
     * @param Permission $permission
     * @return mixed
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        $result = $this->permissionService->update($permission, $request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param Permission $permission
     * @return mixed
     */
    public function destroy(Permission $permission)
    {
        $result = $this->permissionService->destroy($permission);

        return $this->response($result['response'], $result['status']);
    }

    public function allPermissions (){
        return $this->response(Permission::PERMISSIONS);
    }
}
