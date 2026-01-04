<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Default to "This Month" if no date selected
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // 2. Fetch Logs based on filter
        $logs = DB::table('trip_logs')
            ->whereBetween('shift_end', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('shift_end', 'desc')
            ->get();

        // 3. Calculate Summary Stats
        $totalRevenue = $logs->sum('total_revenue');
        $totalPassengers = $logs->sum('passenger_count');
        $topDriver = $logs->groupBy('driver_name')
            ->sortByDesc(fn($rows) => $rows->sum('total_revenue'))
            ->keys()
            ->first();

        return view('admin.reports.index', compact('logs', 'totalRevenue', 'totalPassengers', 'topDriver', 'startDate', 'endDate'));
    }
}