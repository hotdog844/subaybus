<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Location;

class BusTrackingController extends Controller
{
    // POST: /update-location
    public function updateLocation(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Find or create bus
        $bus = Bus::firstOrCreate(
            ['plate_number' => $request->plate_number]
        );

        // Save location
        $location = new Location([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $bus->locations()->save($location);

        return response()->json(['message' => 'Location updated']);
    }

    // GET: /buses
    public function getBusLocations()
    {
        $buses = Bus::with(['locations' => function ($query) {
            $query->latest()->limit(1); // only latest location
        }])->get();

        return response()->json($buses);
    }
}
