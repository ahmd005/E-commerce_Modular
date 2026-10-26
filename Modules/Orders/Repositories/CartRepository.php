<?php

namespace Modules\Orders\Repositories;

use Modules\Orders\Models\CartItem;

class CartRepository implements CartRepositoryInterface
{
    // جلب عناصر سلة المستخدم (مع إمكانية تحديد عناصر معينة فقط)
    public function getUserCart(int $userId, array $cartItemIds = [])
    {
        return CartItem::with('product')
            ->where('user_id', $userId)
            ->when($cartItemIds !== [], fn($query) => $query->whereIn('id', $cartItemIds))
            ->get();
    }

    public function findExistingItem(int $userId, int $productId)
    {
        return CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function createItem(array $data)
    {
        return CartItem::create($data)->load('product');
    }

    public function incrementQuantity(CartItem $cartItem, int $quantity)
    {
        $cartItem->increment('quantity', $quantity);

        return $cartItem->fresh('product');
    }

    public function findUserCartItem($cartItemUuid, $userId)
    {
        return CartItem::where('uuid', $cartItemUuid)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function updateQuantity(CartItem $cartItem, int $quantity)
    {
        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh('product');
    }

    public function removeItem(CartItem $cartItem)
    {
        return $cartItem->delete();
    }

    public function removeItemsByIds(int $userId, array $cartItemIds)
    {
        return CartItem::where('user_id', $userId)
            ->whereIn('id', $cartItemIds)
            ->delete();
    }

    public function clearCart(int $userId)
    {
        return CartItem::where('user_id', $userId)->delete();
    }
}
