<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\Stop;

class StopController extends Controller
{
    // Show the Map & List of Stops
    public function index($route_id)
    {
        $route = Route::findOrFail($route_id);
        
        // Get existing stops ordered by sequence
        $stops = Stop::where('route_id', $route_id)->orderBy('sequence', 'asc')->get();

        return view('admin.routes.stops.index', compact('route', 'stops'));
    }

    // Save a new Stop
    public function store(Request $request, $route_id)
    {
        // 1. Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // 2. Get Route Name
        $route = Route::findOrFail($route_id);

        // 3. Calculate Sequence
        // We check 'order_index' just in case 'sequence' is empty in old rows
        $maxSequence = Stop::where('route_id', $route_id)->max('sequence') 
                       ?? Stop::where('route_id', $route_id)->max('order_index');
                       
        $nextSequence = $maxSequence ? $maxSequence + 1 : 1;

        // 4. Create Stop (Filling ALL required columns)
        Stop::create([
            'route_id' => $route_id,
            'route_name' => $route->name,
            'name' => $request->name,
            
            // New Columns
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'sequence' => $nextSequence,

            // Legacy Columns (Duplicates to stop errors)
            'lat' => $request->latitude, 
            'lng' => $request->longitude,
            'order_index' => $nextSequence // <--- SAVING THE MISSING FIELD
        ]);

        return redirect()->back()->with('success', 'Stop added successfully!');
    }

    // Delete a Stop
    public function destroy($stop_id)
    {
        $stop = Stop::findOrFail($stop_id);
        $stop->delete();

        return redirect()->back()->with('success', 'Stop removed.');
    }
}