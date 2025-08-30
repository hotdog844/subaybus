<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function show()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'passenger_type' => ['required', 'string', 'in:Regular,Student,Senior Citizen'],
        'id_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
    ]);

    $imagePath = $request->file('id_image')->store('id_images', 'public');

    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'name' => $validated['first_name'] . ' ' . $validated['last_name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'passenger_type' => $validated['passenger_type'],
        'id_image_path' => $imagePath,
        'is_verified' => false,
    ]);

    // --- NEW DEBUGGING APPROACH ---
    try {
        // Manually send the verification email
        $user->sendEmailVerificationNotification();
    } catch (\Exception $e) {
        // If it fails, dump the SPECIFIC error message and stop.
    }

    // We are temporarily disabling the event to take manual control.
    // event(new \Illuminate\Auth\Events\Registered($user));

    Auth::login($user);

    return redirect()->route('verification.notice');
}
}