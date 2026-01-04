<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixRoutesSeeder extends Seeder
{
    public function run()
    {
        // 1. DISABLE FOREIGN KEY CHECKS (To prevent errors when deleting)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. CLEAR EXISTING ROUTES (Deletes the duplicates)
        DB::table('routes')->truncate();

        // 3. INSERT CLEAN DATA (With Start/End locations!)
        $routes = [
            [
                'name' => 'PdP Green Route',
                'start_location' => 'Roxas City Terminal',
                'end_location' => 'Pueblo de Panay',
                'distance' => 5.2,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'PdP Red Route',
                'start_location' => 'City Proper',
                'end_location' => 'Lawaan / Banica',
                'distance' => 6.8,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'PdP Blue Route',
                'start_location' => 'Baybay Beach',
                'end_location' => 'Panay Boundary',
                'distance' => 8.5,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'RCPUVTC (Pontevedra)',
                'start_location' => 'Pontevedra Market',
                'end_location' => 'Roxas Integration',
                'distance' => 12.4,
                'created_at' => now(), 'updated_at' => now()
            ]
        ];

        DB::table('routes')->insert($routes);

        // 4. RESET BUS ASSIGNMENTS (Set all buses to "No Route" to avoid broken links)
        DB::table('buses')->update(['route_id' => null]);

        // 5. RE-ENABLE CHECKS
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}