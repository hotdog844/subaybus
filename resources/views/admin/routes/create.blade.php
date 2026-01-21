@extends('layouts.admin')

@section('title', 'Create New Route')

{{-- 1. LOAD LEAFLET CSS --}}
@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #admin-map { 
            height: 600px; 
            width: 100%; 
            border-radius: 12px; 
            border: 2px solid #e2e8f0; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1;
            cursor: crosshair; /* Crosshair cursor para alam na pwedeng mag-click */
        }
        .control-panel {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        /* Custom Dot for Waypoints */
        .waypoint-icon {
            background-color: #3b82f6;
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.4);
        }
    </style>
@endsection

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create New Route</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.routes.store') }}" method="POST" id="routeForm">
            @csrf

            {{-- BASIC INFO --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Route Name</label>
                    <input type="text" name="name" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none" placeholder="e.g. City Loop - North" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Description</label>
                    <input type="text" name="description" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-lg p-3 focus:ring-2 focus:blue-500 outline-none" placeholder="Optional notes">
                </div>
            </div>

            {{-- MAP CONTROLS --}}
            <div class="control-panel">
                <h3 class="font-bold text-gray-700 mb-2">🗺️ Multipoint Route Planner</h3>
                <p class="text-sm text-gray-500 mb-4">
                    1. Drag <b>Green (Start)</b> and <b>Red (End)</b> markers.<br>
                    2. <b>Click anywhere on the road</b> to create stops/corners (Blue Dots).<br>
                    3. The blue line will curve to follow your clicks.
                </p>
                
                <div class="flex gap-4 text-sm font-mono bg-white p-3 rounded border border-gray-200">
                    <div class="flex-1">
                        <span class="text-green-600 font-bold">Origin:</span>
                        <input type="text" name="origin" id="input_origin_name" placeholder="Origin Name" class="text-sm border border-gray-300 rounded p-1 w-full mt-1" required>
                        <input type="hidden" name="origin_lat" id="origin_lat">
                        <input type="hidden" name="origin_lng" id="origin_lng">
                    </div>
                    <div class="flex-1">
                        <span class="text-red-600 font-bold">Dest:</span>
                        <input type="text" name="destination" id="input_dest_name" placeholder="Destination Name" class="text-sm border border-gray-300 rounded p-1 w-full mt-1" required>
                        <input type="hidden" name="destination_lat" id="destination_lat">
                        <input type="hidden" name="destination_lng" id="destination_lng">
                    </div>
                </div>
                
                {{-- AUTO-CALCULATED FIELDS --}}
                <input type="hidden" name="distance" id="input_distance">
                <input type="hidden" name="path_data" id="path_data">
            </div>

            {{-- MAP ACTIONS --}}
            <div class="mb-2 flex justify-end">
                <button type="button" id="clearPointsBtn" class="bg-gray-500 text-white px-3 py-1 rounded text-sm hover:bg-gray-600 transition flex items-center gap-2">
                    <i class="fas fa-undo"></i> Reset / Clear Waypoints
                </button>
            </div>

            {{-- THE MAP --}}
            <div id="admin-map"></div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transform transition hover:-translate-y-0.5">
                    Save Route Configuration
                </button>
            </div>
        </form>
    </div>

    {{-- 2. LEAFLET SCRIPT ONLY --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- 1. SETUP MAP ---
            const defaultLat = 11.5853; 
            const defaultLng = 122.7511; 

            const map = L.map('admin-map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // --- 2. ICONS ---
            const originIcon = L.divIcon({
                className: 'bg-transparent',
                html: '<div class="w-8 h-8 bg-green-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center text-white font-bold text-xs">A</div>',
                iconSize: [32, 32], iconAnchor: [16, 32]
            });

            const destIcon = L.divIcon({
                className: 'bg-transparent',
                html: '<div class="w-8 h-8 bg-red-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center text-white font-bold text-xs">B</div>',
                iconSize: [32, 32], iconAnchor: [16, 32]
            });

            const waypointIcon = L.divIcon({
                className: 'bg-transparent',
                html: '<div class="waypoint-icon" style="width:12px; height:12px;"></div>',
                iconSize: [12, 12], iconAnchor: [6, 6]
            });

            // --- 3. STATE ---
            // Start positions (Default)
            let startPos = [defaultLat, defaultLng];
            let endPos = [defaultLat + 0.01, defaultLng + 0.01];

            let originMarker = L.marker(startPos, { draggable: true, icon: originIcon }).addTo(map);
            let destMarker = L.marker(endPos, { draggable: true, icon: destIcon }).addTo(map);
            let routeLine = L.polyline([], { color: '#3b82f6', weight: 6, opacity: 0.8 }).addTo(map);

            let waypoints = []; 
            let waypointMarkers = [];

            // Initialize Inputs
            updateInputs(originMarker.getLatLng(), 'origin');
            updateInputs(destMarker.getLatLng(), 'destination');
            calculatePath(); // Initial straight line

            // --- 4. LISTENERS ---

            // Drag Start (Green)
            originMarker.on('dragend', function (e) {
                updateInputs(e.target.getLatLng(), 'origin');
                calculatePath();
            });

            // Drag End (Red)
            destMarker.on('dragend', function (e) {
                updateInputs(e.target.getLatLng(), 'destination');
                calculatePath();
            });

            // Click Map (Add Blue Dot)
            map.on('click', function(e) {
                // Add to data
                waypoints.push(e.latlng);

                // Add visual
                const mk = L.marker(e.latlng, { icon: waypointIcon }).addTo(map);
                waypointMarkers.push(mk);

                // Recalculate
                calculatePath();
            });

            // Reset Button
            document.getElementById('clearPointsBtn').addEventListener('click', function() {
                waypoints = [];
                waypointMarkers.forEach(m => map.removeLayer(m));
                waypointMarkers = [];
                calculatePath();
            });

            function updateInputs(latlng, type) {
                document.getElementById(type + '_lat').value = latlng.lat.toFixed(6);
                document.getElementById(type + '_lng').value = latlng.lng.toFixed(6);
            }

            // --- 5. OSRM CALCULATION ---
            async function calculatePath() {
                const oLat = originMarker.getLatLng().lat;
                const oLng = originMarker.getLatLng().lng;
                const dLat = destMarker.getLatLng().lat;
                const dLng = destMarker.getLatLng().lng;

                // Build Coordinate String: Start -> Waypoints -> End
                // OSRM requires: {lng},{lat}
                let coordsString = `${oLng},${oLat}`;
                
                waypoints.forEach(pt => {
                    coordsString += `;${pt.lng},${pt.lat}`;
                });

                coordsString += `;${dLng},${dLat}`;

                const url = `https://router.project-osrm.org/route/v1/driving/${coordsString}?overview=full&geometries=geojson`;

                try {
                    const response = await fetch(url);
                    if(!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();

                    if(data.routes && data.routes.length > 0) {
                        const routeData = data.routes[0];
                        const rawCoords = routeData.geometry.coordinates;
                        
                        // Flip [Lng, Lat] to [Lat, Lng] for Leaflet
                        const leafletCoords = rawCoords.map(c => [c[1], c[0]]);

                        // Draw Line
                        routeLine.setLatLngs(leafletCoords);
                        
                        // Save Data
                        document.getElementById('path_data').value = JSON.stringify(leafletCoords);
                        document.getElementById('input_distance').value = (routeData.distance / 1000).toFixed(2);
                    }
                } catch (error) {
                    console.error("Routing Error:", error);
                    // Fallback to straight line if offline
                    routeLine.setLatLngs([[oLat, oLng], ...waypoints, [dLat, dLng]]);
                }
            }
        });
    </script>
@endsection