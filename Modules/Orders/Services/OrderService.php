<?php

namespace Modules\Orders\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Orders\App\Enums\OrderStatus;
use Modules\Orders\Models\CartItem;
use Modules\Orders\Models\Order;
use Modules\Orders\Repositories\CartRepositoryInterface;
use Modules\Orders\Repositories\OrderRepositoryInterface;
use Exception;
use Modules\Shared\Services\InventoryGrpcService;

class OrderService
{
    protected OrderRepositoryInterface $orderRepository;

    protected CartRepositoryInterface $cartRepository;
    protected InventoryGrpcService $inventoryService;


    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $cartRepository,
        InventoryGrpcService $inventoryService

    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->inventoryService = $inventoryService;
    }

    public function getUserOrders(int $userId)
    {
        return $this->orderRepository->paginateUserOrders(
            $userId,
            (int) config('orders.per_page', 15)
        );
    }

    public function getAllOrders()
    {
        return $this->orderRepository->paginateAllOrders(
            (int) config('orders.per_page', 15)
        );
    }

    public function getOrder(string $uuid, int $userId)
    {
        return $this->orderRepository->findUserOrder($uuid, $userId);
    }

    /**
     * إنشاء طلب من محتويات السلة.
     *
     * @throws Exception
     */
    public function placeOrder(int $userId): Order
    {
        $cartItems = $this->cartRepository->getUserCart($userId);

        if ($cartItems->isEmpty()) {
            throw new Exception('Cart is empty', 422);
        }

        foreach ($cartItems as $item) {
            $productUuid = $item->product->uuid;
            $quantity = $item->quantity;
            try {
                $this->inventoryService->reserveStock($productUuid, $quantity);
            } catch (Exception $e) {
                throw new Exception("فشل حجز المخزون للمنتج {$item->product->name}: " . $e->getMessage(), 422);
            }
        }

        return DB::transaction(function () use ($userId, $cartItems): Order {

            $order = $this->orderRepository->create([
                'user_id' => $userId,
                'total_price' => $this->calculateTotal($cartItems),
                'status' => OrderStatus::Pending,
            ]);

            $this->orderRepository->createItems($order, $this->buildOrderItems($cartItems));

            $this->cartRepository->clearCart($userId);

            return $order->load('items.product');
        });
    }

    /**
     * @throws Exception
     */
    public function updateStatus(string $uuid, OrderStatus $newStatus)
    {
        $order = $this->orderRepository->findOrderByUuid($uuid);

        if (! $order->status->canTransitionTo($newStatus)) {
            throw new Exception('Invalid order status transition', 422);
        }

        // إذا تم تحويل حالة الطلب إلى ملغى (Cancelled)، نعيد الكميات إلى المخزون
        if ($newStatus === OrderStatus::Cancelled) {
            foreach ($order->items as $item) {
                try {
                    $productUuid = $item->product->uuid ?? $item->product->sku;
                    $this->inventoryService->releaseStock($productUuid, $item->quantity);
                } catch (Exception $e) {
                    // يمكنك تسجيل الخطأ أو متابعة الإلغاء حسب رغبتك، والأفضل رمي استثناء إذا فشلت الاستعادة
                    throw new Exception("فشل إعادة المخزون للمنتج {$item->product->name}: " . $e->getMessage(), 422);
                }
            }
        }

        return $this->orderRepository->updateStatus($order, $newStatus);
    }

    /**
     * @throws Exception
     */
    public function deleteOrder(string $uuid, int $userId)
    {
        $order = $this->orderRepository->findUserOrder($uuid, $userId);

        if (! $order->status->isDeletable()) {
            throw new Exception('Order cannot be deleted in its current status', 422);
        }

        foreach ($order->items as $item) {
            try {
                $productUuid = $item->product->uuid ?? $item->product->sku;
                $this->inventoryService->releaseStock($productUuid, $item->quantity);
            } catch (Exception $e) {
                throw new Exception("فشل إعادة المخزون أثناء حذف الطلب للمنتج {$item->product->name}: " . $e->getMessage(), 422);
            }
        }

        return $this->orderRepository->delete($order);
    }

    /**
     * @param Collection<int, CartItem> $cartItems
     */
    private function calculateTotal(Collection $cartItems): float
    {
        return round(
            $cartItems->sum(fn(CartItem $item) => $item->quantity * $item->product->price),
            2
        );
    }

    /**
     * تحويل عناصر السلة إلى عناصر طلب مع حفظ سعر المنتج وقت الشراء.
     *
     * @param Collection<int, CartItem> $cartItems
     * @return array<int, array<string, mixed>>
     */
    private function buildOrderItems(Collection $cartItems): array
    {
        return $cartItems
            ->map(fn(CartItem $item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ])
            ->all();
    }
}
