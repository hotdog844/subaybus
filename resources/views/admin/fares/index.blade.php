@extends('layouts.admin')

@section('title', 'Fare Configuration')

@section('content')

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Soften the inputs */
        input:focus { ring-color: #3b82f6; border-color: #3b82f6; }
    </style>

    <div class="max-w-6xl mx-auto pt-6">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Fare Configuration</h2>
                <p class="text-gray-500 mt-1">Set the authorized fare matrix for each route type.</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded-r-lg shadow-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i>
            <div>
                <p class="font-bold text-sm">Success</p>
                <p class="text-xs">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            @foreach($fares as $fare)
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden hover:shadow-2xl transition duration-300">
                
                <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-100 flex items-center justify-between backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-blue-600 shadow-sm">
                            <i class="fas fa-bus-alt"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">{{ $fare->vehicle_type }}</h3>
                            <div class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Active Matrix</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.fares.update', $fare->id) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fas fa-flag-checkered text-blue-500"></i> Base Settings
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Base Distance</label>
                                <div class="relative group">
                                    <input type="number" id="base_km_{{ $fare->id }}" name="base_km" value="{{ $fare->base_km }}" 
                                        class="w-full pl-3 pr-8 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">km</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Base Price</label>
                                <div class="relative group">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₱</span>
                                    <input type="number" step="0.01" id="base_fare_{{ $fare->id }}" name="base_fare" value="{{ $fare->base_fare }}" 
                                        class="w-full pl-7 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 px-2">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i class="fas fa-road text-gray-400"></i> Succeeding Km
                        </h4>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Add Rate per Km</label>
                            <div class="relative group">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₱</span>
                                <input type="number" step="0.01" id="per_km_{{ $fare->id }}" name="per_km_rate" value="{{ $fare->per_km_rate }}" 
                                    class="w-full pl-7 pr-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Added for every km after the base distance.</p>
                        </div>
                    </div>

                    <div class="bg-blue-50/50 rounded-xl p-4 border border-blue-100/50">
                        <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i class="fas fa-user-graduate"></i> Discounted (20% Off)
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-blue-400 uppercase mb-1">Base</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-300 font-bold text-xs">₱</span>
                                    <input type="number" step="0.01" name="discount_base" value="{{ $fare->discount_base }}" 
                                        class="w-full pl-6 pr-2 py-2 bg-white border border-blue-100 rounded-lg text-xs font-bold text-blue-700 focus:ring-2 focus:ring-blue-200 outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-blue-400 uppercase mb-1">Per Km</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-300 font-bold text-xs">₱</span>
                                    <input type="number" step="0.01" name="discount_per_km" value="{{ $fare->discount_per_km }}" 
                                        class="w-full pl-6 pr-2 py-2 bg-white border border-blue-100 rounded-lg text-xs font-bold text-blue-700 focus:ring-2 focus:ring-blue-200 outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-gray-50 pt-4">
                        <button type="submit" class="bg-gray-900 hover:bg-black text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-gray-200 transition transform active:scale-95 flex items-center gap-2">
                            <i class="fas fa-save"></i> Save Updates
                        </button>
                    </div>

                </form>
            </div>
            @endforeach

        </div>
        
        <div class="mt-8 bg-white rounded-2xl shadow-lg shadow-gray-100 border border-gray-100 p-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Quick Simulator</h3>
                    <p class="text-sm text-gray-400">Test the calculation logic instantly.</p>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <select id="sim_route_id" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-bold h-full">
                    @foreach($fares as $fare)
                        <option value="{{ $fare->id }}">{{ $fare->vehicle_type }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-3 bg-gray-50 p-2 rounded-xl border border-gray-200 shadow-inner">
                    <input type="number" id="calc-km" value="5" class="w-20 bg-white border border-gray-200 rounded-lg px-3 py-2 text-center font-bold text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-xs font-bold text-gray-400 mr-2 uppercase">km</span>
                    <button onclick="calculateSample()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-md transition transform active:scale-95">
                        Check Fare
                    </button>
                </div>
            </div>

            <div class="text-right pr-4">
                 <span class="block text-[10px] uppercase font-bold text-gray-400 tracking-wider">Estimated Fare</span>
                 <span class="text-3xl font-black text-gray-800 tracking-tight" id="calc-result">₱ 0.00</span>
            </div>
        </div>
    </div>

    <script>
        function calculateSample() {
            // 1. Get the Selected Route ID from Dropdown
            let routeId = document.getElementById('sim_route_id').value;
            let km = parseFloat(document.getElementById('calc-km').value);
            
            // 2. Find the specific inputs for THAT route using the unique ID
            // Notice we look for 'base_km_' + routeId
            let baseKmInput = document.getElementById('base_km_' + routeId);
            let baseFareInput = document.getElementById('base_fare_' + routeId);
            let perKmInput = document.getElementById('per_km_' + routeId);

            if (baseKmInput && baseFareInput && perKmInput) {
                let baseKm = parseFloat(baseKmInput.value);
                let baseFare = parseFloat(baseFareInput.value);
                let perKm = parseFloat(perKmInput.value);

                if (!isNaN(km) && !isNaN(baseKm)) {
                    let total = baseFare;
                    
                    if (km > baseKm) {
                        total += (km - baseKm) * perKm;
                    }
                    
                    document.getElementById('calc-result').innerText = '₱ ' + total.toFixed(2);
                }
            } else {
                alert("Could not find matrix data. Please refresh.");
            }
        }
    </script>
@endsection