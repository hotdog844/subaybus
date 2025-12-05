<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin account
        User::create([
            'name' => 'Test User',
            'first_name' => 'Kenneth',
            'last_name' => 'Desales',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'phone' => '09123456789',
            'passenger_type' => 'Student',
            'id_image_path' => null,
            'is_verified' => true,
            'is_admin' => true,
        ]);
    }
}
