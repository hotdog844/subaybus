<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Load API routes with /api prefix
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        // ✅ Load web routes normally
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
