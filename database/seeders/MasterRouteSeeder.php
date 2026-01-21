<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\Stop;
use Illuminate\Support\Facades\DB;

class MasterRouteSeeder extends Seeder
{
    public function run()
    {
        // 1. Clean the tables first to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Route::truncate();
        Stop::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ==========================================
        // DATA DEFINITIONS (High Fidelity Paths)
        // ==========================================

        // --- 1. GREEN ROUTE DATA ---
        $greenPath = json_encode([
            [11.559207, 122.750817], [11.561838, 122.747854], [11.565250, 122.750610],
            [11.566475, 122.751907], [11.568253, 122.754027], [11.570669, 122.753973],
            [11.569562, 122.751172], [11.571085, 122.754011], [11.573055, 122.753803],
            [11.575570, 122.753158], [11.576993, 122.752932], [11.577528, 122.752919],
            [11.577506, 122.753450], [11.577465, 122.754545], [11.577375, 122.755671],
            [11.577594, 122.756280], [11.578637, 122.756392], [11.579846, 122.756470],
            [11.581419, 122.756556], [11.583739, 122.756653], [11.583693, 122.757348],
            [11.583543, 122.760563], [11.584374, 122.769649], [11.585175, 122.774847],
            [11.585504, 122.777546]
        ]);

        $greenRoute = Route::create([
            'name' => 'PdP Green Jeep Route',
            'description' => 'Terminal to Tanza Loop',
            'origin' => 'RCITT Terminal',
            'destination' => 'Tanza Welcome Arc',
            'origin_lat' => 11.559207,
            'origin_lng' => 122.750817,
            'destination_lat' => 11.585504,
            'destination_lng' => 122.777546,
            'color' => '#00b894', // Green
            'distance' => 5.2,
            'path_data' => $greenPath
        ]);

        // Stops for Green Route (Added order_index to match sequence)
        Stop::create([
            'route_id' => $greenRoute->id, 
            'route_name' => 'Green Route', 
            'name' => 'RCITT Terminal', 
            'latitude' => 11.559207, 'longitude' => 122.750817, 
            'lat' => 11.559207, 'lng' => 122.750817, 
            'sequence' => 1, 'order_index' => 1
        ]);
        Stop::create([
            'route_id' => $greenRoute->id, 
            'route_name' => 'Green Route', 
            'name' => 'Pueblo de Panay', 
            'latitude' => 11.566475, 'longitude' => 122.751907, 
            'lat' => 11.566475, 'lng' => 122.751907, 
            'sequence' => 2, 'order_index' => 2
        ]);
        Stop::create([
            'route_id' => $greenRoute->id, 
            'route_name' => 'Green Route', 
            'name' => 'Filamer / Capiz Doctors', 
            'latitude' => 11.575570, 'longitude' => 122.753158, 
            'lat' => 11.575570, 'lng' => 122.753158, 
            'sequence' => 3, 'order_index' => 3
        ]);
        Stop::create([
            'route_id' => $greenRoute->id, 
            'route_name' => 'Green Route', 
            'name' => 'Capiz Provincial Capitol', 
            'latitude' => 11.584374, 'longitude' => 122.769649, 
            'lat' => 11.584374, 'lng' => 122.769649, 
            'sequence' => 4, 'order_index' => 4
        ]);
        Stop::create([
            'route_id' => $greenRoute->id, 
            'route_name' => 'Green Route', 
            'name' => 'Villareal Stadium', 
            'latitude' => 11.576415, 'longitude' => 122.758533, 
            'lat' => 11.576415, 'lng' => 122.758533, 
            'sequence' => 5, 'order_index' => 5
        ]);
        Stop::create([
            'route_id' => $greenRoute->id, 
            'route_name' => 'Green Route', 
            'name' => 'Tanza Welcome Arc', 
            'latitude' => 11.585504, 'longitude' => 122.777546, 
            'lat' => 11.585504, 'lng' => 122.777546, 
            'sequence' => 6, 'order_index' => 6
        ]);


        // --- 2. RED ROUTE DATA ---
        $redPath = json_encode([
            [11.559207, 122.750817], [11.561838, 122.747854], [11.565250, 122.750610],
            [11.566475, 122.751907], [11.568253, 122.754027], [11.569172, 122.754163],
            [11.571975, 122.753902], [11.574869, 122.753562], [11.574498, 122.752339],
            [11.573985, 122.748355], [11.576025, 122.746532], [11.578339, 122.746519],
            [11.580183, 122.747048], [11.582842, 122.747390], [11.582912, 122.749810],
            [11.583036, 122.752298], [11.584754, 122.752206], [11.589697, 122.752243],
            [11.592198, 122.751694], [11.596416, 122.748605], [11.598791, 122.745879],
            [11.603142, 122.740784], [11.606579, 122.736620], [11.605087, 122.710023]
        ]);

        $redRoute = Route::create([
            'name' => 'PdP Red Jeep Route',
            'description' => 'Terminal to Culasi Loop',
            'origin' => 'RCITT Terminal',
            'destination' => 'Culasi Terminal',
            'origin_lat' => 11.559207,
            'origin_lng' => 122.750817,
            'destination_lat' => 11.605087,
            'destination_lng' => 122.710023,
            'color' => '#ff7675', // Red
            'distance' => 8.5,
            'path_data' => $redPath
        ]);

        // Stops for Red Route
        Stop::create([
            'route_id' => $redRoute->id, 
            'route_name' => 'Red Route', 
            'name' => 'Roxas City Hall / Plaza', 
            'latitude' => 11.583036, 'longitude' => 122.752298, 
            'lat' => 11.583036, 'lng' => 122.752298, 
            'sequence' => 1, 'order_index' => 1
        ]);
        Stop::create([
            'route_id' => $redRoute->id, 
            'route_name' => 'Red Route', 
            'name' => 'Gaisano Grand / CityMall', 
            'latitude' => 11.589697, 'longitude' => 122.752243, 
            'lat' => 11.589697, 'lng' => 122.752243, 
            'sequence' => 2, 'order_index' => 2
        ]);
        Stop::create([
            'route_id' => $redRoute->id, 
            'route_name' => 'Red Route', 
            'name' => 'SM City Roxas', 
            'latitude' => 11.596416, 'longitude' => 122.748605, 
            'lat' => 11.596416, 'lng' => 122.748605, 
            'sequence' => 3, 'order_index' => 3
        ]);
        Stop::create([
            'route_id' => $redRoute->id, 
            'route_name' => 'Red Route', 
            'name' => 'Roxas Airport', 
            'latitude' => 11.598791, 'longitude' => 122.745879, 
            'lat' => 11.598791, 'lng' => 122.745879, 
            'sequence' => 4, 'order_index' => 4
        ]);
        Stop::create([
            'route_id' => $redRoute->id, 
            'route_name' => 'Red Route', 
            'name' => 'Peoples Park (Baybay)', 
            'latitude' => 11.606579, 'longitude' => 122.736620, 
            'lat' => 11.606579, 'lng' => 122.736620, 
            'sequence' => 5, 'order_index' => 5
        ]);
        Stop::create([
            'route_id' => $redRoute->id, 
            'route_name' => 'Red Route', 
            'name' => 'Culasi Terminal', 
            'latitude' => 11.605087, 'longitude' => 122.710023, 
            'lat' => 11.605087, 'lng' => 122.710023, 
            'sequence' => 6, 'order_index' => 6
        ]);


        // --- 3. BLUE ROUTE DATA ---
        $bluePath = json_encode([
            [11.559207, 122.750817], [11.561838, 122.747854], [11.565250, 122.750610],
            [11.566475, 122.751907], [11.568253, 122.754027], [11.569172, 122.754163],
            [11.571975, 122.753902], [11.574869, 122.753562], [11.574498, 122.752339],
            [11.573985, 122.748355], [11.576025, 122.746532], [11.578339, 122.746519],
            [11.580183, 122.747048], [11.581587, 122.747462], [11.582838, 122.747355],
            [11.584100, 122.746456], [11.585128, 122.745626], [11.584992, 122.741716],
            [11.585412, 122.738916], [11.586328, 122.736174], [11.586945, 122.732922],
            [11.588240, 122.729903], [11.589879, 122.726653], [11.591211, 122.723769]
        ]);

        $blueRoute = Route::create([
            'name' => 'PdP Blue Jeep Route',
            'description' => 'Terminal to Libas',
            'origin' => 'RCITT Terminal',
            'destination' => 'Libas Terminal',
            'origin_lat' => 11.559207,
            'origin_lng' => 122.750817,
            'destination_lat' => 11.591211,
            'destination_lng' => 122.723769,
            'color' => '#0984e3', // Blue
            'distance' => 6.1,
            'path_data' => $bluePath
        ]);

        // Stops for Blue Route
        Stop::create([
            'route_id' => $blueRoute->id, 
            'route_name' => 'Blue Route', 
            'name' => 'Robinsons Place Hub', 
            'latitude' => 11.569562, 'longitude' => 122.751172, 
            'lat' => 11.569562, 'lng' => 122.751172, 
            'sequence' => 1, 'order_index' => 1
        ]);
        Stop::create([
            'route_id' => $blueRoute->id, 
            'route_name' => 'Blue Route', 
            'name' => 'Libas Terminal', 
            'latitude' => 11.591211, 'longitude' => 122.723769, 
            'lat' => 11.591211, 'lng' => 122.723769, 
            'sequence' => 2, 'order_index' => 2
        ]);
    }
}