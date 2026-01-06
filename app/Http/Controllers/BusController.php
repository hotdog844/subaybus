<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function show(Bus $bus)
    {
        // FIX: We added 'route.stops' here. 
        // This forces Laravel to send the route info along with the bus.
        $bus->load(['driver', 'route.stops']); 
        
        // Keep your existing placeholder data
        $bus->eta_minutes = rand(3, 15);
        $bus->distance_km = number_format(rand(10, 50) / 10, 2);

        return view('mobile_bus_detail', ['bus' => $bus]);
    }
}