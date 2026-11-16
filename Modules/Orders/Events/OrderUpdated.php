<?php

namespace Modules\Orders\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Orders\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;


class OrderUpdated implements ShouldBroadcast
{
    public $order;
    public $action;

    public function __construct($order, $action)
    {
        $this->order = $order;
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        return [
            // القناة الأولى: لوحة المسؤول (Private Channel)
            new PrivateChannel('admin-orders-channel'),
            
            // القناة الثانية: قناة المستخدم صاحب الطلب نفسه (Private Channel)
            new PrivateChannel('user-orders-channel.' . $this->order->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return '.order.updated';
    }
}