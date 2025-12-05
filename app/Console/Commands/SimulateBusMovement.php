<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimulateBusMovement extends Command
{
    // The name of the command we will run in the terminal
    protected $signature = 'track:simulate';
    protected $description = 'Simulates movement for Ghost Buses (Bus 42 and Bus 10)';

    public function handle()
    {
        $this->info("🚌 Starting Ghost Driver Simulation... (Press Ctrl+C to stop)");

        // 1. Define a simple circular route for Bus 42 (Downtown Loop)
        // These are coordinates around Roxas City Plaza
        $route42 = [
            [11.5853, 122.7511], // Plaza
            [11.5860, 122.7520],
            [11.5865, 122.7530],
            [11.5870, 122.7540],
            [11.5860, 122.7550], // Gaisano
            [11.5850, 122.7540],
            [11.5840, 122.7530],
            [11.5853, 122.7511], // Back to Start
        ];

        // 2. Define a route for Bus 10 (Terminal Loop)
        $route10 = [
            [11.5700, 122.7600], // Terminal
            [11.5710, 122.7610],
            [11.5720, 122.7620],
            [11.5730, 122.7610],
            [11.5720, 122.7600],
            [11.5700, 122.7600], // Back to Start
        ];

        $step42 = 0;
        $step10 = 0;

        // Infinite Loop
        while (true) {
            // --- Update Bus 42 (GHOST A) ---
            $coord42 = $route42[$step42];
            $this->updateLocation('SIM_GHOST_A', $coord42[0], $coord42[1]);
            
            // Move to next step (loop back if at end)
            $step42 = ($step42 + 1) % count($route42);

            // --- Update Bus 10 (GHOST B) ---
            $coord10 = $route10[$step10];
            $this->updateLocation('SIM_GHOST_B', $coord10[0], $coord10[1]);
            
            $step10 = ($step10 + 1) % count($route10);

            $this->comment("Updated coordinates at " . Carbon::now()->toTimeString());
            
            // Wait 2 seconds before next move
            sleep(2);
        }
    }

    private function updateLocation($imei, $lat, $lng)
    {
        // Insert new ping into gps_data table
        DB::table('gps_data')->insert([
            'imei' => $imei,
            'lat' => $lat,
            'lng' => $lng,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}