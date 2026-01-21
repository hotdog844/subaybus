<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RouteSeeder extends Seeder
{
    public function run()
    {
        // 1. DISABLE FOREIGN KEY CHECKS (To prevent errors when wiping old data)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Optional: clear old connections so we don't get duplicates
        // Only run this if you want to start fresh with the "Streamlined" routes
        DB::table('route_stops')->truncate(); 

        // 2. THE OFFICIAL STREAMLINED BUS STOPS (The "80/20 Rule")
        $stops = [
            ['id' => 1, 'name' => 'RCITT Terminal (Hub)', 'lat' => 11.559207, 'lng' => 122.750817],
            ['id' => 2, 'name' => 'Pueblo de Panay (Sitio Uno)', 'lat' => 11.566475, 'lng' => 122.751907],
            ['id' => 3, 'name' => 'Robinsons Place Hub', 'lat' => 11.569562, 'lng' => 122.751172],
            ['id' => 4, 'name' => 'Filamer / Capiz Doctors', 'lat' => 11.575570, 'lng' => 122.753158],
            ['id' => 5, 'name' => 'Roxas City Hall / Plaza', 'lat' => 11.583036, 'lng' => 122.752298],
            ['id' => 6, 'name' => 'Gaisano Grand / CityMall', 'lat' => 11.589697, 'lng' => 122.752243],
            ['id' => 7, 'name' => 'SM City Roxas', 'lat' => 11.596416, 'lng' => 122.748605],
            ['id' => 8, 'name' => 'Roxas Airport', 'lat' => 11.598791, 'lng' => 122.745879],
            ['id' => 9, 'name' => 'Peoples Park (Baybay)', 'lat' => 11.606579, 'lng' => 122.736620],
            ['id' => 10, 'name' => 'Culasi Terminal', 'lat' => 11.605087, 'lng' => 122.710023], 
            ['id' => 11, 'name' => 'Libas Terminal', 'lat' => 11.591211, 'lng' => 122.723769], 
            ['id' => 12, 'name' => 'Capiz Provincial Capitol', 'lat' => 11.584374, 'lng' => 122.769649],
            ['id' => 13, 'name' => 'Villareal Stadium', 'lat' => 11.576415, 'lng' => 122.758533],
            ['id' => 14, 'name' => 'Tanza Welcome Arc', 'lat' => 11.585504, 'lng' => 122.777546],
        ];

        foreach ($stops as $stop) {
            // We use insertOrIgnore to avoid crashing if ID exists
            DB::table('stops')->updateOrInsert(
                ['id' => $stop['id']],
                [
                    'name' => $stop['name'],
                    'latitude' => $stop['lat'],
                    'longitude' => $stop['lng'],
                    'created_at' => now(), 'updated_at' => now()
                ]
            );
        }

        // 3. DEFINE THE ROUTES
        
        // --- ROUTE A: PdP RED (Terminal -> Culasi) ---
        $redRouteId = DB::table('routes')->insertGetId([
            'name' => 'PdP Red: Terminal to Culasi',
            'start_location' => 'RCITT Terminal',
            'end_location' => 'Culasi',
            'origin_lat' => 11.559207, 'origin_lng' => 122.750817,
            'dest_lat' => 11.605087, 'dest_lng' => 122.710023,
            'color' => '#ef4444', // Red
            'created_at' => now(), 'updated_at' => now()
        ]);

        // Link Stops for RED
        $redStops = [1, 2, 3, 5, 6, 7, 8, 9, 10]; 
        $order = 1;
        foreach ($redStops as $stopId) {
            DB::table('route_stops')->insert([
                'route_id' => $redRouteId, 
                'stop_id' => $stopId, 
                'order' => $order++,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // --- ROUTE B: PdP BLUE (Terminal -> Libas) ---
        $blueRouteId = DB::table('routes')->insertGetId([
            'name' => 'PdP Blue: Terminal to Libas',
            'start_location' => 'RCITT Terminal',
            'end_location' => 'Libas',
            'origin_lat' => 11.559207, 'origin_lng' => 122.750817,
            'dest_lat' => 11.591211, 'dest_lng' => 122.723769,
            'color' => '#3b82f6', // Blue
            'created_at' => now(), 'updated_at' => now()
        ]);

        // Link Stops for BLUE
        $blueStops = [1, 2, 3, 5, 6, 11]; 
        $order = 1;
        foreach ($blueStops as $stopId) {
            DB::table('route_stops')->insert([
                'route_id' => $blueRouteId, 
                'stop_id' => $stopId, 
                'order' => $order++,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // --- ROUTE C: PdP GREEN (Terminal -> Tanza) ---
        $greenRouteId = DB::table('routes')->insertGetId([
            'name' => 'PdP Green: Terminal to Tanza',
            'start_location' => 'RCITT Terminal',
            'end_location' => 'Tanza',
            'origin_lat' => 11.559207, 'origin_lng' => 122.750817,
            'dest_lat' => 11.585504, 'dest_lng' => 122.777546,
            'color' => '#22c55e', // Green
            'created_at' => now(), 'updated_at' => now()
        ]);

        // Link Stops for GREEN
        $greenStops = [1, 2, 3, 4, 13, 12, 14]; 
        $order = 1;
        foreach ($greenStops as $stopId) {
            DB::table('route_stops')->insert([
                'route_id' => $greenRouteId, 
                'stop_id' => $stopId, 
                'order' => $order++,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }
        
        // 4. RE-ENABLE CHECKS
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}