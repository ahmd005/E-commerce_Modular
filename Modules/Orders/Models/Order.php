<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Users\Models\User;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'total_price',
        'status',
    ];

    // علاقة الطلب مع المستخدم (الطلب يتبع لمستخدم واحد)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة الطلب مع عناصر تفاصيل الطلب (الطلب يحتوي على عدة عناصر)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}