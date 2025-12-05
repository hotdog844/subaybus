<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusStopSeeder extends Seeder
{
    public function run()
    {
        $stops = [
            ['name' => 'Integrated Transport Terminal', 'lat' => 11.5700, 'lng' => 122.7600, 'desc' => 'Main Terminal'],
            ['name' => 'Roxas City Plaza', 'lat' => 11.5853, 'lng' => 122.7511, 'desc' => 'City Center'],
            ['name' => 'Gaisano Marketplace', 'lat' => 11.5820, 'lng' => 122.7550, 'desc' => 'Shopping Mall'],
            ['name' => 'Robinsons Place', 'lat' => 11.5750, 'lng' => 122.7650, 'desc' => 'Mall Entrance'],
            ['name' => 'Capiz State University', 'lat' => 11.5900, 'lng' => 122.7480, 'desc' => 'Main Gate'],
            ['name' => 'St. Anthony Hospital', 'lat' => 11.5880, 'lng' => 122.7500, 'desc' => 'Emergency Entrance'],
            ['name' => 'City Hall', 'lat' => 11.5840, 'lng' => 122.7520, 'desc' => 'Government Center'],
            ['name' => 'Airport Terminal', 'lat' => 11.5950, 'lng' => 122.7550, 'desc' => 'Arrivals Area'],
        ];

        foreach ($stops as $stop) {
            DB::table('bus_stops')->insertOrIgnore([
                'name' => $stop['name'],
                'latitude' => $stop['lat'],
                'longitude' => $stop['lng'],
                'location_description' => $stop['desc'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}