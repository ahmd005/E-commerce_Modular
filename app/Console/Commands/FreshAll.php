<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FreshAll extends Command
{
    protected $signature = 'db:fresh-all {--seed}';
    protected $description = 'migrate:fresh على ecommerce_app و ecommerce_replica';

    public function handle(): int
    {
        foreach (['mysql_read', 'mysql'] as $connection) {
            $this->info("migrate:fresh على: {$connection}");
            $this->call('migrate:fresh', ['--database' => $connection]);
        }

        if ($this->option('seed')) {
            // الـ Seeders تمر عبر Models فتنسخ للقراءة تلقائياً
            $this->call('db:seed');
        }

        return self::SUCCESS;
    }
}