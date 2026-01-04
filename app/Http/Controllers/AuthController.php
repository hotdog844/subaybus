<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
{
    // 1. Validate
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. Attempt Login
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // ✅ 3. REDIRECT BASED ON ROLE (The Fix)
        $role = Auth::user()->role;

        if ($role === 'driver') {
            return redirect()->route('driver.menu'); // Goes to "Select Unit"
        }
        
        if ($role === 'admin') {
            return redirect('/admin/dashboard');
        }

        // Default for Passengers
        return redirect('/mobile/dashboard'); 
    }

    // 4. Failed Login
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
}

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}