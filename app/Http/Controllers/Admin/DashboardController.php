<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\Feedback;
use App\Models\Route as BusRoute; // Use an alias to avoid conflict with Laravel's Route facade
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'drivers' => Driver::count(),
            'buses' => Bus::count(),
            'routes' => BusRoute::count(),
            'feedback' => Feedback::where('status', 'new')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}