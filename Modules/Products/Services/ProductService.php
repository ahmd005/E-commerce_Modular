<?php

namespace Modules\Products\Services;

use Modules\Products\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Products\Models\Category;
use Modules\Products\Events\ProductCreatedEvent;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts(Request $request)
    {
        return $this->productRepository->getAll($request);
    }

    public function getProductById($uuid)
    {
        return $this->productRepository->findById($uuid);
    }


public function createProduct(array $data, $imageFile = null)
{
    // تحويل الـ category_uuid القادم من الطلب إلى category_id حقيقي لقاعدة البيانات
    if (isset($data['category_uuid'])) {
        $category = Category::where('uuid', $data['category_uuid'])->firstOrFail();
        $data['category_id'] = $category->id;
        unset($data['category_uuid']); // إزالة الـ uuid لكي لا يحدث خطأ في الحفظ
    }

    if ($imageFile) {
        $data['image'] = $imageFile->store('products', 'public');
    }


    $product = $this->productRepository->create($data);

    event(new ProductCreatedEvent($product));

    return $product;
}

    public function updateProduct($uuid, array $data, $imageFile = null)
    {
        $product = $this->productRepository->findById($uuid);

        if ($imageFile) {
            // حذف الصورة القديمة إذا وجدت
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $imageFile->store('products', 'public');
        }

        return $this->productRepository->update($uuid, $data);
    }

    public function deleteProduct($uuid)
    {
        $product = $this->productRepository->findById($uuid);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        return $this->productRepository->delete($uuid);
    }
}