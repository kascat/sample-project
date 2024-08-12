<?php

namespace Users;

use Illuminate\Http\JsonResponse;
use Kascat\EasyModule\Core\Response;

/**
 * Trait UserResponse
 */
trait UserResponse
{
    use Response;

    public function responseToLogin(array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json($data, $statusCode);
    }
}
