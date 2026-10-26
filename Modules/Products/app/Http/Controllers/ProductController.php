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

class ProductController extends Controller
{
    use ApiResponseTrait;

    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    // عرض قائمة المنتجات (مع دعم البحث والتصفية وتقسيم الصفحات)
    public function index(Request $request)
    {
        $products = $this->productService->getAllProducts($request);
       // return $this->successResponse(ProductResource::collection($products), 'تم جلب المنتجات بنجاح');
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
}