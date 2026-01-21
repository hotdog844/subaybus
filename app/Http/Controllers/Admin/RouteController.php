<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::all();
        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('admin.routes.create');
    }

   // 3. STORE (Create New Route)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'origin_lat' => 'required|numeric',
            'origin_lng' => 'required|numeric',
            'destination_lat' => 'required|numeric',
            'destination_lng' => 'required|numeric',
        ]);

        // 1. Handle Blue Path
        $finalPathData = $request->path_data;
        if (empty($finalPathData)) {
            $generatedPath = $this->fetchRoutePath(
                $request->origin_lat, $request->origin_lng, 
                $request->destination_lat, $request->destination_lng
            );
            $finalPathData = json_encode($generatedPath);
        }

        // 2. Handle Red Pins (Stops) - ITO ANG KULANG MO KANINA
        $finalStopsData = null;
        if ($request->has('stops_json')) {
            $finalStopsData = json_decode($request->stops_json);
        }

        Route::create([
            'name' => $request->name,
            'description' => $request->description,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'origin_lat' => $request->origin_lat,
            'origin_lng' => $request->origin_lng,
            'destination_lat' => $request->destination_lat,
            'destination_lng' => $request->destination_lng,
            'distance' => $request->distance ?? 0,
            'color' => $request->color ?? '#3b82f6',
            'path_data' => $finalPathData,
            'stops' => $finalStopsData // <--- SAVE STOPS HERE
        ]);

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route created successfully!');
    }

    // --- IDAGDAG MO ITO (Ito ang nawawalang function) ---
    public function edit($id)
    {
        // Hanapin ang route sa database
        $route = Route::findOrFail($id);
        
        // Buksan ang Edit Page (kung saan andun ang Map at Red Pins)
        return view('admin.routes.edit', compact('route'));
    }

    // 5. UPDATE (Edit Existing Route)
    public function update(Request $request, $id)
    {
        $route = Route::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|string',
            'destination' => 'required|string',
        ]);

        // Prepare Data
        $data = [
            'name' => $request->name,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'color' => $request->color ?? $route->color,
            
            // --- ANG SOLUSYON SA ERROR ---
            // Kung walang distance na pinasa, gawin itong 0 para hindi mag-error
            'distance' => $request->distance ?? 0, 
            
            'origin_lat' => $request->origin_lat,
            'origin_lng' => $request->origin_lng,
            'destination_lat' => $request->destination_lat,
            'destination_lng' => $request->destination_lng,
        ];

        // Handle Blue Path
        if ($request->has('path_data') && !empty($request->path_data)) {
            $data['path_data'] = $request->path_data; 
        }

        // Handle Red Pins
        if ($request->has('stops_json')) {
            $data['stops'] = json_decode($request->stops_json);
        }

        $route->update($data); // SAVE

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route updated successfully!');
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('admin.routes.index')->with('success', 'Route deleted.');
    }

    private function fetchRoutePath($lat1, $lng1, $lat2, $lng2)
    {
        try {
            $url = "http://router.project-osrm.org/route/v1/driving/{$lng1},{$lat1};{$lng2},{$lat2}?overview=full&geometries=geojson";
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['routes'][0]['geometry']['coordinates'])) {
                    $rawCoords = $data['routes'][0]['geometry']['coordinates'];
                    return array_map(function($coord) {
                        return [$coord[1], $coord[0]];
                    }, $rawCoords);
                }
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }
}