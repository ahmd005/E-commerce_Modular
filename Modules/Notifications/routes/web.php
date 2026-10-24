<?php

use Illuminate\Support\Facades\Route;
use Modules\Notifications\App\Http\Controllers\NotificationsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('notifications', NotificationsController::class)->names('notifications');
});
