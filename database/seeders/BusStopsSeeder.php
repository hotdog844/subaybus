<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;

class BusStopsSeeder extends Seeder
{
    public function run()
    {
        // Linisin muna ang lumang stops para hindi mag-duplicate
        // Optional: Kung gusto mong burahin ang luma bago mag-seed, pwede mong i-uncomment ito:
        // \App\Models\Stop::truncate(); 

        $this->seedGreenRoute();
        $this->seedRedRoute();
        $this->seedBlueRoute();
        $this->seedPontevedraRoute();
    }

    // ==========================================
    // 1. PdP GREEN ROUTE (Strategically Reduced)
    // ==========================================
    private function seedGreenRoute()
    {
        $route = Route::where('name', 'LIKE', '%Green%')->first();
        if (!$route) { echo "⚠️ Green Route not found.\n"; return; }

        // Logic: Tinira ko ang Malls, Schools, Hospitals, at Major Junctions lang.
        $stops = [
            ["name" => "Roxas City Integrated Terminal", "lat" => 11.559207, "lng" => 122.750817],
            ["name" => "Pueblo de Panay Hub", "lat" => 11.565250, "lng" => 122.750610],
            ["name" => "Robinsons Place Roxas", "lat" => 11.569562, "lng" => 122.751172],
            ["name" => "Metro Roxas Water District", "lat" => 11.573055, "lng" => 122.753803],
            ["name" => "Filamer Christian University", "lat" => 11.575570, "lng" => 122.753158],
            ["name" => "CityMall Roxas", "lat" => 11.576993, "lng" => 122.752932],
            ["name" => "Capiz Doctors Hospital", "lat" => 11.577465, "lng" => 122.754545],
            ["name" => "Capiz State University (CAPSU)", "lat" => 11.577594, "lng" => 122.756280],
            ["name" => "Capiz Bridge / Rotonda", "lat" => 11.581419, "lng" => 122.756556],
            ["name" => "Kapis Mansion (Banica)", "lat" => 11.584374, "lng" => 122.769649],
            ["name" => "Brgy. Tanza Welcome Arc", "lat" => 11.585504, "lng" => 122.777546],
        ];

        // I-reset ang stops ng route na ito
        $route->stops = $stops; 
        $route->save();
        echo "✅ Green Route Optimized! (" . count($stops) . " key stops)\n";
    }

    // ==========================================
    // 2. PdP RED ROUTE (Major Landmarks Only)
    // ==========================================
    private function seedRedRoute()
    {
        $route = Route::where('name', 'LIKE', '%Red%')->first();
        if (!$route) { echo "⚠️ Red Route not found.\n"; return; }

        // Logic: Tinanggal ang mga dikit-dikit na stops sa Roxas Ave.
        $stops = [
            ["name" => "Roxas City Integrated Terminal", "lat" => 11.559207, "lng" => 122.750817],
            ["name" => "Pueblo de Panay Entrance", "lat" => 11.561838, "lng" => 122.747854],
            ["name" => "Hercor College", "lat" => 11.569172, "lng" => 122.754163],
            ["name" => "Roxas City Hall / Plaza", "lat" => 11.583036, "lng" => 122.752298],
            ["name" => "Gaisano Grand Mall", "lat" => 11.589697, "lng" => 122.752243],
            ["name" => "CityMall Arnaldo", "lat" => 11.592198, "lng" => 122.751694],
            ["name" => "SM City Roxas", "lat" => 11.596416, "lng" => 122.748605],
            ["name" => "Roxas City Airport", "lat" => 11.598791, "lng" => 122.745879],
            ["name" => "Bangko Sentral (BSP)", "lat" => 11.603142, "lng" => 122.740784],
            ["name" => "People's Park (Baybay)", "lat" => 11.606579, "lng" => 122.736620],
            ["name" => "San Antonio Resort", "lat" => 11.606725, "lng" => 122.731165],
            ["name" => "Seafood Court (Baybay)", "lat" => 11.607763, "lng" => 122.719174],
            ["name" => "Brgy. Culasi", "lat" => 11.605087, "lng" => 122.710023],
        ];

        $route->stops = $stops;
        $route->save();
        echo "✅ Red Route Optimized! (" . count($stops) . " key stops)\n";
    }

    // ==========================================
    // 3. PdP BLUE ROUTE (Strategic Points)
    // ==========================================
    private function seedBlueRoute()
    {
        $route = Route::where('name', 'LIKE', '%Blue%')->first();
        if (!$route) { echo "⚠️ Blue Route not found.\n"; return; }

        $stops = [
            ["name" => "Roxas City Integrated Terminal", "lat" => 11.559207, "lng" => 122.750817],
            ["name" => "Pueblo de Panay", "lat" => 11.565250, "lng" => 122.750610],
            ["name" => "Hercor College", "lat" => 11.569172, "lng" => 122.754163],
            // Tinanggal ang intermediate Roxas Ave stops dahil covered na ng Green/Red
            ["name" => "Roxas City Bridge", "lat" => 11.580183, "lng" => 122.747048],
            ["name" => "PhilHealth / Capelco", "lat" => 11.584100, "lng" => 122.746456],
            ["name" => "West Lake Villa", "lat" => 11.586328, "lng" => 122.736174],
            ["name" => "CAPSU Dayao", "lat" => 11.588240, "lng" => 122.729903],
            ["name" => "Brgy. Libas Plaza", "lat" => 11.591211, "lng" => 122.723769],
        ];

        $route->stops = $stops;
        $route->save();
        echo "✅ Blue Route Optimized! (" . count($stops) . " key stops)\n";
    }

    // ==========================================
    // 4. RCPUVTC (Cleaned Rural Route)
    // ==========================================
    private function seedPontevedraRoute()
    {
        $route = Route::where('name', 'LIKE', '%Pontevedra%')
                      ->orWhere('name', 'LIKE', '%RCPUVTC%')->first();
        if (!$route) { echo "⚠️ RCPUVTC Route not found.\n"; return; }

        // Logic: Tinanggal ang 15+ na "Capiz Road" points.
        // Nagtira lang ng landmark tulad ng Bridges, Schools, Churches, at Gas Stations.
        // Ito ay sapat na para sa long-distance bus stops.
        $stops = [
            ["name" => "RCPUVTC Terminal", "lat" => 11.583774, "lng" => 122.762378],
            ["name" => "Kapis Mansion", "lat" => 11.584434, "lng" => 122.769677],
            ["name" => "Banica Bridge", "lat" => 11.585321, "lng" => 122.775734],
            ["name" => "Tadjao Bridge", "lat" => 11.566814, "lng" => 122.794500],
            ["name" => "Tanza Elementary School", "lat" => 11.581743, "lng" => 122.786846],
            ["name" => "Paaralang Sentral ng Panay", "lat" => 11.556876, "lng" => 122.796553],
            ["name" => "Panay Church", "lat" => 11.555328, "lng" => 122.793976],
            ["name" => "Petron Panay", "lat" => 11.553557, "lng" => 122.789077],
            // Calitan Bridge retained for spacing
            ["name" => "Calitan Bridge", "lat" => 11.548287, "lng" => 122.788692], 
            // Anhawon Bridge retained for spacing
            ["name" => "Anhawon Bridge", "lat" => 11.535993, "lng" => 122.793188],
            // Agbalo School retained as major landmark
            ["name" => "Agbalo Elementary School", "lat" => 11.503802, "lng" => 122.810296],
            ["name" => "West Oil Gas Station", "lat" => 11.484621, "lng" => 122.831458],
            ["name" => "Guirnella Terminal (Pontevedra)", "lat" => 11.481041, "lng" => 122.834162],
        ];

        $route->stops = $stops;
        $route->save();
        echo "✅ RCPUVTC Route Optimized! (" . count($stops) . " key stops)\n";
    }
}