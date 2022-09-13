<?php

namespace Users;

/**
 * Trait UserResponse
 * @package Users
 */
trait UserResponse
{
    /**
     * @param array $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseToLogin($data = [], int $statusCode = 200)
    {
        return response()->json($data, $statusCode);
    }
}
