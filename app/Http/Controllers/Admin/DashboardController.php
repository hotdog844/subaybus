<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\Feedback;
use App\Models\Route as BusRoute;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Basic Stats Cards
        $stats = [
            'users' => User::count(),
            'drivers' => Driver::count(),
            'buses' => Bus::count(),
            'routes' => BusRoute::count(),
            'feedback' => Feedback::count(),
        ];

        // 2. Passenger Growth Data (The magic part)
        
        // Daily (Last 30 Days)
        $daily = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Weekly (Last 12 Weeks)
        $weekly = User::select(DB::raw('YEARWEEK(created_at) as yearweek'), DB::raw('MIN(DATE(created_at)) as week_start'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subWeeks(12))
            ->groupBy('yearweek')
            ->orderBy('yearweek')
            ->get();

        // Monthly (Last 12 Months)
        $monthly = User::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Yearly (Last 5 Years)
        $yearly = User::select(DB::raw('YEAR(created_at) as year'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subYears(5))
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Format data for Chart.js
        $chartData = [
            'daily' => [
                'labels' => $daily->pluck('date'),
                'data' => $daily->pluck('count'),
            ],
            'weekly' => [
                'labels' => $weekly->pluck('week_start'), // Shows the start date of the week
                'data' => $weekly->pluck('count'),
            ],
            'monthly' => [
                'labels' => $monthly->pluck('month'),
                'data' => $monthly->pluck('count'),
            ],
            'yearly' => [
                'labels' => $yearly->pluck('year'),
                'data' => $yearly->pluck('count'),
            ],
        ];

        return view('admin.dashboard', compact('stats', 'chartData'));
    }
}