<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback; // Change this to the Feedback model
use App\Models\Bus;      // Add the Bus model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    // ... your existing store() method can remain ...

    /**
     * Store a bus review (rating and comment).
     */
    public function storeReview(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:5000',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'subject' => 'Bus Review for ' . $bus->plate_number,
            'message' => $validated['comment'],
            'rating' => $validated['rating'], // Save the rating
        ]);

        return response()->json(['message' => 'Thank you for your review!']);
    }
}