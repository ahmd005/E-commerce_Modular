<?php

namespace Modules\Notifications\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'uuid'        => $this->uuid,
            'title'       => $this->title,
            'message'     => $this->message,
            'is_read'     => $this->is_read,
            'read_at'     => $this->read_at?->toIso8601String(),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}