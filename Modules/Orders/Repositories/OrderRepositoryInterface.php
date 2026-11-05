<?php

namespace Modules\Orders\Repositories;

use Modules\Orders\App\Enums\OrderStatus;
use Modules\Orders\Models\Order;

interface OrderRepositoryInterface
{
    public function paginateUserOrders(int $userId, int $perPage);
    public function paginateAllOrders(int $perPage, ?OrderStatus $status = null);
    public function findUserOrder(string $uuid, int $userId);
    public function findOrderByUuid(string $uuid);
    public function create(array $data);
    public function createItems(Order $order, array $items);
    public function updateStatus(Order $order, OrderStatus $status);
    public function delete(Order $order);
}
