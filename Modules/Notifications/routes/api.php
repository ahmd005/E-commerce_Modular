<?php

use Illuminate\Support\Facades\Route;
use Modules\Notifications\App\Http\Controllers\NotificationsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('notifications', NotificationsController::class)->names('notifications');
});
