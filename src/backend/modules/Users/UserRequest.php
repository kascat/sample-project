<?php

namespace Users;

use App\Utils\Formatter;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Kascat\EasyModule\Core\Request;
use Illuminate\Validation\Rules\Password;
use Permissions\Permission;
use Users\Enums\UserRoleEnum;
use Users\Enums\UserStatusEnum;

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
            User::EMAIL => ['required', 'email'],
            User::PASSWORD => ['required', 'string'],
        ];
    }

    public function validateToIndex()
    {
        return [
            User::NAME => ['string', 'nullable'],
            User::EMAIL => ['string', 'nullable'],
            User::ROLE => ['string', 'nullable'],
            User::PERMISSION_ID => ['int', 'nullable'],
        ];
    }

    public function validateToStore()
    {
        return [
            User::NAME => ['required', 'string'],
            User::EMAIL => [
                'required',
                'string',
                'email',
                Rule::unique(User::TABLE, User::EMAIL),
            ],
            User::PHONE => ['nullable', 'string'],
            User::ROLE => [
                'required',
                Rule::enum(UserRoleEnum::class),
            ],
            User::PASSWORD => ['nullable', 'string', 'min:4'],
            User::LOGIN_TIME => ['nullable', 'integer'],
            User::EXPIRES_IN => ['nullable', 'date'],
            User::PERMISSION_ID => [
                'required',
                'int',
                Rule::exists(Permission::TABLE, Permission::ID)
            ],
        ];
    }

    public function validateToUpdate()
    {
        return [
            User::NAME => ['required', 'string'],
            User::EMAIL => [
                'required',
                'string',
                'email',
                Rule::unique(User::TABLE, User::EMAIL)->ignore($this->route('user')),
            ],
            User::PHONE => ['nullable', 'string'],
            User::ROLE => [
                'required',
                Rule::enum(UserRoleEnum::class),
            ],
            User::PASSWORD => ['nullable', 'string', 'min:4'],
            User::LOGIN_TIME => ['nullable', 'integer'],
            User::EXPIRES_IN => ['nullable', 'date'],
            User::PERMISSION_ID => [
                'required',
                'int',
                Rule::exists(Permission::TABLE, Permission::ID)
            ],
        ];
    }

    public function validateToStatus()
    {
        return [
            User::STATUS => [
                'required',
                Rule::enum(UserStatusEnum::class),
            ],
        ];
    }

    public function validateToForgotPassword()
    {
        return [
            User::EMAIL => ['required', 'email'],
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

        if ($this->input(User::EXPIRES_IN)) {
            $data[User::EXPIRES_IN] = Carbon::createFromFormat(
                'd/m/Y H:i',
                $this->input(User::EXPIRES_IN)
            );
        }

        $this->merge($data);
    }
}
