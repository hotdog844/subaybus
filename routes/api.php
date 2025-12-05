<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\FavoriteRoute;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Toggle Favorite (Add/Remove)
Route::post('/favorites/toggle', function (Request $request) {
    // For this prototype, we'll hardcode user_id=1 if no auth logic is strict yet
    // In production: $user = $request->user();
    $userId = 1; 
    $busId = $request->input('bus_id');

    $exists = FavoriteRoute::where('user_id', $userId)->where('bus_id', $busId)->first();

    if ($exists) {
        $exists->delete();
        return response()->json(['status' => 'removed']);
    } else {
        FavoriteRoute::create(['user_id' => $userId, 'bus_id' => $busId]);
        return response()->json(['status' => 'added']);
    }
});

// Get My Favorites (List of IDs)
Route::get('/favorites/ids', function () {
    $userId = 1; // Hardcoded for demo
    return FavoriteRoute::where('user_id', $userId)->pluck('bus_id');
});


// --- LIVE BUSES ENDPOINT (Public) ---
Route::get('/live-locations', function () {
    // 1. Get the latest 'id' from gps_data for each unique IMEI
    $latestIds = DB::table('gps_data')
        ->select(DB::raw('MAX(id) as id'))
        ->groupBy('imei');

    // 2. Join the tables to get the full info
    $buses = DB::table('buses')
        ->join('gps_data', 'buses.tracker_imei', '=', 'gps_data.imei')
        ->joinSub($latestIds, 'latest_gps', function ($join) {
            $join->on('gps_data.id', '=', 'latest_gps.id');
        })
        ->select(
            'buses.id as bus_id',
            'buses.bus_number',
            'buses.status',
            'buses.current_load',
            'buses.max_capacity',
            'gps_data.lat',
            'gps_data.lng',
            'gps_data.updated_at as last_seen'
        )
        ->get();

    return response()->json($buses);
}); // <--- REMOVED middleware here

// --- ROUTE SHAPES ENDPOINT (Public) ---
Route::get('/routes/shapes', function () {
    return Illuminate\Support\Facades\DB::table('routes')
        ->select('id', 'name', 'color', 'path_data')
        ->whereNotNull('path_data')
        ->get();
});