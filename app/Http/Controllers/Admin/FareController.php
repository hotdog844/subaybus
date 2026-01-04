<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FareController extends Controller
{
    // 1. Show the Matrix
    public function index()
    {
        $fares = DB::table('fare_matrices')->get();
        return view('admin.fares.index', compact('fares'));
    }

    // 2. Update Rates
    public function update(Request $request, $id)
    {
        $request->validate([
            'base_fare' => 'required|numeric',
            'base_km' => 'required|numeric|min:1', // NEW VALIDATION
            'per_km_rate' => 'required|numeric',
        ]);

        DB::table('fare_matrices')->where('id', $id)->update([
            'base_fare' => $request->base_fare,
            'base_km' => $request->base_km, // SAVE NEW FIELD
            'per_km_rate' => $request->per_km_rate,
            'discount_base' => $request->discount_base,
            'discount_per_km' => $request->discount_per_km,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Fare matrix updated successfully!');
    }
}