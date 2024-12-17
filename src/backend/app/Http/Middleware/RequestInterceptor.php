<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class RequestInterceptor
 * @package App\Http\Middleware
 */
class RequestInterceptor
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->perPage = $request->perPage == '0' ? 9999999 : $request->perPage;

        return $next($request);
    }
}
