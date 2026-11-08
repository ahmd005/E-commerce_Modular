<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\DB;
/**
 * @mixin Model
 */
trait ReplicatesToReadDb
{
    protected static function bootReplicatesToReadDb(): void
    {
        static::saved(function ($model) {
            $data = $model->getAttributes();

            // يُنفَّذ بعد نجاح الـ transaction (أو فوراً إن لم يكن هناك transaction)
            DB::afterCommit(function () use ($model, $data) {
                DB::connection('mysql_read')
                    ->table($model->getTable())
                    ->updateOrInsert(
                        [$model->getKeyName() => $model->getKey()],
                        $data
                    );
            });
        });

        static::deleted(function ($model) {
            // مع SoftDeletes يُنسخ deleted_at عبر saved، فلا نحذف الصف فعلياً
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $table = $model->getTable();
            $key   = $model->getKeyName();
            $id    = $model->getKey();

            DB::afterCommit(function () use ($table, $key, $id) {
                DB::connection('mysql_read')->table($table)->where($key, $id)->delete();
            });
        });
    }
}