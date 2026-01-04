@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Edit Bus Configuration</h1>
                <p class="text-gray-500 mt-1">Editing unit: <span class="font-mono text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ $bus->bus_number }}</span></p>
            </div>
            
            <form action="{{ route('admin.buses.destroy', $bus->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold py-2 px-4 rounded-lg shadow-sm transition flex items-center gap-2">
                    <span>Delete Unit</span>
                </button>
            </form>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Please check the inputs:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <form action="{{ route('admin.buses.update', $bus->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT') 

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Bus Identity (ID)</label>
                            <input type="text" name="bus_number" value="{{ old('bus_number', $bus->bus_number) }}" 
                                   class="w-full bg-gray-50 text-gray-900 border border-gray-200 rounded-lg py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:bg-white transition font-mono" required>
                            <p class="text-xs text-gray-400 mt-2">Set to <strong>SIM-BLU</strong> to activate simulation.</p>
                        </div>

                        <div>
    <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Operational Status</label>
    <select name="status" class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none">
        
        <option value="on route" {{ $bus->status == 'on route' || $bus->status == 'active' ? 'selected' : '' }}>
            🟢 On Route (Moving)
        </option>
        
        <option value="at terminal" {{ $bus->status == 'at terminal' || $bus->status == 'standby' ? 'selected' : '' }}>
            🟡 At Terminal (Stopped)
        </option>
        
        <option value="maintenance" {{ $bus->status == 'maintenance' ? 'selected' : '' }}>
            🔴 Maintenance
        </option>

        <option value="offline" {{ $bus->status == 'offline' ? 'selected' : '' }}>
            ⚫ Offline
        </option>

    </select>
</div>
                        
                        <div>
                             <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Capacity</label>
                             <input type="number" name="capacity" value="{{ old('capacity', $bus->capacity ?? 40) }}" class="w-full border border-gray-200 rounded-lg py-3 px-4">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                            <label class="block text-xs font-bold text-blue-800 uppercase tracking-wide mb-2">Assigned Driver</label>
                            <select name="driver_id" class="w-full bg-white border border-blue-200 text-gray-900 rounded-lg py-3 px-4 focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">-- No Driver --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ $bus->driver_id == $driver->id ? 'selected' : '' }}>
                                        👤 {{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-blue-400 mt-2">This driver will appear on the mobile app.</p>
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
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Assigned Route</label>
                            <select name="route_id" class="w-full bg-white border border-gray-200 text-gray-900 rounded-lg py-3 px-4 focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">-- No Route --</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" {{ $bus->route_id == $route->id ? 'selected' : '' }}>
                                        📍 {{ $route->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Plate Number</label>
                            <input type="text" name="plate_number" value="{{ old('plate_number', $bus->plate_number) }}" class="w-full border border-gray-200 rounded-lg py-3 px-4">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="device_id" value="{{ $bus->device_id ?? $bus->bus_number }}">

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-4">
                    <a href="{{ route('admin.buses.index') }}" class="py-3 px-6 text-gray-500 font-bold hover:text-gray-800 transition">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transform active:scale-95 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection