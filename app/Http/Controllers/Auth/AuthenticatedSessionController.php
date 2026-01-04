<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(\App\Http\Requests\Auth\LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        // 🛑 DEBUG: Stop here and show me the role!
        dd($request->user()->role); 

        // Check Role
        $role = $request->user()->role; 

        if ($role === 'driver') { // Ensure this matches EXACTLY what dd() showed
            return redirect()->intended('/driver/menu');
        }

        if ($role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
