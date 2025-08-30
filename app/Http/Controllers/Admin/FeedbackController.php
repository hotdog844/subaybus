<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbackItems = Feedback::latest()->get(); 
        return view('admin.feedback.index', ['feedbackItems' => $feedbackItems]);
    }

    public function markAsRead(Feedback $feedback)
    {
        $feedback->update(['status' => 'read']);
        return redirect()->route('admin.feedback.index')->with('success', 'Feedback marked as read.');
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return redirect()->route('admin.feedback.index')->with('success', 'Feedback message deleted.');
    }
}