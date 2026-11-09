<?php
use Illuminate\Support\Facades\Route;
use Modules\Products\App\Http\Controllers\CategoryController;
use Modules\Products\App\Http\Controllers\ProductController;
use Modules\Shared\App\Http\Middleware\AdminMiddleware;

// 1. راوتات عامة ومتاحة للجميع (عرض المنتجات والتصنيفات فقط)
Route::prefix('v1')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
});

// 2. راوتات محمية ولا يمكن الوصول إليها إلا من قِبل الأدمن (إضافة، تعديل، حذف)
Route::middleware(['auth:api', AdminMiddleware::class])->prefix('v1')->group(function () {
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
});




// --- النسخة الثانية الجديدة المحسنة (v2 - مع Redis Caching والسرعة القصوى) ---
Route::prefix('v2')->group(function () {
    Route::get('products', [ProductController::class, 'indexV2']);
    Route::get('categories', [CategoryController::class, 'indexV2']);
});

Route::middleware(['auth:api', AdminMiddleware::class])->prefix('v2')->group(function () {
    Route::post('products', [ProductController::class, 'storeV2']);
    Route::put('products/{product}', [ProductController::class, 'updateV2']);
    Route::delete('products/{product}', [ProductController::class, 'destroyV2']);

    Route::post('categories', [CategoryController::class, 'storeV2']);
    Route::put('categories/{category}', [CategoryController::class, 'updateV2']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroyV2']);
});
