<?php

namespace Modules\Orders\Services;

use Modules\Orders\Models\CartItem;
use Modules\Orders\Repositories\CartRepositoryInterface;
use Modules\Products\Models\Product;

class CartService
{
    protected CartRepositoryInterface $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getCart(int $userId)
    {
        $items = $this->cartRepository->getUserCart($userId);

        $total = $items->sum(
            fn (CartItem $item) => $item->quantity * ($item->product->price ?? 0)
        );

        return [
            'items' => $items,
            'cart_total' => $total,
        ];
    }

    public function addItem(int $userId, array $data)
    {
        $product = Product::where('uuid', $data['product_uuid'])->firstOrFail();

        $existingItem = $this->cartRepository->findExistingItem($userId, $product->id);

        if ($existingItem) {
            return $this->cartRepository->incrementQuantity($existingItem, $data['quantity']);
        }

        return $this->cartRepository->createItem([
            'user_id' => $userId,
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
        ]);
    }

    public function updateItemQuantity($cartItemId, int $quantity, int $userId)
    {
        $cartItem = $this->cartRepository->findUserCartItem($cartItemId, $userId);

        return $this->cartRepository->updateQuantity($cartItem, $quantity);
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
