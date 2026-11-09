<?php

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;
use Modules\Shared\Services\InventoryGrpcService;

class ProductsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        // 1) تنظيف MySQL
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 2) تعبئة MySQL
        $this->command->info('Creating categories...');
        Category::factory(100)->create();

        $this->command->info('Creating products...');
        $batches = 20;
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

        // 3) المخزون في النود
        $this->seedInventory();
    }

    private function seedInventory(): void
    {
        try {
            $grpc = app(InventoryGrpcService::class);

            // أ) مسح المخزون القديم (اليتيم)
            $this->command->info('Clearing old inventory in Node...');
            $old = $grpc->listProducts();
            $cleared = 0;
            foreach ($old as $row) {
                $grpc->deleteProduct($row['sku']);
                $cleared++;
            }
            $this->command->info("Cleared {$cleared} old inventory rows.");
        } catch (\Throwable $e) {
            $this->command->warn('النود غير متاحة، تم تخطي المخزون: ' . $e->getMessage());
            return;
        }

        // ب) تعبئة المخزون الجديد
        $total = DB::table('products')->count();
        $done = 0;
        $failed = 0;
        $start = microtime(true);

        foreach (DB::table('products')->orderBy('id')->cursor() as $p) {
            try {
                $grpc->createProduct([
                    'name' => $p->name,
                    'sku' => $p->uuid,
                    'initial_quantity' => random_int(0, 500),
                ]);
                $done++;
            } catch (\Throwable $e) {
                $failed++;
                if ($failed === 1) {
                    $this->command->error('أول خطأ: ' . $e->getMessage());
                }
                if ($failed >= 5 && $done === 0) {
                    $this->command->error('توقف: النود غالباً مطفية.');
                    return;
                }
            }

            if (($done + $failed) % 1000 === 0) {
                $this->command->info("Inventory: {$done}/{$total} (failed: {$failed})");
            }
        }

        $secs = round(microtime(true) - $start, 1);
        $this->command->info("Inventory done in {$secs}s. OK: {$done}, Failed: {$failed}");
    }
}