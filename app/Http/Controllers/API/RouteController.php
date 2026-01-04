<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
   public function index()
{
    // We use 'all()' first to ensure it works. 
    // Optimization can be done later once the map is drawing.
    $routes = \App\Models\Route::all();

    // Map the data to ensure coordinates are numbers (Javascript needs numbers)
    $mappedRoutes = $routes->map(function($route) {
        return [
            'id' => $route->id,
            'name' => $route->name,
            'start_location' => $route->start_location,
            'end_location' => $route->end_location,
            // Force these to be numbers or null (fixes "string" issues)
            'origin_lat' => (float)$route->origin_lat,
            'origin_lng' => (float)$route->origin_lng,
            'dest_lat' => (float)$route->dest_lat,
            'dest_lng' => (float)$route->dest_lng,
            'color' => $route->color ?? '#3b82f6', // Default blue if missing
        ];
    });

    return response()->json($mappedRoutes);
}
}