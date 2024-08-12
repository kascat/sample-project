<?php

namespace Users;

use App\Utils\Formatter;
use Illuminate\Validation\Rule;
use Kascat\EasyModule\Core\Request;
use Illuminate\Validation\Rules\Password;

class UserRequest extends Request
{
    public function validateToLogin()
    {
        return [
            User::EMAIL => 'required',
            User::PASSWORD => 'required',
        ];
    }

    public function validateToIndex()
    {
        return [
            User::NAME => 'string|nullable',
            User::EMAIL => 'string|nullable',
            User::ROLE => 'string|nullable',
            User::PERMISSION_ID => 'int|nullable',
        ];
    }

    public function validateToStore()
    {
        return [
            User::EMAIL => [
                'required',
                'string',
                'email',
                Rule::unique(User::TABLE, User::EMAIL),
            ],
            User::NAME => 'required',
            User::ROLE => 'required',
            User::PERMISSION_ID => 'required',
            User::PASSWORD => 'nullable|string|min:6',
            User::LOGIN_TIME => 'nullable|integer',
            User::EXPIRES_IN => 'nullable|date_format:d/m/Y H:i',
        ];
    }

    public function validateToUpdate()
    {
        $pendingPassword = User::STATUS_PENDING_PASSWORD;
        $active          = User::STATUS_ACTIVE;
        $blocked         = User::STATUS_BLOCKED;
        $blockedByTime   = User::STATUS_BLOCKED_BY_TIME;

        return [
            User::EMAIL => [
                'string',
                'email',
                Rule::unique(User::TABLE, User::EMAIL)->ignore($this->route('user')),
            ],
            User::NAME => '',
            User::ROLE => '',
            User::STATUS => "in:$pendingPassword,$active,$blocked,$blockedByTime",
            User::PERMISSION_ID => '',
            User::PASSWORD => 'nullable|string|min:6',
            User::LOGIN_TIME => 'nullable|integer',
            User::EXPIRES_IN => 'nullable|date_format:d/m/Y H:i',
        ];
    }

    public function validateToForgotPassword()
    {
        return [
            User::EMAIL => 'required|email',
        ];
    }

    public function validateToResetPassword()
    {
        return [
            User::PASSWORD => [
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

    protected function prepareForValidation()
    {
        // $this->merge([
        //     User::EXAMPLE => Formatter::onlyNumbers($this->input(User::EXAMPLE)),
        // ]);
    }
}
