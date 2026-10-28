<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\App\Http\Controllers\CartController;
use Modules\Orders\App\Http\Controllers\OrderController;
use Modules\Shared\App\Http\Middleware\AdminMiddleware;

    Route::prefix('cart')->controller(CartController::class)->group(function () {
        Route::get('/v1', 'index');
        Route::post('/v1', 'store');
        Route::put('/v1/{uuid}', 'update')->whereUuid('uuid');
        Route::delete('/v1/{uuid}', 'destroy')->whereUuid('uuid');
        Route::delete('/v1/clear', 'clear');
    });
    Route::prefix('orders')->controller(OrderController::class)->group(function () {
        Route::get('/v1', 'index');
        Route::post('/v1', 'store');
        Route::get('/v1/{uuid}', 'show')->whereUuid('uuid');
        Route::put('/v1/{uuid}', 'update')->whereUuid('uuid')->middleware(AdminMiddleware::class);
        Route::delete('/v1/{uuid}', 'destroy')->whereUuid('uuid');
    });
