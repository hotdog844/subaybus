@extends('layouts.admin')

@section('title', 'Performance Reports')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none; }
            .print-only { display: block; }
            body { background: white; }
            .shadow-xl, .shadow-sm { box-shadow: none !important; border: 1px solid #ddd; }
        }
    </style>

    <div class="max-w-7xl mx-auto pt-4">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4 no-print">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Driver Performance</h2>
                <p class="text-gray-500 mt-1">Revenue and passenger collection reports.</p>
            </div>

            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex items-center gap-3 bg-white p-2 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2 px-2">
                    <span class="text-xs font-bold text-gray-400 uppercase">From</span>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm font-bold text-gray-700 focus:outline-none">
                </div>
                <div class="w-px h-6 bg-gray-200"></div>
                <div class="flex items-center gap-2 px-2">
                    <span class="text-xs font-bold text-gray-400 uppercase">To</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm font-bold text-gray-700 focus:outline-none">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                    Filter
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Revenue</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-1">₱ {{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl">
                    <i class="fas fa-coins"></i>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Passengers</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($totalPassengers) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Top Performer</p>
                    <h3 class="text-xl font-bold text-gray-800 mt-1 truncate max-w-[150px]">{{ $topDriver ?? 'No Data' }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-700">Detailed Shift Logs</h3>
                <button onclick="window.print()" class="no-print text-gray-500 hover:text-gray-800 text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
            
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase">Date</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase">Driver</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase">Bus & Route</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase text-right">Passengers</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase text-right">Collection</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-sm text-gray-500 font-mono">
                            {{ \Carbon\Carbon::parse($log->shift_end)->format('M d, Y') }}
                        </td>
                        <td class="p-4 font-bold text-gray-700">{{ $log->driver_name }}</td>
                        <td class="p-4">
                            <div class="text-xs font-bold text-gray-600">{{ $log->bus_number }}</div>
                            <div class="text-[10px] text-gray-400">{{ $log->route_name }}</div>
                        </td>
                        <td class="p-4 text-right font-mono text-gray-600">{{ $log->passenger_count }}</td>
                        <td class="p-4 text-right font-bold text-green-600">₱ {{ number_format($log->total_revenue, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">No shift records found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection