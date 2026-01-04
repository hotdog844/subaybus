<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HailController extends Controller
{
    // 1. Receive Signal from Passenger
    public function store(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $user = auth()->user();

        // Spam Protection: Check for active signal in last 15 mins
        $existing = DB::table('hails')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('created_at', '>', Carbon::now()->subMinutes(15))
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Signal already active.'
            ]);
        }

        // Create Signal
        DB::table('hails')->insert([
            'user_id' => $user->id,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['status' => 'success']);
    }

    // 2. Send Signals to Driver Map
    public function index()
    {
        // Get active hails from last 15 minutes
        $hails = DB::table('hails')
            ->join('users', 'hails.user_id', '=', 'users.id')
            ->where('hails.status', 'active')
            ->where('hails.created_at', '>', Carbon::now()->subMinutes(15))
            ->select(
                'hails.id', 
                'hails.latitude', 
                'hails.longitude', 
                'users.name as user_name'
            )
            ->get();

        // Format for Driver JS (It expects nested user.name)
        $data = $hails->map(function($hail) {
            return [
                'id' => $hail->id,
                'latitude' => $hail->latitude,
                'longitude' => $hail->longitude,
                'user' => [
                    'name' => $hail->user_name
                ]
            ];
        });

        return response()->json($data);
    }
}