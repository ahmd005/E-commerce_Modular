<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Orders\App\Enums\OrderStatus;
use Modules\Orders\App\Traits\HasUuid;
use Modules\Users\Models\User;

class Order extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'total_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_price' => 'decimal:2',
        ];
    }

    // علاقة الطلب مع المستخدم (الطلب يتبع لمستخدم واحد)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // علاقة الطلب مع عناصر تفاصيل الطلب (الطلب يحتوي على عدة عناصر)
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
