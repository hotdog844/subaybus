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
    public function run()
    {
        // 1. Create the ADMIN
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@subaybus.com'],
            [
                'name'       => 'Admin Boss',  // ✅ Satisfies the old 'name' column
                'first_name' => 'Admin',       // ✅ Satisfies the new column
                'last_name'  => 'Boss',        // ✅ Satisfies the new column
                'password'   => bcrypt('password'),
                'role'       => 'admin',
            ]
        );

        // 2. Create the DRIVER
        \App\Models\User::firstOrCreate(
            ['email' => 'driver@subaybus.com'],
            [
                'name'       => 'Driver Mario',
                'first_name' => 'Driver',
                'last_name'  => 'Mario',
                'password'   => bcrypt('password'),
                'role'       => 'driver',
            ]
        );

        // 3. Create the PASSENGER (User)
        \App\Models\User::firstOrCreate(
            ['email' => 'juan@gmail.com'],
            [
                'name'       => 'Juan Dela Cruz',
                'first_name' => 'Juan',
                'last_name'  => 'Dela Cruz',
                'password'   => bcrypt('password'),
                'role'       => 'user',
            ]
        );
        
        echo "✅ Test accounts created successfully!\n";
    }
}
