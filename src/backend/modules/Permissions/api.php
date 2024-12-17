<?php

use Illuminate\Support\Facades\Route;
use Permissions\Enums\AbilitiesEnum;
use Permissions\PermissionController;

Route::group([
    'middleware' => ['api', 'auth:user', 'user_checker']
], function () {
    Route::get('abilities', [PermissionController::class, 'abilities']);
    Route::apiResource('permissions', PermissionController::class)
        ->middleware(AbilitiesEnum::requireAllAbilities([AbilitiesEnum::PERMISSIONS]));
});
