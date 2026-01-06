<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is logged in at all
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Get the role
        $role = Auth::user()->role;

        // 🛑 DEBUG CHECK: If this logic matches, they get in.
        // We check for "admin" (lowercase), "Admin" (Capital), or 1 (integer)
        if ($role === 'admin' || $role === 'Admin' || $role == 1) {
            return $next($request);
        }

        // 3. If they fail the check, show an error instead of redirecting
        // This stops the "User Page" redirect and shows us the problem.
        abort(403, "ACCESS DENIED. Your role is: " . $role);
    }
}