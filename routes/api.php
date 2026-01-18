<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusLocationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\TripPlannerController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RfidController;
use App\Http\Controllers\OCRController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. GLOBAL HEADERS (Allows access from any device)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

// 2. LIVE TRACKING ROUTE
// We changed this from '/bus-locations' to '/live-tracking' 
// to force the system to stop loading the old cached data.
Route::get('/live-tracking', [BusLocationController::class, 'index']);

// 3. GPS UPDATE ROUTE (For your 9176466392 device)
Route::post('/gps/update', [BusLocationController::class, 'update']);

Route::get('/favorites/ids', [FavoriteController::class, 'ids']);

Route::get('/plan-trip', [TripPlannerController::class, 'plan']);

// The Hardware will hit this URL: http://your-ip-address/api/pay
Route::post('/pay', [PaymentController::class, 'tapCard']);

Route::post('/tap-card', [RfidController::class, 'tapCard']);

Route::post('/verify-id', [OCRController::class, 'verifyStudentId']);