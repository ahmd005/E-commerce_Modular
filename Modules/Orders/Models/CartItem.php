<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Orders\App\Traits\HasUuid;
use Modules\Products\Models\Product;
use Modules\Users\Models\User;

class CartItem extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'cart_items';

    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'quantity',
    ];

    // علاقة عنصر السلة مع المستخدم (BelongsTo)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // علاقة عنصر السلة مع المنتج (BelongsTo)
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
