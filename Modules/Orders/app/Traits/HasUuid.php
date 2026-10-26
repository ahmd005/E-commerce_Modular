<?php

namespace Modules\Orders\App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * يولّد عمود uuid تلقائياً عند إنشاء أي موديل يستخدم هذا الـ Trait.
 */
trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            $model->uuid ??= (string) Str::uuid();
        });
    }
}
