<?php

namespace Modules\Orders\Repositories;

use Modules\Orders\Models\CartItem;

interface CartRepositoryInterface
{
    public function getUserCart(int $userId, array $cartItemIds = []);
    public function findExistingItem(int $userId, int $productId);
    public function createItem(array $data);
    public function incrementQuantity(CartItem $cartItem, int $quantity);
    public function findUserCartItem($cartItemId, int $userId);
    public function updateQuantity(CartItem $cartItem, int $quantity);
    public function removeItem(CartItem $cartItem);
    public function removeItemsByIds(int $userId, array $cartItemIds);
    public function clearCart(int $userId);
}
