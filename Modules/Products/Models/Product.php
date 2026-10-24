<?php

namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
    ];

    // علاقة المنتج مع التصنيف (BelongsTo)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // علاقة المنتج مع عناصر الطلبات (Many-to-Many عبر order_items)
    public function orderItems()
    {
        return $this->hasMany(\Modules\Orders\Models\OrderItem::class);
    }
}