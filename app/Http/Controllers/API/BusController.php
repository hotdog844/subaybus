<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;

class BusController extends Controller
{
    public function index()
    {
        // This can remain for other purposes
        $buses = Bus::with(['driver', 'route'])->get();
        return response()->json($buses);
    }

    // ADD THIS NEW METHOD
    public function getLiveBus()
    {
        // Find the specific bus connected to your live GPS device (e.g., ID 1)
        $liveBus = Bus::with(['driver', 'route'])->find(1);

        if (!$liveBus) {
            return response()->json(['error' => 'Live bus not found'], 404);
        }

        return response()->json($liveBus);
    }
}