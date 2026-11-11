<?php

namespace Modules\Products\Services;

use Modules\Products\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;
use Modules\Products\Events\ProductCreatedEvent;
use Modules\Shared\Services\InventoryGrpcService;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;
    protected InventoryGrpcService $inventoryGrpcService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
         InventoryGrpcService $inventoryGrpcService
         )
    {
        $this->productRepository = $productRepository;
        $this->inventoryGrpcService = $inventoryGrpcService;
    }

    // public function getAllProducts(Request $request)
    // {
    //     return $this->productRepository->getAll($request);
    // }


    public function getAllProducts(Request $request)
    {
        // 1. جلب استعلام المنتجات محلياً مع الـ Pagination مباشرة
        $paginatedProducts = Product::with('category')->latest()
            ->when($request->has('category_uuid'), function ($q) use ($request) {
                $q->whereHas('category', function ($sub) use ($request) {
                    $sub->where('uuid', $request->category_uuid);
                });
            })
            ->when($request->has('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->paginate($request->get('per_page', 10));

        // 2. جلب المخزون والكميات المحدثة من خدمة رفيقك عبر الـ gRPC
        try {
            $inventoryResponse = $this->inventoryGrpcService->listProducts();
            
            // التأكد من استخراج المصفوفة بشكل صحيح سواء كانت داخل مفتاح data أو مصفوفة مباشرة
            $inventoryList = is_array($inventoryResponse) 
                ? ($inventoryResponse['data'] ?? $inventoryResponse) 
                : [];

            $inventoryProducts = collect($inventoryList);

            // 3. التعديل على العناصر داخل الـ Paginator ودمج الكميات بأمان
            $paginatedProducts->getCollection()->transform(function ($product) use ($inventoryProducts) {
                // البحث المرن عن المنتج في المستودع عبر الـ uuid أو id أو sku
                $inventoryItem = $inventoryProducts->first(function ($item) use ($product) {
                    return (isset($item['product_id']) && $item['product_id'] == $product->uuid) ||
                           (isset($item['uuid']) && $item['uuid'] == $product->uuid) ||
                           (isset($item['sku']) && $item['sku'] == $product->sku) ||
                           (isset($item['id']) && $item['id'] == $product->id);
                });
                
                // تعيين الكمية (إذا وجدت نأخذها، وإلا نضع 0 كقيمة افتراضية)
                $product->quantity = $inventoryItem['quantity'] ?? $inventoryItem['stock'] ?? 0;

                return $product;
            });
        } catch (\Exception $e) {
            // في حال توقف سيرفر رفيقك مؤقتاً، نضع الكمية 0 كبديل آمن
            $paginatedProducts->getCollection()->transform(function ($product) {
                $product->quantity = 0;
                return $product;
            });
        }

        return $paginatedProducts;
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

    // أضف هذا الجزء لربط إنشاء المنتج مع خدمة المستودع الخارجية
    try {
        $this->inventoryGrpcService->createProduct([
            'name' => $product->name,
            'sku' => $product->uuid,
           'initial_quantity' => (int) request('initial_quantity', 0),
        ]);
    } catch (\Exception $e) {
        // في حال فشل الاتصال بخدمة المستودع، يمكنك التعامل مع الخطأ هنا
    }

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

       // تحديث المنتج محلياً
        $updatedProduct = $this->productRepository->update($uuid, $data);

       // إبلاغ خدمة Node.js الخارجية بالتحديث
try {
   
    $delta = (int) ($data['delta'] ?? request('delta', request('quantity', 0)));

    // طباعة أو إجبار الإرسال حتى لو كانت القيمة موجبة أو سالبة
    if ($delta !== 0) {
        $this->inventoryGrpcService->updateStock($product->uuid, $delta);
    }
} catch (\Exception $e) {
    // تسجيل الخطأ إن وجد
}

        return $updatedProduct;

     //   return $this->productRepository->update($uuid, $data);
    }

    public function deleteProduct($uuid)
    {
        $product = $this->productRepository->findById($uuid);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // أضف هذا السطر لإبلاغ خدمة المستودع بحذف المنتج
        try {
            $this->inventoryGrpcService->deleteProduct($product->uuid);
        } catch (\Exception $e) {
            // تجاهل أو سجل الخطأ
        }

        return $this->productRepository->delete($uuid);
    }






    public function getAllProductsV2(Request $request)
{
    $perPage = min((int) $request->get('per_page', 10), 100);

    $query = Product::with('category')->orderBy('id', 'desc');

    if ($request->filled('category_uuid')) {
        $categoryId = Category::where('uuid', $request->category_uuid)->value('id');
        $query->where('category_id', $categoryId ?? 0);
    }

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    return $query->paginate($perPage);
}


}