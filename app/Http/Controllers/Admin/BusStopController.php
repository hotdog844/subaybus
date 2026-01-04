<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusStop;
use App\Models\Route as BusRoute;
use Illuminate\Http\Request;

class BusStopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BusRoute $route)
    {
        $stops = $route->busStops()->orderBy('sequence')->get();
        return view('admin.stops.index', compact('route', 'stops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(BusRoute $route)
    {
        return view('admin.stops.create', compact('route'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, BusRoute $route)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'sequence' => 'required|integer|min:1',
        ]);

        $route->busStops()->create($validated);

        return redirect()->route('admin.routes.stops.index', $route->id)->with('success', 'Stop added successfully!');
    }

    // ... We will add edit and delete methods later ...
}