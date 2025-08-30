<?php

namespace App\Http\Controllers;

use App\Models\BusStop;
use Illuminate\Http\Request;

class NearbyStopsController extends Controller
{
    public function index()
    {
        // Eager load the relationships: for each stop, get its route,
        // and for that route, get all of its associated buses and their drivers.
        $stops = BusStop::with('route.buses.driver')->get();
        
        return view('nearby_stops', compact('stops'));
    }
}