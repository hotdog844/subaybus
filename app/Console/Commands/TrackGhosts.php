<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bus;
use Illuminate\Support\Facades\DB;

class TrackGhosts extends Command
{
    protected $signature = 'track:ghosts';
    protected $description = 'Simulate movement for GHOST buses only (Respects Admin Settings)';

    public function handle()
    {
        $this->info("👻 Ghost Bus Simulation Started...");
        $this->info("🛡️  Protected IDs: 9176466392, SIM-GRN, 111");

        // 1. CONFIG: Your Real Device IDs (The script will NEVER touch these)
        $realBusIds = ['9176466392', 'SIM-GRN', '111']; 

        while (true) {
            // 2. FETCH only buses that are NOT real
            $ghostBuses = Bus::whereNotIn('plate_number', $realBusIds)->get();

            foreach ($ghostBuses as $bus) {
                
                // --- RULE 1: RESPECT ADMIN STATUS ---
                // If Admin says "Offline", the ghost bus sleeps.
                if (strtolower($bus->status) !== 'on route') {
                    continue; 
                }

                // --- RULE 2: MOVE IT ---
                // Randomly add/subtract 0.0001 (approx 10-15 meters)
                // This makes it look like it's driving around the area.
                $latChange = (rand(0, 1) ? 0.00015 : -0.00015); 
                $lngChange = (rand(0, 1) ? 0.00015 : -0.00015);

                $newLat = $bus->lat + $latChange;
                $newLng = $bus->lng + $lngChange;

                // --- RULE 3: SURGICAL SAVE ---
                // We update ONLY location and time. 
                // We DO NOT touch Route, Driver, or Status.
                DB::table('buses')
                    ->where('id', $bus->id)
                    ->update([
                        'lat' => $newLat,
                        'lng' => $newLng,
                        'last_seen' => now(),
                        'updated_at' => now(),
                    ]);
                
                $this->info("👻 Moved {$bus->plate_number} (Route: {$bus->route_id})");
            }

            sleep(3); // Updates every 3 seconds
        }
    }
}