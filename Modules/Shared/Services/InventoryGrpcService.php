<?php

namespace Modules\Products\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class InventoryGrpcService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.inventory.url', env('INVENTORY_SERVICE_URL', 'http://localhost:50051'));
    }

    /**
     * مطابقة لـ CreateProduct في ملف الـ Proto
     */
    public function createProduct(array $data)
    {
        try {
            $response = Http::post("{$this->baseUrl}/inventory.InventoryService/CreateProduct", [
                'name' => $data['name'],
                'sku' => $data['sku'] ?? 'SKU-' . uniqid(),
                'initial_quantity' => $data['initial_quantity'] ?? 0,
            ]);

            if ($response->failed()) {
                throw new Exception($response->json('message') ?? 'فشل إنشاء المنتج في خدمة المستودع');
            }

            return $response->json();
        } catch (Exception $e) {
            throw new Exception("خطأ في الاتصال بخدمة المستودع: " . $e->getMessage());
        }
    }

    /**
     * مطابقة لـ UpdateStock في ملف الـ Proto
     */
    public function updateStock(string $productId, int $delta)
    {
        try {
            Http::post("{$this->baseUrl}/inventory.InventoryService/UpdateStock", [
                'product_id' => $productId,
                'delta' => $delta,
            ]);
        } catch (Exception $e) {
            // تسجيل الخطأ أو تجاهله حسب رغبتك
        }
    }

    /**
     * مطابقة لـ DeleteProduct في ملف الـ Proto
     */
    public function deleteProduct(string $productId)
    {
        try {
            Http::post("{$this->baseUrl}/inventory.InventoryService/DeleteProduct", [
                'product_id' => $productId,
            ]);
        } catch (Exception $e) {
            // تسجيل الخطأ
        }
    }

    /**
     * مطابقة لـ ReserveStock (تُستخدم عند الطلبات)
     */
    public function reserveStock(string $productId, int $quantity)
    {
        try {
            $response = Http::post("{$this->baseUrl}/inventory.InventoryService/ReserveStock", [
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);

            if ($response->failed()) {
                throw new Exception($response->json('message') ?? 'الكمية غير كافية أو فشل الحجز');
            }

            return $response->json();
        } catch (Exception $e) {
            throw new Exception("خطأ في حجز المخزون: " . $e->getMessage());
        }
    }
}