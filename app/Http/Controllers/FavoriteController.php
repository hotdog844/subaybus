<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite; // We assume you have this Model. See Step 3 if not.

class FavoriteController extends Controller
{
    // This function returns the list of Route IDs that the user has favorited
    public function ids()
    {
        $user = Auth::user();

        // If user is not logged in, return empty list
        if (!$user) {
            return response()->json([]);
        }

        // Get all 'route_id' values where the user_id matches the current user
        // This assumes your database table is named 'favorites' and has a 'route_id' column
        $favoriteIds = Favorite::where('user_id', $user->id)->pluck('route_id');

        return response()->json($favoriteIds);
    }
}