<?php

namespace Modules\Orders\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Orders\Models\Order;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public string $action; // (created, updated, deleted, cancelled)

    public function __construct(Order $order, string $action)
    {
        $this->order = $order;
        $this->action = $action;
    }

    /**
     * القناة التي سيتم بث الحدث عليها
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('orders-channel'), // يمكنك تخصيصها لكل مستخدم لاحقاً مثل new PrivateChannel('orders.' . $this->order->user_id)
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.updated';
    }
}