<?php

namespace Modules\Orders\Services;

use Exception;
use Modules\Orders\Models\CartItem;
use Modules\Orders\Repositories\CartRepositoryInterface;
use Modules\Products\Models\Product;
use Modules\Shared\Services\InventoryGrpcService;

class CartService
{
    protected CartRepositoryInterface $cartRepository;
    protected InventoryGrpcService $inventoryService;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        InventoryGrpcService $inventoryService
    ) {
        $this->cartRepository = $cartRepository;
        $this->inventoryService = $inventoryService;
    }

    public function getCart(int $userId)
    {
        $items = $this->cartRepository->getUserCart($userId);

        $total = $items->sum(
            fn(CartItem $item) => $item->quantity * ($item->product->price ?? 0)
        );

        return [
            'items' => $items,
            'cart_total' => $total,
        ];
    }

    public function addItem(int $userId, array $data)
    {
        $product = Product::where('uuid', $data['product_uuid'])->firstOrFail();
        $requestedQuantity = $data['quantity'];

        $isAvailable = $this->inventoryService->checkStock($product->uuid, $requestedQuantity);

        if (!$isAvailable) {
            throw new Exception('عذراً، الكمية المطلوبة غير متوفرة حالياً في المخزون.', 422);
        }

        $existingItem = $this->cartRepository->findExistingItem($userId, $product->id);

        if ($existingItem) {
            $totalQuantity = $existingItem->quantity + $requestedQuantity;
            $isTotalAvailable = $this->inventoryService->checkStock($product->uuid, $totalQuantity);

            if (!$isTotalAvailable) {
                throw new Exception('عذراً، الكمية الإجمالية المطلوبة تجاوزت المخزون المتاح.', 422);
            }

            return $this->cartRepository->incrementQuantity($existingItem, $requestedQuantity);
        }

        return $this->cartRepository->createItem([
            'user_id' => $userId,
            'product_id' => $product->id,
            'quantity' => $requestedQuantity,
        ]);
    }

    public function updateItemQuantity($cartItemId, int $newQuantity, int $userId)
    {
        // 1. جلب عنصر السلة مع معلومات المنتج المرتبط به (لتوفير الـ uuid / sku)
        $cartItem = $this->cartRepository->findUserCartItem($cartItemId, $userId);

        // التأكد من جلب علاقة المنتج للحصول على الـ uuid أو الـ sku الخاص به
        // (إذا كانت العلاقة محملة مسبقاً أو تحتاج لجلبها)
        $productUuid = $cartItem->product->uuid ?? $cartItem->product->sku;

        // 2. التحقق من المخزون باستخدام الـ uuid/sku والكمية الجديدة
        $isAvailable = $this->inventoryService->checkStock($productUuid, $newQuantity);

        if (!$isAvailable) {
            throw new Exception('عذراً، الكمية الجديدة غير متوفرة في المخزون.', 422);
        }

        // 3. تحديث الكمية إذا كانت متوفرة
        return $this->cartRepository->updateQuantity($cartItem, $newQuantity);
    }

    public function deleteItem($cartItemId, int $userId)
    {
        $cartItem = $this->cartRepository->findUserCartItem($cartItemId, $userId);

        return $this->cartRepository->removeItem($cartItem);
    }

    public function emptyCart(int $userId)
    {
        return $this->cartRepository->clearCart($userId);
    }
}