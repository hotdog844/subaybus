<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $driver = Auth::guard('drivers')->user();
        $bus = $driver->bus;

        return view('driver.dashboard', compact('driver', 'bus'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:on route,at terminal,offline',
        ]);

        $driver = Auth::guard('drivers')->user();
        $bus = $driver->bus;

        if ($bus) {
            $bus->update(['status' => $request->status]);
            return back()->with('success', 'Status updated successfully!');
        }

        return back()->with('error', 'You are not assigned to any bus.');
    }
}