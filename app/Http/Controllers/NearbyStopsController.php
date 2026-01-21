<?php

namespace App\Http\Controllers;

use App\Models\Stop; // Changed from BusStop to Stop
use Illuminate\Http\Request;

class NearbyStopsController extends Controller
{
    public function index()
    {
        // Get all stops and include the route details
        $stops = Stop::with('route')->get();
        
        // Pass data to the view
        return view('nearby_stops', compact('stops'));
    }
}