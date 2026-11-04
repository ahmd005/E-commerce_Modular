<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // <-- أضف هذه المكتبة لتوليد الـ UUID

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@app.com'],
            [
                'uuid' => (string) Str::uuid(), // <-- توليد UUID فريد ومطلوب للحقل الإلزامي
                'name' => 'Admin User',
                'email' => 'admin@app.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );
    }
}