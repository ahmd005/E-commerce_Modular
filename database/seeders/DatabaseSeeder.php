<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Products\Database\Seeders\ProductsDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
   public function run(): void
    {
        // استدعاء سيدر الأدمن القديم
        $this->call([
            AdminUserSeeder::class,
        ]);

        // استدعاء سيدر المنتجات والتصنيفات الضخم لاختبار الأداء
        $this->call([
            ProductsDatabaseSeeder::class,
        ]);
    }
}
