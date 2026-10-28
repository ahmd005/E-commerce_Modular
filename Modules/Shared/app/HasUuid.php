<?php

namespace Modules\Shared\app;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * يتم استدعاء هذه الدالة تلقائياً عند إقلاع النموذج (Model)
     */
    protected static function bootHasUuid(): void
    {
        // الحدث 'creating' يعمل قبل حفظ الكائن في قاعدة البيانات مباشرة
        static::creating(function ($model) {
            // التحقق مما إذا كان الحقل uuid فارغاً
            if (empty($model->{$model->getUuidColumn()})) {
                // توليد UUID جديد وتعيينه
                $model->{$model->getUuidColumn()} = (string) Str::uuid();
            }
        });
    }

    /**
     * تحديد اسم العمود الذي سيحمل الـ UUID (يمكن تغييره إذا أردت)
     */
    public function getUuidColumn(): string
    {
        return 'uuid';
    }

    /**
     * (اختياري) ربط النموذج بالـ UUID بدلاً من الـ ID في الروابط (Routes)
     */
    public function getRouteKeyName(): string
    {
        return $this->getUuidColumn();
    }
}