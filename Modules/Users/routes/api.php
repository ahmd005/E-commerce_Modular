<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\app\Http\Controllers\UsersController;
use Modules\Users\app\Http\Controllers\AuthController;
use Modules\Users\app\Http\Middleware\TrackActivity;
use Modules\Users\app\Http\Middleware\LoginRateLimit;
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('users', UsersController::class)->names('users');
});



Route::middleware([TrackActivity::class])->prefix('E_commerce/v1/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware(LoginRateLimit::class);
    Route::post('login', [AuthController::class, 'login'])->middleware(LoginRateLimit::class);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
        Route::post('refresh', [AuthController::class, 'refresh']);

});