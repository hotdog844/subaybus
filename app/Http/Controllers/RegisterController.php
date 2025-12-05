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
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:15'],
            'passenger_type' => ['required', 'string'],
            'id_image' => ['required', 'image', 'max:2048'], // Validate the image (max 2MB)
        ]);

        // Handle File Upload
        $idImagePath = $request->file('id_image')->store('id_verifications', 'public');

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'passenger_type' => $request->passenger_type,
            'id_image_path' => $idImagePath, // Save the path
            'is_verified' => false, // Default to unverified
        ]);

        // ... (Your existing Make.com/Email logic here) ...

        Auth::login($user);

        return redirect(route('verification.notice'));
    }
}