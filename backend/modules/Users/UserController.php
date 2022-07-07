<?php

namespace Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Validation\Rules\Password as RulesPassword;

/**
 * Class UserController
 * @package Users
 */
class UserController extends Controller
{
    use UserResponse;

    private UserService $userService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserRequest $request)
    {
        $result = $this->userService->login($request->validated());

        return $this->response($result['response']);
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(UserRequest $request)
    {
        $result = $this->userService->logout($request->bearerToken());

        return $this->response($result['response']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutAll()
    {
        $result = $this->userService->logoutAll();

        return $this->response($result['response']);
    }

    /**
     * @return mixed
     */
    public function loggedUser()
    {
        $user = auth()->user();
        $user->permission;

        return $this->response($user);
    }

    /**
     * @param UserRequest $request
     * @return mixed
     */
    public function index(UserRequest $request)
    {
        $result = $this->userService->index($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param UserRequest $request
     * @return mixed
     */
    public function store(UserRequest $request)
    {
        $result = $this->userService->store($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function show(User $user)
    {
        return $this->response($user->load(\request('with') ?? [])->toArray());
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return mixed
     */
    public function update(UserRequest $request, User $user)
    {
        $result = $this->userService->update($user, $request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function destroy(User $user)
    {
        $result = $this->userService->destroy($user);

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param UserRequest $request
     * @return mixed
     */
    public function forgotPassword(UserRequest $request)
    {
        $result = $this->userService->forgotPassword($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param UserRequest $request
     * @return mixed
     */
    public function resetPassword(UserRequest $request)
    {
        $result = $this->userService->resetPassword($request->validated());

        return $this->response($result['response'], $result['status']);
    }
}
