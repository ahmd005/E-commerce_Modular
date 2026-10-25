<?php

namespace Modules\Products\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'uuid' => $this->uuid,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => $this->price,
            'image'       => $this->image ? asset('storage/' . $this->image) : null,
            'category'    => [
                'id'   => $this->category->id ?? null,
                'name' => $this->category->name ?? null,
            ],
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}