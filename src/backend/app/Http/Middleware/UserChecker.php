<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Users\Enums\UserStatusEnum;
use Users\User;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserChecker
{
    public function handle($request, Closure $next)
    {
        /** @var User|null $loggedUser */
        $loggedUser = Auth::user();

        if ($loggedUser?->status !== UserStatusEnum::ACTIVE) {
            throw new HttpResponseException(response()->json(['message' => __('inactive_user')], Response::HTTP_UNAUTHORIZED));
        }

        if (!!$loggedUser->expires_in && Carbon::now() > $loggedUser->expires_in) {
            throw new HttpResponseException(response()->json(['message' => __('expired_access')], Response::HTTP_UNAUTHORIZED));
        }

        return $next($request);
    }
}
