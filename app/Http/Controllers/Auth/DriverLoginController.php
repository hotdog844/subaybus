<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverLoginController extends Controller
{
    /**
     * Show the driver login form.
     */
    public function showLoginForm()
    {
        return view('auth.driver-login');
    }

    /**
     * Handle a login request for a driver.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to log the driver in using the 'drivers' guard
        if (Auth::guard('drivers')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            // Redirect to the driver's dashboard (we will create this next)
            return redirect()->intended('/driver/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the driver out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('drivers')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/driver/login');
    }
}