<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LiveDemoSeeder extends Seeder
{
    public function run()
    {
        // --- 1. REAL BUS (Linked to your physical device) ---
        // REPLACE '1234567890' with the ACTUAL ID written on your ST-901 unit!
        $realImei = '9176466392'; 

        DB::table('buses')->insertOrIgnore([
            'bus_number' => 'Bus 01 (REAL)',
            'plate_number' => 'ABC-1234',
            'tracker_imei' => $realImei, // This connects to gps_data
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Initial Ping for Real Bus
        DB::table('gps_data')->insert([
            'imei' => $realImei,
            'lat' => 11.5853000,
            'lng' => 122.7511000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // --- 2. GHOST BUS A (Simulated) ---
        $ghostAImei = 'SIM_GHOST_A';

        DB::table('buses')->insertOrIgnore([
            'bus_number' => 'Bus 42 (GHOST)',
            'plate_number' => 'SIM-001',
            'tracker_imei' => $ghostAImei,
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Initial Ping for Ghost A
        DB::table('gps_data')->insert([
            'imei' => $ghostAImei,
            'lat' => 11.5820000,
            'lng' => 122.7550000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // --- 3. GHOST BUS B (Simulated) ---
        $ghostBImei = 'SIM_GHOST_B';

        DB::table('buses')->insertOrIgnore([
            'bus_number' => 'Bus 10 (GHOST)',
            'plate_number' => 'SIM-002',
            'tracker_imei' => $ghostBImei,
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Initial Ping for Ghost B
        DB::table('gps_data')->insert([
            'imei' => $ghostBImei,
            'lat' => 11.5700000,
            'lng' => 122.7600000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // --- 4. GHOST BUS C  ---
        $ghostCImei = 'SIM_GHOST_C';

        DB::table('buses')->insertOrIgnore([
            'bus_number' => 'UV Express',
            'plate_number' => 'SIM-003',
            'tracker_imei' => $ghostCImei,
            'status' => 'active',
            'current_load' => 8,
            'max_capacity' => 14, // UVs are smaller
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Initial Ping
        DB::table('gps_data')->insert([
            'imei' => $ghostCImei,
            'lat' => 11.5853,
            'lng' => 122.7511,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

// Update REAL Bus with load capacity
DB::table('buses')->where('tracker_imei', $realImei)->update([
    'current_load' => 15, // MEDIUM
    'max_capacity' => 18,
]);

// Update GHOST A with load capacity
DB::table('buses')->where('tracker_imei', $ghostAImei)->update([
    'current_load' => 11, // FULL
    'max_capacity' => 18,
]);

// Update GHOST B with load capacity
DB::table('buses')->where('tracker_imei', $ghostBImei)->update([
    'current_load' => 5, // LOW
    'max_capacity' => 18,
]);
    }
}