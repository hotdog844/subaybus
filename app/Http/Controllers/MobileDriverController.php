<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bus; 
use Carbon\Carbon;

class MobileDriverController extends Controller
{
    // 1. Show the "Login" screen
    public function login()
    {
        $buses = DB::table('buses')->get();
        return view('driver', ['buses' => $buses]);
    }

    // 2. Handle the "Login"
    public function authenticate(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // ✅ Check if they are actually a driver
        if (Auth::user()->role !== 'driver') {
            Auth::logout();
            return back()->withErrors(['email' => 'Access denied. Drivers only.']);
        }

        // ✅ Redirect to the "Select Unit" Hub
        return redirect()->route('driver.menu');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials.',
    ]);
}

    // 3. Show the Dashboard (The Cockpit)
    public function dashboard($id)
    {
        // 1. Get the Bus
        $bus = \App\Models\Bus::findOrFail($id);

        // 2. FORCE FETCH: Get Route Name directly from the table (Bypassing the Model)
        $routeName = 'No Route Assigned'; // Default value
        
        if ($bus->route_id) {
            // We use DB::table to ignore any "Soft Delete" or Model issues
            $routeData = \Illuminate\Support\Facades\DB::table('routes')
                            ->where('id', $bus->route_id)
                            ->first();
            
            if ($routeData) {
                $routeName = $routeData->name;
            }
        }

        // 3. Pass both 'bus' AND 'routeName' to the view
        if (view()->exists('driver.dashboard')) {
            return view('driver.dashboard', compact('bus', 'routeName'));
        } 
        
        return view('mobile_driver_dashboard', compact('bus', 'routeName'));
    }

    // 4. Update Status & Passenger Count (FIXED)
    public function updateStatus(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);

        // --- A. Handle Passenger Count (From the + / - buttons) ---
        // The JS sends { passenger_count: 5 }, so we check for that directly
        if ($request->has('passenger_count')) {
            $bus->passenger_count = $request->passenger_count;
            $bus->updated_at = now(); // Update timestamp so map knows it's live
        }

        // --- B. Handle Status Change (From "Report Issue" or ending shift) ---
        if ($request->has('status')) {
            $newStatus = $request->status;

            // Logic: If going offline, save the report
            if ($newStatus == 'offline' && $bus->status != 'offline') {
                $this->saveShiftReport($bus);
            }

            $bus->status = $newStatus;
        }

        // --- C. Handle Live GPS (Future Proofing) ---
        if ($request->has('lat') && $request->has('lng')) {
            if ($request->lat != 0) {
                $bus->lat = $request->lat;
                $bus->lng = $request->lng;
            }
        }

        $bus->save();

        return response()->json(['status' => 'success']);
    }

// --- HELPER: Save Trip Log ---
private function saveShiftReport($bus)
{
    // 1. Get Real Route Name
    $routeName = 'Unassigned Route';
    if ($bus->route_id) {
        $routeName = DB::table('routes')->where('id', $bus->route_id)->value('name');
    }

    // 2. Calculate Revenue (Example: 15.00 flat rate)
    $baseFare = 15.00; 
    $revenue = $bus->passenger_count * $baseFare;

    // 3. Insert Log
    DB::table('trip_logs')->insert([
        'driver_name' => auth()->check() ? auth()->user()->name : 'Driver ' . $bus->plate_number,
        'bus_number' => $bus->bus_number,
        'route_name' => $routeName,
        'passenger_count' => $bus->passenger_count,
        'total_revenue' => $revenue,
        'shift_start' => Carbon::parse($bus->updated_at)->subHours(2), // Rough estimate
        'shift_end' => Carbon::now(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);

    // 4. Reset Counter for next shift
    $bus->passenger_count = 0;
    $bus->save();
}

public function endShift($id)
{
    $bus = \App\Models\Bus::findOrFail($id);

    // 1. Update the Bus Status
    $bus->update([
        'status' => 'offline',
        'driver_id' => null, // Unassign the driver so the bus is free
        'passenger_count' => 0 // Reset passengers for next shift
    ]);

    // 2. Return success response
    return response()->json(['status' => 'success']);
}

// 1. Show the Driver Hub / Menu
public function index()
    {
        // Fetch all buses (or filter by 'status' => 'offline' if you only want available ones)
        // Using with('route') to ensure we show route names in the list
        $buses = \App\Models\Bus::with('route')->get();

        return view('driver.menu', compact('buses'));
    }
}