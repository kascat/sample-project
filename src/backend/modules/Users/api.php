<?php

use Illuminate\Support\Facades\Route;
use Permissions\Enums\AbilitiesEnum;
use Users\UserController;

Route::group([
    'middleware' => ['api']
], function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('forgot-password', [UserController::class, 'forgotPassword']);
    Route::post('password-setting', [UserController::class, 'resetPassword'])
        ->middleware(['auth:user', AbilitiesEnum::requireAllAbilities([AbilitiesEnum::PASSWORD_SETTING])]);
});

Route::group([
    'middleware' => ['api', 'auth:user', 'user_checker']
], function () {
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('logout-all', [UserController::class, 'logoutAll']);
    Route::get('users/logged-user', [UserController::class, 'loggedUser']);
    Route::apiResource('users', UserController::class)
        ->middleware(AbilitiesEnum::requireAllAbilities([AbilitiesEnum::USERS]));
});
