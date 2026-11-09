<?php

namespace Modules\Products\Services;

use Illuminate\Support\Facades\Cache;

class ProductCache
{
    public static function version(): int
    {
        return (int) Cache::get('v2:products:ver', 0);
    }

    // أي كتابة (إنشاء/تعديل/حذف) تستدعيها، فكل المفاتيح القديمة بتصير غير مستخدمة وبتنتهي بالـ TTL
    public static function bump(): void
    {
        Cache::increment('v2:products:ver');
    }
}