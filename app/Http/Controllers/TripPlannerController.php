<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Stop;
use App\Models\Route as BusRoute;

class TripPlannerController extends Controller
{
    public function plan(Request $request)
    {
        // 1. Get Coordinates (Start & End)
        // Pass these from your React App
        $startLat = $request->input('start_lat');
        $startLng = $request->input('start_lng');
        $destLat  = $request->input('dest_lat');
        $destLng  = $request->input('dest_lng');

        // Validation
        if (!$startLat || !$destLat) {
            return response()->json(['error' => 'Missing location data'], 400);
        }

        // 2. Find Closest Stop to Origin (Using 'lat' and 'lng' columns)
        $startStop = Stop::select('*', DB::raw("
            ( 6371 * acos( cos( radians($startLat) ) 
            * cos( radians( lat ) ) 
            * cos( radians( lng ) - radians($startLng) ) 
            + sin( radians($startLat) ) 
            * sin( radians( lat ) ) ) ) AS distance"))
            ->having('distance', '<', 1) // Within 1km
            ->orderBy('distance')
            ->first();

        // 3. Find Closest Stop to Destination
        $endStop = Stop::select('*', DB::raw("
            ( 6371 * acos( cos( radians($destLat) ) 
            * cos( radians( lat ) ) 
            * cos( radians( lng ) - radians($destLng) ) 
            + sin( radians($destLat) ) 
            * sin( radians( lat ) ) ) ) AS distance"))
            ->having('distance', '<', 1) // Within 1km
            ->orderBy('distance')
            ->first();

        // 4. Check if stops were found
        if (!$startStop) {
            return response()->json(['message' => 'You are too far from any bus stop.'], 404);
        }
        if (!$endStop) {
            return response()->json(['message' => 'No bus stops near your destination.'], 404);
        }

        // 5. THE MATCHING LOGIC
        // Check if both stops belong to the same route
        if ($startStop->route_id == $endStop->route_id) {
            
            $route = BusRoute::find($startStop->route_id);

            return response()->json([
                'success' => true,
                'data' => [
                    [
                        'route_id' => $route->id,
                        'name' => $route->name,     // e.g., "PdP Green Route"
                        'color' => $route->color,   // e.g., "#00b894"
                        'eta' => '15 min',          // Hardcoded for defense
                        'price' => '25.00',         // Hardcoded for defense
                        'start_stop' => $startStop->name,
                        'end_stop' => $endStop->name
                    ]
                ]
            ]);
        }

        return response()->json(['message' => 'No direct bus route available.'], 404);
    }
}