<?php

use Illuminate\Support\Facades\Route;
use Modules\Notifications\app\Http\Controllers\NotificationsController;

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::get('/notifications', [NotificationsController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationsController::class, 'unreadCount']);
    Route::patch('/notifications/{uuid}/read', [NotificationsController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationsController::class, 'markAllAsRead']);
});