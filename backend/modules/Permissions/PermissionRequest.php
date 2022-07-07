<?php

namespace Permissions;

use App\Http\Requests\Request;

/**
 * Class PermissionRequest
 * @package Permissions
 */
class PermissionRequest extends Request
{
    /**
     * @return string[]
     */
    public function validateToIndex()
    {
        return [
            'name' => '',
        ];
    }

    /**
     * @return string[]
     */
    public function validateToStore()
    {
        return [
            'name'      => 'required|min:2',
            'abilities' => '',
        ];
    }

    /**
     * @return string[]
     */
    public function validateToUpdate()
    {
        return [
            'name'      => 'min:2',
            'abilities' => '',
        ];
    }
}
