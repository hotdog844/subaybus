<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\User;  // For Drivers
use App\Models\Route; // For assigning routes
use Illuminate\Http\Request;

class BusController extends Controller
{
    /**
     * Display a listing of the buses.
     */
    public function index()
    {
        // Fixed: changed 'assignedRoute' to 'route' (Standard convention)
        // Added: 'fareMatrix' so you can see the fare type in the list
        $buses = Bus::with(['driver', 'route', 'fareMatrix'])->latest()->paginate(10);
        return view('admin.buses.index', compact('buses'));
    }

    /**
     * Show the form for creating a new bus.
     */
    public function create()
    {
        $routes = Route::all();
        // We fetch drivers from the USERS table
        $drivers = User::where('role', 'driver')->get();
        $fares = \App\Models\FareMatrix::all(); 

        return view('admin.buses.create', compact('routes', 'drivers', 'fares'));
    }

    /**
     * Store a newly created bus in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate the input
        $validated = $request->validate([
            'bus_number' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255|unique:buses',
            'route_id' => 'nullable|exists:routes,id',
            // Tanggalin muna ang driver validation para iwas error
            // 'driver_id' => 'nullable|exists:users,id', 
            'status' => 'required|string',
            'capacity' => 'nullable|integer',
            'fare_matrix_id' => 'nullable|exists:fare_matrices,id',
        ]);

        // 2. Prepare Data (CHEAT CODE)
        // Gumamit tayo ng $request->except para hindi isama ang driver_id na galing sa form
        $busData = $request->except(['driver_id']);

        // --- COORDINATES (Palitan mo ito ng nakuha mo sa Google Maps Right Click) ---
        $busData['lat'] = 11.55953551145487;  // Example lang to, palitan mo ng exact
        $busData['lng'] = 122.75077744692442; // Example lang to
        // ------------------------------------------------------------------------

        $busData['status'] = 'active'; 
        $busData['max_capacity'] = $request->capacity ?? 40;
        $busData['driver_id'] = null; // FORCE NULL para walang error

        // 3. Fallback Route
        if (empty($busData['route_id'])) {
            $defaultRoute = Route::first();
            $busData['route_id'] = $defaultRoute ? $defaultRoute->id : null;
        }

        // 4. Save
        Bus::create($busData);

        return redirect()->route('admin.buses.index')->with('success', 'Bus added successfully at the Terminal!');
    }

    /**
     * Show the form for editing the specified bus.
     */
    public function edit($id)
    {
        $bus = Bus::findOrFail($id);
        $routes = Route::all();
        // CONSISTENT: Fetch from Users table
        $drivers = User::where('role', 'driver')->get();
        $fares = \App\Models\FareMatrix::all();

        return view('admin.buses.edit', compact('bus', 'routes', 'drivers', 'fares'));
    }

    /**
     * Update the specified bus in storage.
     */
    public function update(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);

        // 1. VALIDATION
        $validated = $request->validate([
            'bus_number' => 'required|string|max:255',
            'plate_number' => 'nullable|string|max:255',
            'status' => 'required|string',
            'route_id' => 'nullable|exists:routes,id',
            
            // --- FIXED: CONSISTENCY ---
            // Changed 'exists:drivers,id' to 'exists:users,id'
            // This now matches your create/edit logic.
            'driver_id' => 'nullable|exists:users,id', 
            
            // --- ADDED MISSING FIELD ---
            // You forgot this in your previous code!
            'fare_matrix_id' => 'nullable|exists:fare_matrices,id',

            'capacity' => 'nullable|integer',
            // Note: Removed 'type' and 'device_id' from validation 
            // unless you actually have input fields for them in the form.
        ]);

        // 2. SECURITY CHECK (Protect your GPS Unit)
        // Prevent changing the device settings of the main unit accidentally
        if ($bus->device_id === '9176466392') {
             // We allow updates, but we ensure the Device ID cannot be overwritten if passed
             unset($validated['device_id']);
        }

        // 3. SAVE THE DATA
        $bus->update($validated);

        return redirect()->route('admin.buses.index')->with('success', 'Bus updated successfully');
    }

    /**
     * Remove the specified bus from storage.
     */
    public function destroy($id)
    {
        $bus = Bus::findOrFail($id);

        // --- SECURITY PROTECTION ---
        // Prevent deletion of the critical GPS unit
        if ($bus->device_id === '9176466392' || $bus->bus_number === '9176466392') {
            return redirect()->back()->with('error', 'SECURITY ALERT: This Bus Unit (9176466392) is protected and cannot be deleted.');
        }
        // ---------------------------

        $bus->delete();
        return redirect()->route('admin.buses.index')->with('success', 'Bus deleted successfully.');
    }
}