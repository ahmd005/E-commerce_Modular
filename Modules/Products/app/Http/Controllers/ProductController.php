<?php

namespace Modules\Products\App\Http\Controllers;

use App\Http\Controllers\Controller;
//use App\Traits\ApiResponseTrait;
use Modules\Products\App\Traits\ApiResponseTrait;
use Modules\Products\App\Http\Requests\StoreProductRequest;
use Modules\Products\App\Http\Requests\UpdateProductRequest;
use Modules\Products\Services\ProductService;
use Modules\Products\App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Products\Services\ProductCache;

class ProductController extends Controller
{
    use ApiResponseTrait;

    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
{
    $products = $this->productService->getAllProducts($request);
    
    return ProductResource::collection($products)->additional([
        'status' => true,
        'message' => 'تم جلب المنتجات بنجاح',
    ]);
}

    // إضافة منتج جديد
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct(
            $request->validated(),
            $request->file('image')
        );

        return $this->successResponse(new ProductResource($product), 'تم إنشاء المنتج بنجاح', 201);
    }

    // عرض تفاصيل منتج معين
    public function show($uuid)
    {
        $product = $this->productService->getProductById($uuid);
        return $this->successResponse(new ProductResource($product), 'تم جلب تفاصيل المنتج بنجاح');
    }

    // تعديل منتج
    public function update(UpdateProductRequest $request, $uuid)
    {
        $product = $this->productService->updateProduct(
            $uuid,
            $request->validated(),
            $request->file('image')
        );

        return $this->successResponse(new ProductResource($product), 'تم تحديث المنتج بنجاح');
    }

    // حذف منتج
    public function destroy($uuid)
    {
        $this->productService->deleteProduct($uuid);
        return $this->successResponse(null, 'تم حذف المنتج بنجاح');
    }


    

// --- دوال النسخة الثانية (v2) للمنتجات مع Redis Caching الديناميكي ---
public function indexV2(Request $request)
{
    $params = $request->only(['page', 'per_page', 'search', 'category_uuid']);
    ksort($params);
    $key = 'v2:products:version:' . ProductCache::version() . ':' . md5(json_encode($params));

    $products = Cache::remember($key, now()->addMinutes(30), function () use ($request) {
        return $this->productService->getAllProductsV2($request);
    });

    return ProductResource::collection($products)->additional([
        'status' => true,
        'message' => 'تم جلب المنتجات بنجاح',
    ]);
}

public function storeV2(StoreProductRequest $request)
{
    $product = $this->productService->createProduct($request->validated(), $request->file('image'));
    ProductCache::bump();
    return $this->successResponse(new ProductResource($product), 'تم إنشاء المنتج بنجاح', 201);
}

public function updateV2(UpdateProductRequest $request, $uuid)
{
    $product = $this->productService->updateProduct($uuid, $request->validated(), $request->file('image'));
    ProductCache::bump();
    return $this->successResponse(new ProductResource($product), 'تم تحديث المنتج بنجاح');
}

public function destroyV2($uuid)
{
    $this->productService->deleteProduct($uuid);
    ProductCache::bump();
    return $this->successResponse(null, 'تم حذف المنتج بنجاح');
}
}