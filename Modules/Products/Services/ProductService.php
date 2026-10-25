<?php

namespace Modules\Products\Services;

use Modules\Products\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function getProductById($id)
    {
        return $this->productRepository->findById($id);
    }

    public function createProduct(array $data, $imageFile = null)
    {
        if ($imageFile) {
            $data['image'] = $imageFile->store('products', 'public');
        }

        return $this->productRepository->create($data);
    }

    public function updateProduct($id, array $data, $imageFile = null)
    {
        $product = $this->productRepository->findById($id);

        if ($imageFile) {
            // حذف الصورة القديمة إذا وجدت
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $imageFile->store('products', 'public');
        }

        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct($id)
    {
        $product = $this->productRepository->findById($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        return $this->productRepository->delete($id);
    }
}