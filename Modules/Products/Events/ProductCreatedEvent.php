<?php

namespace Modules\Products\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class ProductCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product; // متغير عام لحفظ بيانات المنتج

    /**
     * Create a new event instance.
     */
    public function __construct($product) // استقبال المنتج هنا
    {
        $this->product = $product;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}