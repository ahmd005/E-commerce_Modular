<?php

namespace Modules\Orders\Repositories;

use Modules\Orders\App\Enums\OrderStatus;
use Modules\Orders\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    private const RELATIONS = ['items.product'];

    // جلب طلبات المستخدم مع تقسيم الصفحات (Pagination)
    public function paginateUserOrders(int $userId, int $perPage)
    {
        return Order::with(self::RELATIONS)
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function findUserOrder(string $uuid, int $userId)
    {
        return Order::with(self::RELATIONS)
            ->where('uuid', $uuid)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return Order::create($data);
    }

    public function createItems(Order $order, array $items)
    {
        return $order->items()->createMany($items);
    }

    public function updateStatus(Order $order, OrderStatus $status)
    {
        $order->update(['status' => $status]);

        return $order->fresh(self::RELATIONS);
    }

    public function delete(Order $order)
    {
        return $order->delete();
    }
}
