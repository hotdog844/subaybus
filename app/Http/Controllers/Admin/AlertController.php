<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alert;

class AlertController extends Controller
{
    public function index()
    {
        // Get all alerts, newest first
        $alerts = Alert::latest()->get();
        return view('admin.alerts.index', compact('alerts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'message' => 'required|string|max:255',
            'type' => 'required|in:info,warning,danger'
        ]);

        Alert::create($validated);

        return redirect()->back()->with('success', 'Alert Broadcasted Successfully!');
    }

    // ✅ ADD THIS FUNCTION TO ENABLE DELETION
    public function destroy($id)
    {
        $alert = Alert::findOrFail($id);
        $alert->delete();

        return redirect()->back()->with('success', 'Broadcast stopped successfully.');
    }
}