<?php

namespace App\Http\Middleware;

use App\Http\Services\Service;
use Users\User;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->status !== User::STATUS_ACTIVE)
        {
            throw Service::exception(['message' => 'Usuário não ativo'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
