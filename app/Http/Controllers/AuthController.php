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

    if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // 🛑 DEBUGGING START (Add these lines temporarily)
            $role = Auth::user()->role;
            
            // If the role isn't matching, this will show us WHY.
            // Example: Is it "Admin" (capital A)? Or "admin " (with a space)?
            // dd("The System sees your role as: " . $role); 
            // 🛑 DEBUGGING END

            // 1. Check Admin
            if ($role === 'admin') {
                return redirect('/admin/dashboard');
            }
            
            // 2. Check Driver
            if ($role === 'driver') {
                return redirect()->route('driver.menu');
            }
            
            // 3. Default (Passenger)
            return redirect('/mobile/dashboard');
        }
        
        return back()->withErrors(['email' => 'Failed']);
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