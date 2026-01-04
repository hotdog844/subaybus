@extends('layouts.admin')

@section('title', 'Manage Stops for ' . $route->name)

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #stop-map { height: 500px; width: 100%; border-radius: 12px; z-index: 1; border: 2px solid #e2e8f0; }
        .stop-list { max-height: 500px; overflow-y: auto; }
        /* Animation for new markers */
        .leaflet-marker-icon { transition: all 0.2s; }
    </style>
@endsection

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manage Stops</h2>
        <p class="text-gray-500 text-sm">Route: <span class="font-bold text-blue-600">{{ $route->name }}</span></p>
    </div>
    <a href="{{ route('admin.routes.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">
        <i class="fas fa-arrow-left mr-1"></i> Back to Routes
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="mb-2 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">📍 Interactive Map</h3>
            <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded">Click on the line to add a stop</span>
        </div>
        <div id="stop-map"></div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col">
        
        {{-- ADDED: ERROR MESSAGE BLOCK --}}
        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm border border-red-100">
                <strong class="font-bold">Whoops!</strong>
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ADDED: SUCCESS MESSAGE BLOCK --}}
        @if(session('success'))
            <div class="bg-green-50 text-green-600 p-3 rounded-lg mb-4 text-sm font-bold border border-green-100">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Existing Stops</h3>
        
        <div class="stop-list flex-grow space-y-2 pr-1 custom-scrollbar">
            @forelse($stops as $index => $stop)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-800 text-sm">{{ $stop->name }}</div>
                            <div class="text-[10px] text-gray-400">{{ number_format($stop->latitude, 5) }}, {{ number_format($stop->longitude, 5) }}</div>
                        </div>
                    </div>
                    <form action="{{ route('admin.routes.stops.destroy', $stop->id) }}" method="POST" onsubmit="return confirm('Remove this stop?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-300 hover:text-red-500 transition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 text-sm">
                    No stops added yet.<br>Click the map to start!
                </div>
            @endforelse
        </div>
    </div>
</div>

<div id="addStopModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-96 transform transition-all scale-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Stop</h3>
        
        <form action="{{ route('admin.routes.stops.store', $route->id) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stop Name</label>
                <input type="text" name="name" id="modal_stop_name" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. Provincial Hospital" required autofocus>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-6">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase">Lat</label>
                    <input type="text" name="latitude" id="modal_lat" class="w-full bg-gray-100 border border-gray-200 rounded p-2 text-xs text-gray-600" readonly>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase">Lng</label>
                    <input type="text" name="longitude" id="modal_lng" class="w-full bg-gray-100 border border-gray-200 rounded p-2 text-xs text-gray-600" readonly>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg font-medium text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-sm shadow-md">Save Stop</button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPTS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Initialize Map (Use Origin as center, or default)
        var centerLat = {{ $route->origin_lat ?? 11.5853 }};
        var centerLng = {{ $route->origin_lng ?? 122.7511 }};
        var map = L.map('stop-map').setView([centerLat, centerLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

        // 2. Draw the Blue Route Line (Visual Reference)
        var pathData = {!! $route->path_data ?? '[]' !!}; // Load JSON from DB
        
        if (pathData.length > 0) {
            var polyline = L.polyline(pathData, {
                color: '#3b82f6', // Tailwind Blue-500
                weight: 5,
                opacity: 0.6,
                lineCap: 'round'
            }).addTo(map);
            
            map.fitBounds(polyline.getBounds(), {padding: [50, 50]});
        }

        // 3. Display Existing Stops
        var stops = {!! json_encode($stops) !!};
        var stopIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color: #2563eb; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);'></div>",
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });

        stops.forEach(function(stop) {
            L.marker([stop.latitude, stop.longitude], {icon: stopIcon})
             .addTo(map)
             .bindPopup("<b>" + stop.name + "</b>");
        });

        // 4. Click to Add New Stop
        var newMarker = null;

        map.on('click', function(e) {
            var lat = e.latlng.lat.toFixed(6);
            var lng = e.latlng.lng.toFixed(6);

            // Move or Create Marker
            if (newMarker) {
                newMarker.setLatLng(e.latlng);
            } else {
                newMarker = L.marker(e.latlng, {draggable: true}).addTo(map);
            }

            // Open Modal
            openModal(lat, lng);
        });

        // Modal Logic
        window.openModal = function(lat, lng) {
            document.getElementById('modal_lat').value = lat;
            document.getElementById('modal_lng').value = lng;
            document.getElementById('modal_stop_name').value = ''; // Clear name
            document.getElementById('addStopModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('modal_stop_name').focus(), 100);
        }

        window.closeModal = function() {
            document.getElementById('addStopModal').classList.add('hidden');
            if(newMarker) {
                map.removeLayer(newMarker); // Remove the temp marker if canceled
                newMarker = null;
            }
        }
    });
</script>
@endsection