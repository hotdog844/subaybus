<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        // 1. Validate - matching your Blade input names
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:drivers,email',
            'password' => 'required|string|min:8',
            'license_number' => 'required|string|unique:drivers,license_number',
            'license_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'psa_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'cert_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Handle File Uploads to Public Storage
        $licensePath = $request->file('license_image')->store('drivers/licenses', 'public');
        $psaPath = $request->file('psa_image')->store('drivers/psa', 'public');
        $certPath = $request->file('cert_image')->store('drivers/medical', 'public');

        // 3. Create Record
        Driver::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'license_number' => $request->license_number,
            'contact_number' => $request->contact_number,
            'license_image' => $licensePath,
            'psa_image' => $psaPath,
            'cert_image' => $certPath,
            'status' => 'pending' 
        ]);

        return redirect('/')->with('success', 'Application submitted! Please wait for admin approval.');
    }

    /**
     * ✅ NEW: Verification Method for the Modal
     */
    public function verify(Driver $driver)
    {
        $driver->update(['status' => 'verified']);

        return redirect()->route('admin.drivers.index')->with('success', "Driver {$driver->name} has been successfully verified!");
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
            'password' => 'nullable|string|min:8', 
        ]);

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'license_number' => $validated['license_number'],
            'contact_number' => $validated['contact_number'],
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        $driver->update($dataToUpdate);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver updated successfully!');
    }

    public function destroy(Driver $driver)
    {
        // Prevent deletion if assigned to a bus
        if ($driver->bus) {
            return redirect()->route('admin.drivers.index')->with('error', 'Cannot delete driver. They are currently assigned to a bus.');
        }

        // Optional: Delete physical images from storage when driver is deleted
        Storage::disk('public')->delete([$driver->license_image, $driver->psa_image, $driver->cert_image]);

        $driver->delete();
        return redirect()->route('admin.drivers.index')->with('success', 'Driver and associated documents deleted.');
    }
}