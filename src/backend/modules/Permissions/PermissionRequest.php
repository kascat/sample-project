<?php

namespace Permissions;

use Kascat\EasyModule\Core\Request;

/**
 * Class PermissionRequest
 */
class PermissionRequest extends Request
{
    public function validateToIndex(): array
    {
        return [
            Permission::NAME => 'nullable|string',
        ];
    }

    public function validateToStore(): array
    {
        return [
            Permission::NAME => ['required', 'min:2'],
            Permission::ABILITIES => ['array', 'nullable'],
            Permission::DEFAULT => ['boolean', 'required'],
        ];
    }

    public function validateToUpdate(): array
    {
        return [
            Permission::NAME => ['required', 'min:2'],
            Permission::ABILITIES => ['array', 'nullable'],
            Permission::DEFAULT => ['boolean', 'required'],
        ];
    }
}
