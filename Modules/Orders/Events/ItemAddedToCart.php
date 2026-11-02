<?php
namespace Modules\Orders\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Orders\Models\CartItem;

class ItemAddedToCart
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cart;

    public function __construct(CartItem $cart)
    {
        $this->cart = $cart;
    }
}