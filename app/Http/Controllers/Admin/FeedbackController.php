<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;           // <--- FIXED: Added this import
use Illuminate\Support\Facades\DB;     // <--- FIXED: Added this import

class FeedbackController extends Controller
{
    public function index(Request $request) // <--- Now works because of the import above
    {
        // 1. CALCULATE STATS
        $totalFeedback = DB::table('feedback')->count();
        $unreadCount = DB::table('feedback')->where('status', 'new')->count();
        $avgRating = DB::table('feedback')->avg('rating') ?? 0;

        // 2. GET DATA WITH SEARCH
        $query = DB::table('feedback')
            ->join('users', 'feedback.user_id', '=', 'users.id')
            ->leftJoin('buses', 'feedback.bus_id', '=', 'buses.id')
            ->select(
                'feedback.*', 
                'users.name as user_name', 
                'users.email as user_email',
                'buses.plate_number as bus_plate'
            );

        // Search Logic
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('users.name', 'like', "%{$search}%")
                  ->orWhere('buses.plate_number', 'like', "%{$search}%");
        }

        $feedbackItems = $query->orderBy('feedback.created_at', 'desc')->paginate(10);

        return view('admin.feedback.index', compact('feedbackItems', 'totalFeedback', 'unreadCount', 'avgRating'));
    }

    // Mark as Read
    public function markAsRead($id)
    {
        DB::table('feedback')
            ->where('id', $id)
            ->update(['status' => 'read', 'updated_at' => now()]);
            
        return back()->with('success', 'Feedback marked as read.');
    }

    // Delete
    public function destroy($id)
    {
        DB::table('feedback')->where('id', $id)->delete();
        return back()->with('success', 'Feedback deleted.');
    }
}