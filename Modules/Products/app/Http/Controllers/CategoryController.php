<?php

namespace Modules\Products\App\Http\Controllers;

use App\Http\Controllers\Controller;
//use App\Traits\ApiResponseTrait;
use Modules\Products\App\Traits\ApiResponseTrait;
use Modules\Products\App\Http\Requests\StoreCategoryRequest;
use Modules\Products\App\Http\Requests\UpdateCategoryRequest;
use Modules\Products\Services\CategoryService;
use Modules\Products\App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Cache;
use Modules\Products\Services\ProductCache;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return $this->successResponse(CategoryResource::collection($categories), 'تم جلب التصنيفات بنجاح');
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());
        return $this->successResponse(new CategoryResource($category), 'تم إنشاء التصنيف بنجاح', 201);
        
    }

    public function show($uuid)
    {
        $category = $this->categoryService->getCategoryById($uuid);
        return $this->successResponse(new CategoryResource($category), 'تم جلب تفاصيل التصنيف بنجاح');
    }

    public function update(UpdateCategoryRequest $request, $uuid)
    {
        $category = $this->categoryService->updateCategory($uuid, $request->validated());
        return $this->successResponse(new CategoryResource($category), 'تم تحديث التصنيف بنجاح');
        

    }

    public function destroy($uuid)
    {
        $this->categoryService->deleteCategory($uuid);
        return $this->successResponse(null, 'تم حذف التصنيف بنجاح');
    }






  

// --- دوال النسخة الثانية (v2) للتصنيفات مع Redis ---

public function indexV2()
{
    // تخزين التصنيفات في Redis لمدة 60 دقيقة
    $categories = Cache::remember('v2:categories:all', now()->addMinutes(60), function () {
        return $this->categoryService->getAllCategories();
    });

    return $this->successResponse(CategoryResource::collection($categories), 'تم جلب التصنيفات بنجاح (v2 Cached)');
}


public function storeV2(StoreCategoryRequest $request)
{
    $category = $this->categoryService->createCategory($request->validated());
    
    // إبطال كاش التصنيفات فوراً
    Cache::forget('v2:categories:all');

    return $this->successResponse(new CategoryResource($category), 'تم إنشاء التصنيف بنجاح', 201);
}

public function updateV2(UpdateCategoryRequest $request, $uuid)
{
    $category = $this->categoryService->updateCategory($uuid, $request->validated());
    
    // إبطال الكاش
    Cache::forget('v2:categories:all');
    Cache::forget("v2:category:{$uuid}");
    ProductCache::bump();

    return $this->successResponse(new CategoryResource($category), 'تم تحديث التصنيف بنجاح');
}

public function destroyV2($uuid)
{
    $this->categoryService->deleteCategory($uuid);
    
    // إبطال الكاش
    Cache::forget('v2:categories:all');
    Cache::forget("v2:category:{$uuid}");
    ProductCache::bump();

    return $this->successResponse(null, 'تم حذف التصنيف بنجاح');
}


}