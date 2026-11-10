<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Products\Models\Product;
use Modules\Users\Models\User;

class CartItemSeeder extends Seeder
{
    private const MAX_ITEMS_PER_USER = 5;

    public function run(): void
    {
        $products = Product::pluck('id');

        if ($products->isEmpty()) {
            $this->command->error('شغّل ProductSeeder أولاً.');
            return;
        }

        $userIds = User::where('role', 'customer')->pluck('id');
        $now     = now();
        $rows    = [];

        foreach ($userIds as $userId) {
            // بعض المستخدمين لديهم سلة فارغة (محاكاة واقعية)
            $itemsCount = random_int(0, min(self::MAX_ITEMS_PER_USER, $products->count()));
            if ($itemsCount === 0) {
                continue;
            }

            // random($n) يُرجع منتجات فريدة → لا انتهاك للـ unique(user_id, product_id)
            foreach ($products->random($itemsCount) as $productId) {
                $rows[] = [
                    'uuid'       => (string) Str::uuid(),
                    'user_id'    => $userId,
                    'product_id' => $productId,
                    'quantity'   => random_int(1, 5),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('cart_items')->insert($chunk);
        }

        $this->command->info('✓ Cart Items: ' . count($rows));
    }
}