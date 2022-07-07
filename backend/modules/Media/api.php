<?php

use Illuminate\Support\Facades\Route;
use Media\MediaController;

Route::group([
    'middleware' => ['auth:sanctum', 'user_checker']
], function () {
    Route::apiResource('media', MediaController::class);
});
