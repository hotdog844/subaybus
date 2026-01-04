@extends('layouts.admin')

@section('title', 'Create Professional Route')

{{-- 1. LOAD MAP ASSETS IN HEAD --}}
@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

    <style>
        #admin-map { 
            height: 500px; 
            width: 100%; 
            border-radius: 12px; 
            border: 2px solid #e2e8f0; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        .control-panel {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        .leaflet-routing-container { display: none !important; } 
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
                <h3 class="font-bold text-gray-700 mb-2">🗺️ Interactive Route Planner</h3>
                <p class="text-sm text-gray-500 mb-4">
                    1. Click the map to set <b>Origin (Green)</b>.<br>
                    2. Click again to set <b>Destination (Red)</b>.<br>
                    3. <b>Type the specific name</b> of the location in the boxes below.
                </p>
                
                <div class="flex gap-4 text-sm font-mono bg-white p-3 rounded border border-gray-200">
                    
                    {{-- ORIGIN INPUTS (UPDATED: Visible & Editable) --}}
                    <div class="flex-1">
                        <span class="text-green-600 font-bold">Origin:</span> <span id="lbl_origin" class="text-xs text-gray-400">Click Map</span>
                        
                        <input type="text" name="origin" id="input_origin_name" placeholder="Name this location (e.g. Pueblo Terminal)" class="text-sm border border-gray-300 rounded p-2 w-full mt-1 bg-gray-50 focus:bg-white outline-none focus:ring-1 focus:ring-green-500" required>
                        
                        <input type="hidden" name="origin_lat" id="input_origin_lat">
                        <input type="hidden" name="origin_lng" id="input_origin_lng">
                        <input type="hidden" name="distance" id="input_distance">
                    </div>

                    {{-- DESTINATION INPUTS (UPDATED: Visible & Editable) --}}
                    <div class="flex-1">
                        <span class="text-red-600 font-bold">Dest:</span> <span id="lbl_dest" class="text-xs text-gray-400">Click Map</span>
                        
                        <input type="text" name="destination" id="input_dest_name" placeholder="Name this location (e.g. City Hall)" class="text-sm border border-gray-300 rounded p-2 w-full mt-1 bg-gray-50 focus:bg-white outline-none focus:ring-1 focus:ring-red-500" required>
                        
                        <input type="hidden" name="destination_lat" id="input_dest_lat">
                        <input type="hidden" name="destination_lng" id="input_dest_lng">
                    </div>
                </div>

                <input type="hidden" name="path_data" id="path_data_input">
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

    {{-- SCRIPTS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Initialize Map
            var map = L.map('admin-map').setView([11.5853, 122.7511], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // 2. Variables
            var originMarker = null;
            var destMarker = null;
            var routeControl = null;

            // 3. Icons
            var greenIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });

            var redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });

            // 4. Click Handler
            map.on('click', function(e) {
                if (!originMarker) {
                    // Set Origin
                    originMarker = L.marker(e.latlng, {draggable: true, icon: greenIcon}).addTo(map).bindPopup("Origin").openPopup();
                    updateCoordinates('origin', e.latlng);
                    
                    originMarker.on('dragend', function(event) {
                        updateCoordinates('origin', event.target.getLatLng());
                        calculateRoute();
                    });

                } else if (!destMarker) {
                    // Set Destination
                    destMarker = L.marker(e.latlng, {draggable: true, icon: redIcon}).addTo(map).bindPopup("Destination").openPopup();
                    updateCoordinates('dest', e.latlng);
                    
                    destMarker.on('dragend', function(event) {
                        updateCoordinates('dest', event.target.getLatLng());
                        calculateRoute();
                    });

                    calculateRoute();
                }
            });

            // 5. Update Hidden Inputs
            function updateCoordinates(type, latlng) {
                var lat = latlng.lat.toFixed(6);
                var lng = latlng.lng.toFixed(6);
                var str = lat + ", " + lng;

                if (type === 'origin') {
                    document.getElementById('lbl_origin').innerText = str;
                    document.getElementById('input_origin_lat').value = lat;
                    document.getElementById('input_origin_lng').value = lng;
                    
                    // Logic: Only auto-fill if the user hasn't typed anything yet
                    var nameInput = document.getElementById('input_origin_name');
                    if(nameInput.value === "") {
                        nameInput.value = "Pinned Location"; 
                    }

                } else {
                    document.getElementById('lbl_dest').innerText = str;
                    document.getElementById('input_dest_lat').value = lat;
                    document.getElementById('input_dest_lng').value = lng;
                    
                    var nameInput = document.getElementById('input_dest_name');
                    if(nameInput.value === "") {
                        nameInput.value = "Pinned Location"; 
                    }
                }
            }

            // 6. Calculate Route
            function calculateRoute() {
                if (!originMarker || !destMarker) return;

                if (routeControl) {
                    map.removeControl(routeControl);
                }

                routeControl = L.Routing.control({
                    waypoints: [
                        originMarker.getLatLng(),
                        destMarker.getLatLng()
                    ],
                    routeWhileDragging: false,
                    addWaypoints: false,
                    createMarker: function() { return null; }, 
                    lineOptions: {
                        styles: [{color: 'blue', opacity: 0.6, weight: 6}]
                    },
                    show: false 
                }).addTo(map);

                // Extract the coordinates AND DISTANCE from the calculated route
                routeControl.on('routesfound', function(e) {
                    var routes = e.routes;
                    var summary = routes[0].summary;
                    
                    // 1. Get Coordinates for the blue line
                    var coordinates = routes[0].coordinates; 
                    var simplifiedPath = coordinates.map(c => [c.lat, c.lng]);
                    document.getElementById('path_data_input').value = JSON.stringify(simplifiedPath);
                    
                    // 2. Get Distance (New!)
                    // summary.totalDistance is in meters. Convert to KM.
                    var km = (summary.totalDistance / 1000).toFixed(2);
                    document.getElementById('input_distance').value = km;
                    
                    console.log("Route calculated: " + km + " km");
                });
            }

        });
    </script>
@endsection