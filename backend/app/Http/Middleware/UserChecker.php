<?php

namespace App\Http\Middleware;

use Illuminate\Http\Exceptions\HttpResponseException;
use Users\User;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserChecker
{
    public function handle($request, Closure $next)
    {
        if (Auth::user()?->status !== User::STATUS_ACTIVE)
        {
            throw new HttpResponseException(response()->json(['message' => 'Usuário inativo'], Response::HTTP_UNAUTHORIZED));
        }

        return $next($request);
    }
}
