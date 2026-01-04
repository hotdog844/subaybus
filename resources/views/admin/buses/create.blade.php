@extends('layouts.admin')

@section('title', 'Add New Bus')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>

    <div class="max-w-4xl mx-auto pt-6">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Add New Bus</h2>
                <p class="text-sm text-gray-500">Register a new vehicle into the fleet management system.</p>
            </div>
            <a href="{{ route('admin.buses.index') }}" class="text-gray-500 hover:text-gray-800 font-bold text-sm flex items-center gap-2 transition">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
            <div class="bg-gray-50/50 px-8 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-bus"></i>
                </div>
                <h3 class="font-bold text-gray-800">Vehicle Information</h3>
            </div>

            <form action="{{ route('admin.buses.store') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        
        <div>
            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Bus Number / Identity</label>
            <input type="text" name="bus_number" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none" placeholder="e.g. Bus 101 or SIM-TEST" required>
        </div>

        <div>
            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Plate Number</label>
            <input type="text" name="plate_number" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none" placeholder="e.g. ABC 1234" required>
        </div>

        <div>
            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Assign Route</label>
            <select name="route_id" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none">
                <option value="">-- No Route (Gray) --</option>
                @foreach($routes as $route)
                    <option value="{{ $route->id }}">{{ $route->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
    <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">
        Fare Type (Matrix)
    </label>
    <select name="fare_matrix_id" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none">
        <option value="">-- Default / Standard Fare --</option>
        
        @foreach($fares as $fare)
            <option value="{{ $fare->id }}" 
                {{ (isset($bus) && $bus->fare_matrix_id == $fare->id) ? 'selected' : '' }}>
                
                💰 {{ $fare->name ? $fare->name : 'Matrix #' . $fare->id }} (₱{{ $fare->base_fare ?? '0.00' }})
            </option>
        @endforeach
        
    </select>
</div>

        <div>
            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Assign Driver</label>
            <select name="driver_id" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none">
                <option value="">-- No Driver (Auto-Bot) --</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Initial Status</label>
            <select name="status" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none">
                <option value="at terminal">🟡 At Terminal (Parked)</option>
                <option value="on route">🟢 On Route (Active)</option>
                <option value="offline">⚫ Offline</option>
                <option value="maintenance">🔴 Maintenance</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Max Capacity</label>
            <input type="number" name="capacity" value="40" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none">
        </div>

    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
        Create Bus
    </button>
</form>

                 
        </div>
    </div>
@endsection