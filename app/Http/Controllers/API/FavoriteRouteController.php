<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    // Return a list of Bus IDs that the user has favorited
    public function getFavoriteIds()
    {
        // If user is not logged in, return empty list
        if (!Auth::check()) {
            return response()->json([]);
        }

        $ids = Favorite::where('user_id', Auth::id())
            ->pluck('bus_id'); // Just get the ID numbers

        return response()->json($ids);
    }
    
    // Toggle Favorite (Add/Remove)
    public function toggle(Request $request)
    {
        $request->validate(['bus_id' => 'required|exists:buses,id']);
        $user = Auth::user();

        $existing = Favorite::where('user_id', $user->id)
            ->where('bus_id', $request->bus_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'bus_id' => $request->bus_id
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}