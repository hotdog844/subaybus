<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusLocationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\TripPlannerController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RfidController;
use App\Http\Controllers\OCRController;

// 1. IMPORT YOUR MODEL
use App\Models\Route as RouteModel; 

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

// 2. ITO ANG SOLUSYON SA NAWALANG PINS (Direct Database Call)
Route::get('/routes', function() {
    // Kukunin nito lahat ng routes + stops nang walang error
    return RouteModel::all();
});

// 3. LIVE TRACKING
Route::get('/bus-locations', [BusLocationController::class, 'index']);
Route::post('/gps/update', [BusLocationController::class, 'update']);

// Other Features
Route::get('/favorites/ids', [FavoriteController::class, 'ids']);
Route::get('/plan-trip', [TripPlannerController::class, 'plan']);
Route::post('/pay', [PaymentController::class, 'tapCard']);
Route::post('/tap-card', [RfidController::class, 'tapCard']);
Route::post('/verify-id', [OCRController::class, 'verifyStudentId']);
Route::get('/routes/shapes', function() {
    return RouteModel::all();
});