<?php

namespace Users;

use App\Utils\Formatter;
use Illuminate\Validation\Rule;
use Kascat\EasyModule\Core\Request;
use Illuminate\Validation\Rules\Password;
use Permissions\Permission;
use Users\Enums\UserRoleEnum;

class UserRequest extends Request
{
    public function validateToRegister()
    {
        return [
            User::NAME => ['string', 'required'],
            User::EMAIL => [
                'string',
                'required',
                Rule::unique(User::TABLE, User::EMAIL),
            ],
            User::PHONE => ['nullable', 'string'],
            User::PASSWORD => [
                'required',
                'string',
                Password::min(4),
                'confirmed'
            ],
        ];
    }

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
            User::PHONE => 'nullable|string',
            User::NAME => 'required|string',
            User::ROLE => [
                'required',
                Rule::enum(UserRoleEnum::class),
            ],
            User::PASSWORD => 'nullable|string|min:4',
            User::PERMISSION_ID => ['required', 'int', Rule::exists(Permission::TABLE, Permission::ID)],
        ];
    }

    public function validateToUpdate()
    {
        $pendingPassword = User::STATUS_PENDING_PASSWORD;
        $active = User::STATUS_ACTIVE;
        $blocked = User::STATUS_BLOCKED;

        return [
            User::EMAIL => [
                'string',
                'email',
                Rule::unique(User::TABLE, User::EMAIL)->ignore($this->route('user')),
            ],
            User::NAME => 'required|string',
            User::PHONE => 'nullable|string',
            User::ROLE => [Rule::enum(UserRoleEnum::class)],
            User::STATUS => "in:$pendingPassword,$active,$blocked",
            User::PASSWORD => 'nullable|string|min:4',
            User::PERMISSION_ID => ['int', Rule::exists(Permission::TABLE, Permission::ID)],
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
                Password::min(4),
                'confirmed',
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $data = [];

        if ($this->input(User::PHONE)) {
            $data[User::PHONE] = Formatter::onlyNumbers($this->input(User::PHONE));
        }

        $this->merge($data);
    }
}
