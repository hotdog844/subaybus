<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        // Kunin ang routes kasama ang stops, naka-order ayon sa sequence
        $routes = Route::with(['stops' => function($query) {
            $query->orderBy('sequence', 'asc')->orderBy('order_index', 'asc');
        }])->get();

        $mappedRoutes = $routes->map(function($route) {
            
            // 1. I-decode ang Path Data (Ang Blue Line)
            $pathData = $route->path_data;
            
            // Kung ito ay naka-string (JSON format galing DB), gawing Array
            if (is_string($pathData)) {
                $pathData = json_decode($pathData);
            }
            
            // Fallback: Kung walang path data, gamitin ang Origin -> Destination straight line
            if (empty($pathData) && $route->origin_lat && $route->destination_lat) {
                $pathData = [
                    [(float)$route->origin_lat, (float)$route->origin_lng],
                    [(float)$route->destination_lat, (float)$route->destination_lng]
                ];
            }

            return [
                'id' => $route->id,
                'name' => $route->name,
                'description' => $route->description,
                'color' => $route->color ?? '#3b82f6', // Default Blue
                
                // Ipadala ang path array sa dashboard
                'path' => $pathData, 

                // I-map ang mga stops
                'stops' => $route->stops->map(function($stop) use ($route) {
                    return [
                        'id' => $stop->id,
                        'name' => $stop->name,
                        'lat' => (float)$stop->latitude, 
                        'lng' => (float)$stop->longitude,
                        'sequence' => $stop->sequence,
                        // Mahalaga: Ipasa ang Route ID para sa frontend logic
                        'route_id' => $route->id,
                        'route_name' => $route->name,
                        'route_color' => $route->color ?? '#3b82f6'
                    ];
                })
            ];
        });

        return response()->json($mappedRoutes);
    }
}