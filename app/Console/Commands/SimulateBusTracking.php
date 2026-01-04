<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimulateBusTracking extends Command
{
    // COMMAND NAME
    protected $signature = 'track:simulate';
    protected $description = 'Simulates buses driving smoothly along real database routes.';

    public function handle()
    {
        $this->info("🚀 Starting Smart Bus Simulation...");
        $this->info("Press Ctrl+C to stop.");

        // CONFIGURATION: Speed of simulation
        // Lower = Smoother but slower updates
        $stepSize = 0.00015; // Movement per 'tick' (approx 15-20 meters)

        // LOAD REAL ROUTES FROM DB
        // We group stops by route name so buses follow their specific path
        $routes = [
            'Green' => DB::table('stops')->where('route_name', 'like', '%Green%')->orderBy('order_index')->get(),
            'Red'   => DB::table('stops')->where('route_name', 'like', '%Red%')->orderBy('order_index')->get(),
            'Blue'  => DB::table('stops')->where('route_name', 'like', '%Blue%')->orderBy('order_index')->get(),
            'UV'    => DB::table('stops')->where('route_name', 'like', '%UV%')->orderBy('order_index')->get(),
        ];

        // INITIALIZE BUS STATE
        // We keep track of where each bus is exactly
        $busStates = [
            'Green' => ['current_stop_idx' => 0, 'progress' => 0, 'lat' => 0, 'lng' => 0, 'plate' => 'SIM-GRN'],
            'Red'   => ['current_stop_idx' => 0, 'progress' => 0, 'lat' => 0, 'lng' => 0, 'plate' => 'SIM-RED'],
            'Blue'  => ['current_stop_idx' => 0, 'progress' => 0, 'lat' => 0, 'lng' => 0, 'plate' => 'SIM-BLU'],
            'UV'    => ['current_stop_idx' => 0, 'progress' => 0, 'lat' => 0, 'lng' => 0, 'plate' => 'SIM-UV'],
        ];

        // INFINITE LOOP
        while (true) {
            foreach ($busStates as $routeKey => &$state) {
                
                // Get the stops for this route
                $stops = $routes[$routeKey];
                if ($stops->count() < 2) continue; // Need at least 2 stops to move

                // Identify Start and End of current segment
                $startNode = $stops[$state['current_stop_idx']];
                
                // Logic to find next stop (Loop back to 0 if at end)
                $nextIdx = $state['current_stop_idx'] + 1;
                if ($nextIdx >= $stops->count()) {
                    $nextIdx = 0; // Loop back to start
                    // Optional: Pause at terminal?
                }
                $endNode = $stops[$nextIdx];

                // CALCULATE MOVEMENT (Linear Interpolation)
                // We move 'progress' from 0.0 to 1.0 betwen Start and End
                
                // Calculate distance to ensure consistent speed
                $distLat = $endNode->lat - $startNode->lat;
                $distLng = $endNode->lng - $startNode->lng;
                $totalDist = sqrt(($distLat**2) + ($distLng**2));
                
                // Increment progress
                // If distance is long, step is smaller percentage. If short, larger percentage.
                $stepPercentage = $stepSize / ($totalDist > 0 ? $totalDist : 1);
                $state['progress'] += $stepPercentage;

                // CHECK IF ARRIVED AT NEXT STOP
                if ($state['progress'] >= 1.0) {
                    $state['progress'] = 0; // Reset for next segment
                    $state['current_stop_idx'] = $nextIdx; // Officially reached next stop
                    
                    // Snap exactly to the node to prevent drift
                    $state['lat'] = $endNode->lat;
                    $state['lng'] = $endNode->lng;
                } else {
                    // STILL MOVING BETWEEN STOPS
                    $state['lat'] = $startNode->lat + ($distLat * $state['progress']);
                    $state['lng'] = $startNode->lng + ($distLng * $state['progress']);
                }

                // UPDATE DATABASE
                // We update or create the bus record
                $busNumber = "PdP $routeKey Route";
                // Special case for Green if you want it to simulate the Real Bus (Bus 1)
                // If you are testing hardware, rename this. If simulating, keep it.
                $dbId = match($routeKey) {
                    'Green' => 1, // Simulates the Main Bus
                    'Red' => 2,
                    'Blue' => 3,
                    'UV' => 4,
                    default => 99
                };

                DB::table('buses')->updateOrInsert(
                    ['id' => $dbId],
                    [
                        'bus_number' => $busNumber,
                        'plate_number' => $state['plate'],
                        'lat' => $state['lat'],
                        'lng' => $state['lng'],
                        'status' => 'active',
                        'updated_at' => Carbon::now(),
                    ]
                );

                $this->info("🚌 $routeKey Bus: Moving towards " . $endNode->name);
            }

            // WAIT (Simulate Tick Rate)
            // 0.5 seconds sleep = smooth updates
            usleep(500000); 
        }
    }
}