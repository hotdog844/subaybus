<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\GpsLog;
use Illuminate\Http\Request;

class BusLocationController extends Controller
{
    public function index()
    {
        // 1. GET BUSES
        // We load 'route', 'driver', and 'fareMatrix' to avoid errors
        $buses = Bus::with(['route', 'driver', 'fareMatrix'])
                    ->whereIn('status', [
                        'active', 'on_route', 'On Route', 'on route',
                        'full', 
                        'standby', 'at terminal', 
                        'maintenance', 
                        'offline'
                    ]) 
                    ->get();

        // 2. PROCESS LOCATIONS
        $data = $buses->map(function($bus) {
            
            // --- A. CONFIGURATION ---
            
            // 1. Specific Terminals (Red/Blue at Pueblo, UV at Crossing)
            $terminals = [
                'red'  => ['lat' => 11.55974, 'lng' => 122.75155], // Pueblo Terminal
                'blue' => ['lat' => 11.55974, 'lng' => 122.75155], // Pueblo Terminal
                'uv'   => ['lat' => 11.56000, 'lng' => 122.76000], // Crossing
            ];

            // 2. Default "Parking Lot" (Roxas City Plaza)
            $defaultLat = 11.5853; 
            $defaultLng = 122.7511;

            // Initialize variables
            $lat = $defaultLat;
            $lng = $defaultLng;

            // --- B. LOGIC ---

            // CASE 1: REAL GPS DEVICE (Green Bus)
            if ($bus->device_id === '9176466392' || $bus->bus_number === '9176466392') {
                $latestGps = GpsLog::where('device_id', '9176466392')->latest()->first();
                if ($latestGps) {
                    $lat = $latestGps->latitude;
                    $lng = $latestGps->longitude;
                }
            } 
            
            // CASE 2: SIMULATION BUSES (Red, Blue, UV)
            elseif (str_starts_with($bus->bus_number, 'SIM')) {
                
                // Identify Type
                $type = 'red'; // default fallback
                if (str_contains($bus->bus_number, 'BLU')) $type = 'blue';
                if (str_contains($bus->bus_number, 'UV'))  $type = 'uv';

                // Check Status
                if (in_array(strtolower($bus->status), ['standby', 'at terminal', 'offline'])) {
                    // Snap to their specific terminal
                    $lat = $terminals[$type]['lat'];
                    $lng = $terminals[$type]['lng'];
                } else {
                    // Move randomly AROUND their terminal
                    $baseLat = $terminals[$type]['lat'];
                    $baseLng = $terminals[$type]['lng'];
                    $lat = $baseLat + (rand(-50, 50) / 8000); 
                    $lng = $baseLng + (rand(-50, 50) / 8000);
                }
            }

            // CASE 3: BRAND NEW / GENERIC BUSES (The Scatter Logic)
            else {
                // Fixed Scatter
                $offset = $bus->id * 0.0003; 
                $lat = $defaultLat + $offset; 
                $lng = $defaultLng + $offset; 
            }

            // --- C. RETURN DATA ---
            return [
                'id' => $bus->id,
                'bus_number' => $bus->bus_number,
                'plate_number' => $bus->plate_number,
                'lat' => (float)$lat,
                'lng' => (float)$lng,
                'status' => $bus->status,
                'passenger_count' => $bus->passengers ?? 0, 
                // Fix: Check if driver exists before accessing name
                'driver_name' => $bus->driver ? $bus->driver->name : 'No Driver Assigned',
                'route' => $bus->route ? [
                    'id' => $bus->route->id,
                    'name' => $bus->route->name,
                    'color' => $bus->route->color
                ] : null,

                // --- FARE DATA ---
                'fare' => $bus->fareMatrix ? [
                    'name' => $bus->fareMatrix->name,     
                    'base_price' => $bus->fareMatrix->base_fare, 
                ] : null,
            ];
        });

        return response()->json($data);
    }

    public function update(Request $request)
    {
        return response()->json(['message' => 'GPS Endpoint Active']);
    }
}