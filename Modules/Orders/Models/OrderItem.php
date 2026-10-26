<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Orders\App\Traits\HasUuid;
use Modules\Products\Models\Product;

class OrderItem extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'uuid',
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    // عنصر الطلب يتبع لطلب واحد
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // عنصر الطلب يرتبط بمنتج معين
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
