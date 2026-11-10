<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Products\Models\Product;
use Modules\Users\Models\User;

class OrderSeeder extends Seeder
{
    private const TOTAL_ORDERS = 10000;
    private const CHUNK_SIZE   = 500;
    private const MAX_ITEMS    = 5;
    private const MAX_QTY      = 3;

    public function run(): void
    {
        $userIds  = User::where('role', 'customer')->pluck('id')->all();
        $products = Product::select('id', 'price')->get();

        if (empty($userIds) || $products->isEmpty()) {
            $this->command->error('شغّل UserSeeder و ProductSeeder أولاً.');
            return;
        }

        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $bar = $this->command->getOutput()->createProgressBar(self::TOTAL_ORDERS);
        $bar->start();

        for ($created = 0; $created < self::TOTAL_ORDERS; $created += self::CHUNK_SIZE) {
            $batchSize = min(self::CHUNK_SIZE, self::TOTAL_ORDERS - $created);
            $this->seedBatch($batchSize, $userIds, $products, $statuses);
            $bar->advance($batchSize);
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('✓ Orders: ' . self::TOTAL_ORDERS);
    }

    private function seedBatch(int $size, array $userIds, $products, array $statuses): void
    {
        DB::transaction(function () use ($size, $userIds, $products, $statuses) {
            $now          = now();
            $orders       = [];
            $pendingItems = [];
            $maxItems     = min(self::MAX_ITEMS, $products->count());

            for ($i = 0; $i < $size; $i++) {
                $uuid       = (string) Str::uuid();
                $itemsCount = random_int(1, $maxItems);
                $total      = 0.0;

                // random($n) يُرجع Collection عند $n >= 1
                $selected = $products->random($itemsCount);

                foreach ($selected as $product) {
                    $qty   = random_int(1, self::MAX_QTY);
                    $total += (float) $product->price * $qty;

                    $pendingItems[] = [
                        'order_uuid' => $uuid,
                        'product_id' => $product->id,
                        'quantity'   => $qty,
                        'price'      => $product->price,
                    ];
                }

                $orders[] = [
                    'uuid'        => $uuid,
                    'user_id'     => $userIds[array_rand($userIds)],
                    'total_price' => round($total, 2),
                    'status'      => $statuses[array_rand($statuses)],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            DB::table('orders')->insert($orders);

            // ربط order_items بمعرّفات الطلبات الفعلية
            $orderIds = DB::table('orders')
                ->whereIn('uuid', array_column($orders, 'uuid'))
                ->pluck('id', 'uuid');

            $items = [];
            foreach ($pendingItems as $item) {
                $items[] = [
                    'uuid'       => (string) Str::uuid(),
                    'order_id'   => $orderIds[$item['order_uuid']],
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('order_items')->insert($items);
        });
    }
}