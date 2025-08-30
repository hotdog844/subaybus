<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function show(Bus $bus)
    {
        // Laravel's "Route Model Binding" automatically finds the bus by its ID.
        // We load the 'driver' relationship to get the driver's name.
        $bus->load('driver');
        
        // Add placeholder data for the UI for now
        $bus->eta_minutes = rand(3, 15);
        $bus->distance_km = number_format(rand(10, 50) / 10, 2); // e.g., 1.0, 5.0

        // Make sure you have a view file named 'mobile_bus_detail.blade.php'
        return view('mobile_bus_detail', ['bus' => $bus]);
    }
}