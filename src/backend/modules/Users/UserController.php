<?php

namespace Users;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(UserRequest $request): mixed
    {
        $result = $this->userService->register($request->validated());

        return response($result['response'], $result['status']);
    }

    public function login(UserRequest $request): mixed
    {
        $result = $this->userService->login($request->validated());

        return response($result['response'], $result['status']);
    }

    public function logout(UserRequest $request): mixed
    {
        $result = $this->userService->logout($request->bearerToken());

        return response($result['response'], $result['status']);
    }

    public function logoutAll(): mixed
    {
        $result = $this->userService->logoutAll();

        return response($result['response'], $result['status']);
    }

    public function loggedUser(): mixed
    {
        $result = $this->userService->loggedUser();

        return response($result['response'], $result['status']);
    }

    public function index(UserRequest $request): mixed
    {
        $result = $this->userService->index($request->validated());

        return response($result['response'], $result['status']);
    }

    public function store(UserRequest $request): mixed
    {
        $result = $this->userService->store($request->validated());

        return response($result['response'], $result['status']);
    }

    public function show(int $id): mixed
    {
        $user = UserRepository::findOrFail($id);

        return response($user->load(\request('with') ?? [])->toArray());
    }

    public function update(UserRequest $request, int $id): mixed
    {
        $user = UserRepository::findOrFail($id);

        $result = $this->userService->update($user, $request->validated());

        return response($result['response'], $result['status']);
    }

    public function destroy(int $id): mixed
    {
        $user = UserRepository::findOrFail($id);

        $result = $this->userService->destroy($user);

        return response($result['response'], $result['status']);
    }

    public function forgotPassword(UserRequest $request): mixed
    {
        $result = $this->userService->forgotPassword($request->validated());

        return response($result['response'], $result['status']);
    }

    public function passwordSetting(UserRequest $request): mixed
    {
        $result = $this->userService->passwordSetting($request->validated());

        return response($result['response'], $result['status']);
    }

    public function status(UserRequest $request, int $id): mixed
    {
        $user = UserRepository::findOrFail($id);

        $result = $this->userService->updateStatus($user, $request->validated());

        return response($result['response'], $result['status']);
    }
}
