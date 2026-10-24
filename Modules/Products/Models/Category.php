<?php

namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    // علاقة التصنيف مع المنتجات (One-to-Many)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}