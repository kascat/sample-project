<?php

namespace Users;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    use UserResponse;

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(UserRequest $request): mixed
    {
        $result = $this->userService->login($request->validated());

        return $this->response($result['response']);
    }

    public function logout(UserRequest $request): mixed
    {
        $result = $this->userService->logout($request->bearerToken());

        return $this->response($result['response']);
    }

    public function logoutAll(): mixed
    {
        $result = $this->userService->logoutAll();

        return $this->response($result['response']);
    }

    public function loggedUser(): mixed
    {
        /** @var User $user */
        $user = auth()->user();

        $user->load(User::RELATION_PERMISSION);

        return $this->response($user);
    }

    public function index(UserRequest $request): mixed
    {
        $result = $this->userService->index($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function store(UserRequest $request): mixed
    {
        $result = $this->userService->store($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function show(int $userId): mixed
    {
        $user = UserRepository::findOrFail($userId);

        return $this->response($user->load(\request('with') ?? [])->toArray());
    }

    public function update(UserRequest $request, int $userId): mixed
    {
        $user = UserRepository::findOrFail($userId);

        $result = $this->userService->update($user, $request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function destroy(int $userId): mixed
    {
        $user = UserRepository::findOrFail($userId);

        $result = $this->userService->destroy($user);

        return $this->response($result['response'], $result['status']);
    }

    public function forgotPassword(UserRequest $request): mixed
    {
        $result = $this->userService->forgotPassword($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function resetPassword(UserRequest $request): mixed
    {
        $result = $this->userService->resetPassword($request->validated());

        return $this->response($result['response'], $result['status']);
    }
}
