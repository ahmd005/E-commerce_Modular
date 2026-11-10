<?php

namespace Modules\Users\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    private const TOTAL_USERS = 200;

    public function run(): void
    {
        // تجزئة كلمة المرور مرة واحدة فقط (bcrypt مكلف جدًا في الحلقات)
        $hashedPassword = Hash::make('password');
        $now = now();

       

        for ($i = 2; $i <= self::TOTAL_USERS; $i++) {
            $rows[] = [
                'uuid'           => (string) Str::uuid(),
                'name'           => "Customer {$i}",
                'email'          => "customer{$i}@example.com",
                'password'       => $hashedPassword,
                'role'           => 'customer',
                'remember_token' => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        $this->command->info('✓ Users: ' . self::TOTAL_USERS);
        $this->command->line('   admin@example.com / password');
    }
}