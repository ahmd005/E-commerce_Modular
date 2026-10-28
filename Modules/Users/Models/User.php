<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Shared\app\HasUuid;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable,HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // علاقة المستخدم مع عناصر السلة
    public function cartItems()
    {
        return $this->hasMany(\Modules\Orders\Models\CartItem::class);
    }

     public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            // يمكننا إضافة الأدوار هنا لتسريع التحقق في الوحدات الأخرى
            'roles' => "{$this->role}",
        ];
    }

     public function getJWTIdentifier()
    {
        return $this->getKey();
    }

  
}