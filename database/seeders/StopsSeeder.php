<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StopsSeeder extends Seeder
{
    public function run()
    {
        DB::table('stops')->truncate();

        $stops = [
            // ==========================================
            // 🟢 PdP GREEN ROUTE (High Precision Road Snapped)
            // ==========================================
            // 1. Terminal (Exit Road)
            ['PdP Green', 'Transport Terminal', 11.559850, 122.751450, 1], 
            
            // 2. Pueblo (Main Road, not parking lot)
            ['PdP Green', 'Pueblo de Panay', 11.562750, 122.749650, 2],    

            // 3. Security Bank (National Highway)
            ['PdP Green', 'Security Bank', 11.559200, 122.751650, 3],

            // 4. Robinsons Place (Immaculate Heart Ave Entrance) - FIXED
            ['PdP Green', 'Robinsons Place', 11.564600, 122.748400, 4],    

            // --- INVISIBLE CORNER (To make the turn smooth) ---
            ['PdP Green', 'Lawaan Intersection', 11.568500, 122.753800, 5], 

            // 5. Roxas Avenue (On the road)
            ['PdP Green', 'Roxas Avenue', 11.571781, 122.753940, 6],

            // 6. Hemingway Street (Aligned)
            ['PdP Green', 'Hemingway Street', 11.573600, 122.756500, 7],

            // 7. Camansi St (DPWH Frontage)
            ['PdP Green', 'DPWH (Camansi St)', 11.574760, 122.757160, 8],

            // 8. Villareal Stadium (Main Gate Road)
            ['PdP Green', 'Villareal Stadium', 11.575330, 122.758540, 9],

            // 9. CapSU Main (Fuentes Drive Road)
            ['PdP Green', 'CapSU Main', 11.577600, 122.756050, 10],

            // 10. Capiz Doctors (Moved from Building to Street) - FIXED
            ['PdP Green', 'Capiz Doctors Hospital', 11.577350, 122.754600, 11],

            // --- INVISIBLE BRIDGE APPROACH ---
            ['PdP Green', 'Bridge Approach', 11.581500, 122.753500, 12],

            // 11. Capiz Bridge (Center of Bridge)
            ['PdP Green', 'Capiz Bridge', 11.582410, 122.753089, 13],

            // 12. Tiza Bridge (Center)
            ['PdP Green', 'Tiza Bridge', 11.578520, 122.759880, 14],

            // 13. Legaspi Street (Junction)
            ['PdP Green', 'Legaspi Street', 11.584480, 122.759120, 15],

            // 14. Gov. A. Balgos Ave
            ['PdP Green', 'Gov. A. Balgos Ave', 11.580420, 122.763335, 16],

            // 15. Tanza Welcome Arc (Road Center)
            ['PdP Green', 'Tanza Welcome Arc', 11.581900, 122.786630, 17],


            // ==========================================
            // 🔴 PdP RED JEEP ROUTE (Previous)
            // ==========================================
            ['PdP Red', 'Transport Terminal', 11.559900, 122.751400, 1],
            ['PdP Red', 'Pueblo de Panay', 11.562700, 122.749800, 2],
            ['PdP Red', 'Security Bank', 11.559131, 122.751619, 3],
            ['PdP Red', 'Robinsons Place', 11.564500, 122.748500, 4],
            ['PdP Red', 'Hercor College', 11.569200, 122.754500, 5],
            ['PdP Red', 'Roxas City Hall', 11.584800, 122.752300, 6],
            ['PdP Red', 'SM City Roxas', 11.593500, 122.748100, 7], 
            ['PdP Red', 'Roxas City Airport', 11.598200, 122.748600, 8], 
            ['PdP Red', 'Arnaldo Boulevard', 11.597800, 122.744800, 9],
            ['PdP Red', 'Port of Culasi', 11.604200, 122.709500, 10],

            // ==========================================
            // 🔵 PdP BLUE JEEP ROUTE (Previous)
            // ==========================================
            ['PdP Blue', 'Transport Terminal', 11.559900, 122.751400, 1],
            ['PdP Blue', 'Robinsons Place', 11.564500, 122.748500, 2],
            ['PdP Blue', 'Roxas City Hall', 11.584800, 122.752300, 3],
            ['PdP Blue', 'Capelco', 11.585200, 122.745500, 4],
            ['PdP Blue', 'Brgy. Libas', 11.595000, 122.721400, 5],

            // ==========================================
            // 🟣 UV EXPRESS (Previous)
            // ==========================================
            ['UV Express', 'UV Terminal', 11.583900, 122.762500, 1],
            ['UV Express', 'Banica', 11.584400, 122.769200, 2],
            ['UV Express', 'Panay Church', 11.556100, 122.793900, 3],
            ['UV Express', 'Pontevedra Proper', 11.481300, 122.832700, 4],
        ];

        foreach ($stops as $stop) {
            DB::table('stops')->insert([
                'route_name' => $stop[0],
                'name' => $stop[1],
                'lat' => $stop[2],
                'lng' => $stop[3],
                'order_index' => $stop[4],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}