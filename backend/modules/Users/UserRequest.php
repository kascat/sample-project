<?php

namespace Users;

use App\Http\Requests\Request;
use Illuminate\Validation\Rules\Password;

/**
 * Class UserRequest
 * @package Users
 */
class UserRequest extends Request
{
    /**
     * @return string[]
     */
    public function validateToLogin()
    {
        return [
            'email'    => 'required',
            'password' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function validateToIndex()
    {
        return [
            'name'          => '',
            'permission_id' => '',
            'role'          => '',
        ];
    }

    /**
     * @return string[]
     */
    public function validateToStore()
    {
        return [
            'email'         => 'required|string|email|unique:users',
            'name'          => 'required',
            'role'          => 'required',
            'permission_id' => 'required',
            'password'      => 'nullable|string|min:6',
            'login_time'    => 'nullable|integer',
            'expires_in'    => 'nullable|date_format:d/m/Y H:i',
        ];
    }

    /**
     * @return string[]
     */
    public function validateToUpdate()
    {
        $pendingPassword = User::STATUS_PENDING_PASSWORD;
        $active          = User::STATUS_ACTIVE;
        $blocked         = User::STATUS_BLOCKED;
        $blockedByTime   = User::STATUS_BLOCKED_BY_TIME;

        return [
            'email'         => 'string|email',
            'name'          => '',
            'role'          => '',
            'permission_id' => '',
            'password'      => 'nullable|string|min:6',
            'login_time'    => 'nullable|integer',
            'expires_in'    => 'nullable|date_format:d/m/Y H:i',
            'status'        => "in:$pendingPassword,$active,$blocked,$blockedByTime",
        ];
    }

    /**
     * @return string[]
     */
    public function validateToForgotPassword()
    {
        return [
            'email' => 'required|email',
        ];
    }

    /**
     * @return array[]
     */
    public function validateToResetPassword()
    {
        return [
            'password' => [
                'required',
                'string',
                Password::min(6)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed'
            ],
        ];
    }
}
