<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\Route as BusRoute; // Use alias
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BusController extends Controller
{
    public function index()
    {
        $buses = Bus::with('driver', 'route')->get(); // Load relationships for the list
        return view('admin.buses.index', ['buses' => $buses]);
    }

    public function create()
    {
        $drivers = Driver::all();
        $routes = BusRoute::all(); // Get all routes
        return view('admin.buses.create', compact('drivers', 'routes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:buses,plate_number',
            'driver_id' => 'nullable|exists:drivers,id',
            'route_id' => 'nullable|exists:routes,id', // Validate the route_id
            'fare' => 'required|numeric|min:0',
            'status' => 'required|in:at terminal,on route,offline',
        ]);

        Bus::create($validated);
        return redirect()->route('admin.buses.index')->with('success', 'Bus added successfully!');
    }

    public function edit(Bus $bus)
    {
        $drivers = Driver::all();
        $routes = BusRoute::all(); // Get all routes
        return view('admin.buses.edit', compact('bus', 'drivers', 'routes'));
    }

    public function update(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', Rule::unique('buses')->ignore($bus->id)],
            'driver_id' => 'nullable|exists:drivers,id',
            'route_id' => 'nullable|exists:routes,id', // Validate the route_id
            'fare' => 'required|numeric|min:0',
            'status' => 'required|in:at terminal,on route,offline',
        ]);

        $bus->update($validated);
        return redirect()->route('admin.buses.index')->with('success', 'Bus updated successfully!');
    }

    // ... destroy method ...
}