<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncReplica extends Command
{
    protected $signature = 'db:sync-replica';
    protected $description = 'نسخ كل البيانات من ecommerce_app إلى ecommerce_replica';

    public function handle(): int
    {
        $src = DB::connection('mysql');
        $dst = DB::connection('mysql_read');

        $dst->statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($src->select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"') as $row) {
            $table = array_values((array) $row)[0];

            $dst->table($table)->truncate();

            $src->table($table)->get()->chunk(500)->each(function ($rows) use ($dst, $table) {
                $dst->table($table)->insert($rows->map(fn ($r) => (array) $r)->all());
            });

            $this->info("تم نسخ: {$table}");
        }

        $dst->statement('SET FOREIGN_KEY_CHECKS=1');

        return self::SUCCESS;
    }
}