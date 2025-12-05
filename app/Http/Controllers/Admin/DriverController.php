<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import the Hash facade
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        return view('admin.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:drivers',
        'license_number' => 'required|string|max:255|unique:drivers,license_number',
        'contact_number' => 'nullable|string|max:255',
        'password' => 'required|string|min:8',
        'license_image' => 'required|image|max:2048', // Max 2MB
        'cert_image' => 'required|image|max:2048',    // Max 2MB
    ]);

    // Handle File Uploads
    $licensePath = $request->file('license_image')->store('driver_documents', 'public');
    $certPath = $request->file('cert_image')->store('driver_documents', 'public');

    // Hash password
    $validated['password'] = Hash::make($validated['password']);

    // Add paths to data
    $validated['license_image_path'] = $licensePath;
    $validated['cert_image_path'] = $certPath;

    Driver::create($validated);

    return redirect()->route('admin.drivers.index')->with('success', 'Driver added successfully with documents!');
}

    public function edit(Driver $driver)
    {
        return view('admin.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('drivers')->ignore($driver->id)],
        'license_number' => ['required', 'string', 'max:255', Rule::unique('drivers')->ignore($driver->id)],
        'contact_number' => 'nullable|string|max:255',
        'password' => 'nullable|string|min:8|confirmed', // Password is now optional
    ]);

    // Prepare the data for updating
    $dataToUpdate = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'license_number' => $validated['license_number'],
        'contact_number' => $validated['contact_number'],
    ];

    // Only add the password to the update array if a new one was provided
    if ($request->filled('password')) {
        $dataToUpdate['password'] = Hash::make($validated['password']);
    }

    $driver->update($dataToUpdate);

    return redirect()->route('admin.drivers.index')->with('success', 'Driver updated successfully!');
}

    public function destroy(Driver $driver)
    {
        if ($driver->bus) {
            return redirect()->route('admin.drivers.index')->with('error', 'Cannot delete driver. They are currently assigned to a bus.');
        }
        $driver->delete();
        return redirect()->route('admin.drivers.index')->with('success', 'Driver deleted successfully!');
    }
}