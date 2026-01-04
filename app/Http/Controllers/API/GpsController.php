<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GpsController extends Controller
{
    // ENDPOINT: /api/gps/data
    // Usage: ?id=SIM-GRN&lat=11.58&lon=122.75
    public function receive(Request $request)
    {
        // 1. Capture Data (SinoTrack uses 'id', 'lat', 'lon')
        $deviceId = $request->query('id') ?? $request->query('imei');
        $lat = $request->query('lat');
        $lng = $request->query('lon') ?? $request->query('lng'); 
        
        if (!$deviceId || !$lat || !$lng) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        // 2. Find the Bus by Plate Number
        $bus = DB::table('buses')->where('plate_number', $deviceId)->first();

        if ($bus) {
            // 3. Update the Database
            DB::table('buses')->where('id', $bus->id)->update([
                'lat' => $lat,
                'lng' => $lng,
                'status' => 'on route', // Force status to active so it turns GREEN
                'last_seen' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return response()->json(['status' => 'OK', 'message' => 'Location Updated']);
        }

        return response()->json(['error' => 'Device ID not found'], 404);
    }
}