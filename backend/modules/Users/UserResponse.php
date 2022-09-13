<?php

namespace Users;

use Kascat\EasyModule\Core\Response;

/**
 * Trait UserResponse
 * @package Users
 */
trait UserResponse
{
    use Response;

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
