<?php

namespace Modules\Products\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
         //   'description' => $this->description,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}