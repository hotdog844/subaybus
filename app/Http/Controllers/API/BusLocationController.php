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
    // Eager load everything to pull the Admin-assigned data
    $buses = Bus::with(['route', 'driver', 'fareMatrix'])
                ->whereIn('status', ['active', 'on_route', 'full', 'standby']) 
                ->get();

    $data = $buses->map(function($bus) {
        // ... (Keep your existing GPS coordinate logic here) ...

        return [
    'id' => $bus->id,
    'plate_number' => $bus->plate_number,
    'lat' => (float)$lat,
    'lng' => (float)$lng,
    'driver_name' => $bus->driver ? $bus->driver->name : 'No Driver',
    'fare' => $bus->fareMatrix ? (float)$bus->fareMatrix->base_fare : 0.00, 
    'route' => $bus->route ? [
        'origin_lat' => $bus->route->origin_lat,
        'origin_lng' => $bus->route->origin_lng,
        'dest_lat' => $bus->route->destination_lat, // Fixed name
        'dest_lng' => $bus->route->destination_lng, // Fixed name
    ] : null,
];
    });

    return response()->json($data);
}
}