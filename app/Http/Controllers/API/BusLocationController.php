<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Route as BusRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Make sure to import DB

class BusLocationController extends Controller
{
    /**
     * Provides a list of all defined routes for the filter pills.
     */
    public function getRoutes()
    {
        $routes = BusRoute::select('id', 'name')->get();
        return response()->json($routes);
    }

    /**
     * A powerful search endpoint for the homepage and terminal view.
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');
        $routeFilter = $request->input('route_id');

        $busesQuery = Bus::with('driver', 'route')
            // This is the main correction: We JOIN with the locations table.
            ->join('locations', 'buses.id', '=', 'locations.bus_id')
            ->whereIn('buses.status', ['on route', 'at terminal'])
            // We also need to get the latest location for each bus.
            ->whereIn('locations.id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                      ->from('locations')
                      ->groupBy('bus_id');
            });

        if (!empty($query)) {
            $busesQuery->where(function ($q) use ($query) {
                $q->where('buses.plate_number', 'LIKE', "%{$query}%")
                  ->orWhereHas('driver', function ($driverQuery) use ($query) {
                      $driverQuery->where('name', 'LIKE', "%{$query}%");
                  })
                  ->orWhereHas('route', function ($routeQuery) use ($query) {
                      $routeQuery->where('name', 'LIKE', "%{$query}%")
                                 ->orWhere('description', 'LIKE', "%{$query}%");
                  });
            });
        }

        if (!empty($routeFilter)) {
            $busesQuery->where('buses.route_id', $routeFilter);
        }
        
        // Select all the necessary columns from both tables
        $buses = $busesQuery->select('buses.*', 'locations.latitude', 'locations.longitude')->get();
        
        return response()->json($buses);
    }

    /**
     * Update the location of a specific bus from a GPS device.
     */
    public function update(Request $request)
    {
        // This part is for the future GPS device and remains correct.
        // It correctly inserts a new row into the 'locations' table.
        $apiKey = $request->header('X-API-KEY');
        if ($apiKey !== env('DEVICE_API_KEY', 'YOUR_SECRET_API_KEY')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'bus_id' => 'required|integer|exists:buses,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        
        // This correctly creates a new location record.
        DB::table('locations')->insert([
            'bus_id' => $validated['bus_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // This correctly updates the bus status.
        Bus::find($validated['bus_id'])->update([
            'status' => 'on route',
            'last_seen' => now(),
        ]);

        return response()->json(['message' => 'Location updated successfully!']);
    }
}