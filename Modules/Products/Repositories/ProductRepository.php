<?php

namespace Modules\Products\Repositories;

use Modules\Products\Models\Product;
use Illuminate\Http\Request;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll(Request $request)
    {
        $query = Product::with('category');

        // ميزة إضافية: التصفية حسب التصنيف (Category Filter)
        if ($request->has('category_uuid')) {
        $query->whereHas('category', function ($q) use ($request) {
            $q->where('uuid', $request->category_uuid);
        });
    }

        // التصفية بالبحث عن الاسم
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // تقسيم الصفحات (Pagination)
        return $query->paginate($request->get('per_page', 10));
    }


    public function findById($uuid)
    {
        return Product::with('category')->where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

   
    public function update($uuid, array $data)
    {
        $product = Product::where('uuid', $uuid)->firstOrFail();
        $product->update($data);
        return $product;
    }


    public function delete($uuid)
    {
        $product = Product::where('uuid', $uuid)->firstOrFail();
        return $product->delete();
    }
}