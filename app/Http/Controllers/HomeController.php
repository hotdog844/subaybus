<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\Rating;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Use the auth() helper which safely returns null if not logged in
        $user = auth()->user();

        $totalTrips = 0;
        $lastTrip = null;
        $avgRating = 0;

        if ($user) {
            $totalTrips = Trip::where('user_id', $user->id)->count();
            $lastTrip = Trip::where('user_id', $user->id)->latest()->first();
            $avgRating = Rating::where('user_id', $user->id)->avg('rating') ?? 0;
        }
        
        $totalBuses = Bus::count();
        $stops = BusStop::all();

        // Pass all the data to the view
        return view('mobile_dashboard', [
            'totalTrips' => $totalTrips,
            'totalBuses' => $totalBuses,
            'lastTrip' => $lastTrip,
            'avgRating' => round($avgRating, 1),
            'stops' => $stops
        ]);
    }
}