<?php

namespace App\Http\Controllers\Admin; // <--- NOTICE: Namespace is Admin, not Api

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    // 1. Show the list of routes (HTML Page)
    public function index()
    {
        $routes = Route::all();
        return view('admin.routes.index', compact('routes'));
    }

    // 2. Show the "Create New Route" form
    public function create()
    {
        return view('admin.routes.create');
    }

    // 3. Save a new route to the database
    public function store(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|string|max:255',       // Text Name (e.g., "City Hall")
            'destination' => 'required|string|max:255',  // Text Name (e.g., "Airport")
            'path_data' => 'nullable|json',              // The Blue Line
            'distance' => 'nullable|numeric',            // Calculated Distance
            // Coordinates are optional but good to have validation
            'origin_lat' => 'nullable|numeric',
            'origin_lng' => 'nullable|numeric',
            'destination_lat' => 'nullable|numeric',
            'destination_lng' => 'nullable|numeric',
        ]);

        // 2. Create the Route
        Route::create([
            'name' => $request->name,
            'description' => $request->description,
            'origin' => $request->origin,               // Saving the NAME
            'destination' => $request->destination,     // Saving the NAME
            'origin_lat' => $request->origin_lat,       // Saving Coordinate
            'origin_lng' => $request->origin_lng,       // Saving Coordinate
            'destination_lat' => $request->destination_lat, // Saving Coordinate
            'destination_lng' => $request->destination_lng, // Saving Coordinate
            'path_data' => $request->path_data,
            'distance' => $request->distance ?? 0.00,
        ]);

        return redirect()->route('admin.routes.index')->with('success', 'Route created successfully!');
    }

    // 4. Show the "Edit Route" form
    public function edit(Route $route)
    {
        return view('admin.routes.edit', compact('route'));
    }

    // 5. Update the route (This saves your Map Coordinates!)
    public function update(Request $request, $id)
    {
        // 1. Find the existing route (Don't create a new one!)
        $route = Route::findOrFail($id);

        // 2. Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|string',
            'destination' => 'required|string',
            // Coordinates are optional but good to validate if present
            'origin_lat' => 'nullable|numeric',
            'origin_lng' => 'nullable|numeric',
            'destination_lat' => 'nullable|numeric',
            'destination_lng' => 'nullable|numeric',
            'path_data' => 'nullable', // The blue line data
            'distance' => 'nullable'
        ]);

        // 3. Update the existing record
        $route->update([
            'name' => $request->name,
            'description' => $request->description,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'origin_lat' => $request->origin_lat,
            'origin_lng' => $request->origin_lng,
            'destination_lat' => $request->destination_lat,
            'destination_lng' => $request->destination_lng,
            'path_data' => $request->path_data,
            'distance' => $request->distance,
        ]);

        return redirect()->route('admin.routes.index')->with('success', 'Route updated successfully!');
    }

    // 6. Delete a route
    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('admin.routes.index')->with('success', 'Route deleted successfully.');
    }
}