<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routes = Route::all();
        return view('admin.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.routes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:routes,name',
            'start_destination' => 'required|string|max:255',
            'end_destination' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Route::create($validated);

        return redirect()->route('admin.routes.index')->with('success', 'Route added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        // Not needed for this project
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Route $route)
    {
        return view('admin.routes.edit', compact('route'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('routes')->ignore($route->id)],
            'start_destination' => 'required|string|max:255',
            'end_destination' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $route->update($validated);

        return redirect()->route('admin.routes.index')->with('success', 'Route updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('admin.routes.index')->with('success', 'Route deleted successfully!');
    }
}