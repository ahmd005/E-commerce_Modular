<?php

namespace Modules\Orders\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'     => $this->uuid,
            'quantity' => $this->quantity,
            'price'    => $this->price,
            'subtotal' => round($this->quantity * $this->price, 2),
            'product'  => [
                'id'   => $this->product->id ?? null,
                'uuid' => $this->product->uuid ?? null,
                'name' => $this->product->name ?? null,
            ],
        ];
    }
}
