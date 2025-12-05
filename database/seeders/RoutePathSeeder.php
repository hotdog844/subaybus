<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutePathSeeder extends Seeder
{
    public function run()
    {
        // 1. Downtown Express (Green Path)
        $downtownPath = json_encode([
            [11.5853, 122.7511], // Plaza
            [11.5860, 122.7520],
            [11.5865, 122.7530],
            [11.5870, 122.7540],
            [11.5860, 122.7550], // Gaisano
            [11.5850, 122.7540],
            [11.5840, 122.7530],
            [11.5853, 122.7511], // Back to Start
        ]);

        DB::table('routes')->insertOrIgnore([
            'name' => 'Downtown Express',
            'color' => '#00b894', // Green
            'path_data' => $downtownPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. North Loop (Blue Path) - Dummy Line for visual variety
        $northPath = json_encode([
            [11.5700, 122.7600], // Terminal
            [11.5720, 122.7620],
            [11.5750, 122.7650], // Robinsons
        ]);

        DB::table('routes')->insertOrIgnore([
            'name' => 'North Loop',
            'color' => '#0984e3', // Blue
            'path_data' => $northPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}