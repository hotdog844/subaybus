<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; 

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. Register your custom Admin Middleware alias
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

        // 2. THE FIX: Redirect "Already Logged In" users based on Role
        $middleware->redirectUsersTo(function (Request $request) {
            
            // Get the current user
            $user = $request->user();

            // Check Role: Admin
            if ($user && $user->role === 'admin') {
                return '/admin/dashboard';
            }

            // Check Role: Driver
            if ($user && $user->role === 'driver') {
                return '/driver/menu'; // Goes to "Select Unit"
            }

            // Default: Passenger (User)
            return '/mobile/dashboard'; 
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();