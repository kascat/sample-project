<?php

use Illuminate\Support\Facades\Route;
use Permissions\Enums\AbilitiesEnum;
use Permissions\PermissionController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'user_checker']
], function () {
    Route::get('permissions/all-permission', [PermissionController::class, 'allPermissions']);
    Route::apiResource('permissions', PermissionController::class)
        ->middleware(AbilitiesEnum::requireAllAbilities([AbilitiesEnum::PERMISSIONS]));
});
