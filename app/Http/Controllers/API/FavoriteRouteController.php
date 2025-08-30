<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteRouteController extends Controller
{
    /**
     * Return a list of the authenticated user's favorite route IDs.
     */
    public function index()
    {
        $favoriteRouteIds = Auth::user()->favoriteRoutes()->pluck('routes.id');
        return response()->json($favoriteRouteIds);
    }

    /**
     * Add or remove a route from the user's favorites.
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
        ]);

        $user = Auth::user();
        $routeId = $validated['route_id'];

        // toggle() will attach if not attached, and detach if already attached.
        // It returns an array indicating what was done.
        $result = $user->favoriteRoutes()->toggle($routeId);

        $status = count($result['attached']) > 0 ? 'favorited' : 'unfavorited';

        return response()->json([
            'status' => $status,
            'message' => "Route successfully {$status}."
        ]);
    }
}