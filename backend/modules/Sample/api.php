<?php
/**
 * Example to Token Ability (Permissions)
 *
 * Request's token has at least one of the listed abilities:
 * middleware(['ability:user.update,user.show'])
 *
 * Request's token has all of the listed abilities:
 * ::middleware(['abilities:user.update,user.show'])
 *
 * More:
 * https://laravel.com/docs/9.x/sanctum#token-abilities
 */

use Illuminate\Support\Facades\Route;
use Sample\Controllers\SampleController;

Route::group([
    'middleware' => ['auth:sanctum', 'user_checker']
], function () {
//    Route::apiResource('samples', SampleController::class);
//    Route::post('test', [SampleController::class, 'test']);
//    Route::middleware(['abilities:sample.update,sample.show'])->post('test-with-permission', [SampleController::class, 'test']);
});
