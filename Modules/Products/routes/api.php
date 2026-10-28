<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\App\Http\Controllers\CategoryController;
use Modules\Products\App\Http\Controllers\ProductController;

 Route::middleware(['auth:api'])->prefix('v1')->group(function () {
//منتجات
    Route::apiResource('products',ProductController::class);
//تصنيفات
    Route::apiResource('categories', CategoryController::class);
});
