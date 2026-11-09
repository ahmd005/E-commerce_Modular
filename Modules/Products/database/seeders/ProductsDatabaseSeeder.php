<?php

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;

class ProductsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        // تنظيف: نفس البيانات بالضبط في كل تشغيل
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Creating categories...');
        Category::factory(100)->create();

        $this->command->info('Creating products...');
        $batches = 20;      // 20 × 1000 = 20,000 منتج
        $batchSize = 1000;

        for ($i = 0; $i < $batches; $i++) {
            $now = now('UTC');

            $rows = Product::factory($batchSize)->make()->map(function ($p) use ($now) {
                return $p->getAttributes() + [
                    'created_at' => $now->copy()->subSeconds(random_int(0, 31536000))->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString(),
                ];
            })->all();

            DB::table('products')->insert($rows);
            $this->command->info('Inserted ' . (($i + 1) * $batchSize));
        }
    }
}