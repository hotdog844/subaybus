<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusLocationController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\FavoriteRouteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// --- SubayBus API Routes ---

// Route to get all defined routes for the homepage filter pills
Route::get('/routes', [BusLocationController::class, 'getRoutes']);

// The new, powerful search route for the homepage to get bus locations
Route::get('/buses/search', [BusLocationController::class, 'search']);

// Endpoint for a hardware device/driver app to send its location
Route::post('/buses/location', [BusLocationController::class, 'update']);

// Endpoint for a logged-in user to submit a rating and review for a bus

// --- Routes for Managing Favorite Routes ---
Route::middleware('auth:sanctum')->group(function () {
    // Get all of the user's favorite route IDs
    Route::get('/favorites', [FavoriteRouteController::class, 'index']);
    // Add or remove a favorite
    Route::post('/favorites/toggle', [FavoriteRouteController::class, 'toggle']);
});
