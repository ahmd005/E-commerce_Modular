<?php

namespace Modules\Orders\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $price = $this->product->price ?? 0;

        return [
            'id'         => $this->id,
            'uuid'       => $this->uuid,
            'quantity'   => $this->quantity,
            'product'    => [
                'id'    => $this->product->id ?? null,
                'uuid'  => $this->product->uuid ?? null,
                'name'  => $this->product->name ?? null,
                'price' => $this->product->price ?? null,
            ],
            'subtotal'   => $this->quantity * $price,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
