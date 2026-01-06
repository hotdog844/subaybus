<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trip;
use App\Models\Rating;
use App\Models\Bus;
use App\Models\BusStop;

class MobileController extends Controller
{
    public function index()
    {
        return view('mobile');
    }

    public function dashboard()
{   $user = Auth::user();

    // Simulate trips
    $totalTrips = Trip::where('user_id', $user->id)->count();

    // Get latest trip info
    $lastTrip = Trip::where('user_id', $user->id)->latest()->first();

    // Simulate average rating from ratings table
    $avgRating = Rating::where('user_id', $user->id)->avg('rating');
    
    return view('mobile_dashboard', [
        'user' => $user,
        'totalTrips' => $totalTrips,
        'lastTrip' => $lastTrip,
        'avgRating' => $avgRating ?? 0,
    ]);
}

    public function profile()
    {
        $user = Auth::user(); // ✅ Get current logged-in user
    return view('mobile_profile', compact('user'));
    }

    public function settings()
    {
        return view('mobile_settings');
    }

    public function editProfile()
    {
        return view('mobile_edit_profile');
    }

    public function showBus($id)
{
    $bus = Bus::with('locations')->findOrFail($id);

    // ✅ Automatically log trip only if user is logged in and hasn't tapped this bus in last 10 minutes
    if (Auth::check()) {
        $recentTrip = Trip::where('user_id', Auth::id())
            ->where('bus_id', $bus->id)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if (!$recentTrip) {
            Trip::create([
                'user_id' => Auth::id(),
                'bus_id'  => $bus->id,
                'route'   => $bus->route,
            ]);
        }
    }

    return view('mobile_bus_detail', compact('bus'));
}

public function updateProfile(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone' => 'required|string|max:20',
        'passenger_type' => 'required|in:regular,student,senior,pwd',
        'id_card' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('id_card')) {
        $user->id_card_path = $request->file('id_card')->store('ids', 'public');
    }

    $user->update($request->only('name', 'email', 'phone', 'passenger_type'));

    return redirect('/mobile/profile')->with('success', 'Profile updated.');
}

public function nearby()
{
    // Fetch all stops. Ensure your BusStop model has 'latitude' and 'longitude' columns
    $stops = \App\Models\BusStop::all(); 

    return view('mobile.nearby', compact('stops'));
}
    
}