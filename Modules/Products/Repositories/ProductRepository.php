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
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // التصفية بالبحث عن الاسم
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // تقسيم الصفحات (Pagination)
        return $query->paginate($request->get('per_page', 10));
    }

    public function findById($id)
    {
        return Product::with('category')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }
}