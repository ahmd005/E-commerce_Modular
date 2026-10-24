<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Products\Models\Product;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    // عنصر الطلب يتبع لطلب واحد
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // عنصر الطلب يرتبط بمنتج معين
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}