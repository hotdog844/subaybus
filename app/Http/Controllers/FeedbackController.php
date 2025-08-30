<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Show the feedback form.
     */
    public function create()
    {
        return view('feedback.create');
    }

    /**
     * Store a new feedback submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Create the feedback submission
        Feedback::create([
            'user_id' => Auth::id(), // Will be null if user is not logged in
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        return redirect()->route('feedback.create')->with('success', 'Thank you for your feedback!');
    }
}