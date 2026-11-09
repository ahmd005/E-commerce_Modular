<?php

namespace App\Providers;

use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        DB::listen(function ($q) {
            if (! in_array($q->connectionName, ['mysql', 'mysql_read'])) {
                return;
            }
            if (str_contains($q->sql, 'telescope_')) {
                return;
            }

            $app     = config('database.connections.mysql.database');
            $replica = config('database.connections.mysql_read.database');

            $isRead = (bool) preg_match('/^\s*(select|show)\b/i', $q->sql);

            // القاعدة الفعلية
            if ($q->connectionName === 'mysql_read') {
                $db = $replica;                       // استعلام النسخ نُفّذ على الـ replica
            } else {
                // readWriteType يحدده Laravel نفسه: read = Read PDO، write = Write PDO
                $type = $q->readWriteType ?? ($isRead ? 'read' : 'write');
                $db   = $type === 'read' ? $replica : $app;
            }

            $usage = app(\App\Support\DbUsage::class);
            $isRead ? $usage->reads[] = $db : $usage->writes[] = $db;
        });

        Telescope::tag(function (IncomingEntry $entry) {
            if ($entry->type !== 'query') {
                return [];
            }

            $connection = $entry->content['connection'] ?? 'unknown';
            $db = config("database.connections.{$connection}.database");

            return ["db:{$db}"];
        });
        // 1) نسخ كل عملية كتابة من mysql إلى mysql_read
        DB::listen(function ($query) {
            if ($query->connectionName !== 'mysql') {
                return;
            }

            if (! preg_match('/^\s*(insert|update|delete|replace)\b/i', $query->sql)) {
                return;
            }

            // جدول migrations يُدار بكل قاعدة لوحدها
            if (str_contains($query->sql, '`migrations`')) {
                return;
            }

            DB::connection('mysql_read')->affectingStatement($query->sql, $query->bindings);
        });

        $this->app->singleton(\App\Support\DbUsage::class);

        // 2) نعكس الـ transactions على القاعدة الثانية
        Event::listen(TransactionBeginning::class, function ($e) {
            if ($e->connectionName === 'mysql') {
                DB::connection('mysql_read')->beginTransaction();
            }
        });
        Event::listen(TransactionCommitted::class, function ($e) {
            if ($e->connectionName === 'mysql') {
                DB::connection('mysql_read')->commit();
            }
        });
        Event::listen(TransactionRolledBack::class, function ($e) {
            if ($e->connectionName === 'mysql') {
                DB::connection('mysql_read')->rollBack();
            }
        });

        // 3) توجيه القراءة (فقط خارج الـ console)
        if (! $this->app->runningInConsole()) {
            DB::connection('mysql')->setReadPdo(
                fn() => DB::connection('mysql_read')->getPdo()
            );
        }
    }
}
