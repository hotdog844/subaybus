@extends('layouts.admin')



@section('title', 'Edit Route & Stops')



@section('styles')

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>

    <style>

        #admin-map {

            height: 650px;

            width: 100%;

            border-radius: 12px;

            border: 2px solid #e2e8f0;

            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);

            z-index: 1;

            cursor: crosshair;

        }

        .control-panel {

            background: #fff;

            padding: 20px;

            border-radius: 12px;

            margin-bottom: 20px;

            border: 1px solid #e2e8f0;

            box-shadow: 0 2px 4px rgba(0,0,0,0.05);

        }

        .mode-btn {

            transition: all 0.2s;

        }

        .mode-btn.active {

            background-color: #3b82f6; /* Blue-500 */

            color: white;

            border-color: #3b82f6;

        }

        /* Custom Marker Styles */

        .stop-marker-icon {

            background-color: #ef4444; /* Red */

            border: 2px solid white;

            border-radius: 50%;

            box-shadow: 0 2px 5px rgba(0,0,0,0.5);

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 14px;

        }

        .path-node-icon {

            background-color: #3b82f6; /* Blue */

            border: 2px solid white;

            border-radius: 50%;

            width: 10px; height: 10px;

        }

    </style>

@endsection



@section('content')

@if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">May naging problema!</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">

        <h2 class="text-2xl font-bold text-gray-800">Manage Route: {{ $route->name }}</h2>

        <a href="{{ route('admin.routes.index') }}" class="text-gray-500 hover:text-gray-700">

            <i class="fas fa-arrow-left"></i> Back

        </a>

    </div>



    <form action="{{ route('admin.routes.update', $route->id) }}" method="POST" id="routeForm">

        @csrf

        @method('PUT')



        {{-- TOP CONTROLS --}}

        {{-- TOP CONTROLS --}}
        <div class="control-panel">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Route Name</label>
                    <input type="text" name="name" value="{{ old('name', $route->name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Color (Hex)</label>
                    <input type="color" name="color" value="{{ old('color', $route->color ?? '#3b82f6') }}" class="w-full h-12 p-1 rounded-lg border border-gray-200 cursor-pointer">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Origin Name (Start)</label>
                    <input type="text" name="origin" value="{{ old('origin', $route->origin) }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. RCITT Terminal" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Destination Name (End)</label>
                    <input type="text" name="destination" value="{{ old('destination', $route->destination) }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. Brgy. Libas" required>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 mt-2">
                <div class="flex items-center gap-4 mb-2">
                    <span class="font-bold text-gray-700 uppercase text-xs tracking-wider">Editor Mode:</span>
                    
                    <button type="button" id="modePath" class="mode-btn active px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-600 text-sm font-bold shadow-sm hover:bg-gray-50">
                        <i class="fas fa-route mr-2"></i> Draw Path (Blue Line)
                    </button>
                    
                    <button type="button" id="modeStops" class="mode-btn px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-600 text-sm font-bold shadow-sm hover:bg-gray-50">
                        <i class="fas fa-map-pin mr-2"></i> Manage Stops (Red Pins)
                    </button>
                </div>
                
                <p id="helpText" class="text-sm text-blue-600 bg-blue-50 p-2 rounded border border-blue-100">
                    <i class="fas fa-info-circle"></i> <b>Path Mode:</b> Click map to curve the blue line. Drag Green/Red markers for Start/End.
                </p>
            </div>
            <input type="hidden" name="distance" id="distance" value="{{ old('distance', $route->distance ?? 0) }}">
            <input type="hidden" name="path_data" id="path_data" value="{{ old('path_data', json_encode($route->path_data)) }}">
            <input type="hidden" name="stops_json" id="stops_json" value="{{ old('stops_json', json_encode($route->stops)) }}">
            
            <input type="hidden" name="origin_lat" id="origin_lat" value="{{ old('origin_lat', $route->origin_lat) }}">
            <input type="hidden" name="origin_lng" id="origin_lng" value="{{ old('origin_lng', $route->origin_lng) }}">
            <input type="hidden" name="destination_lat" id="destination_lat" value="{{ old('destination_lat', $route->destination_lat) }}">
            <input type="hidden" name="destination_lng" id="destination_lng" value="{{ old('destination_lng', $route->destination_lng) }}">
        </div>



        {{-- THE MAP --}}

        <div class="relative">

            <div id="admin-map"></div>

           

            <button type="button" id="clearBtn" class="absolute top-4 right-4 z-[500] bg-white text-red-500 font-bold py-2 px-4 rounded shadow hover:bg-red-50 border border-red-100">

                <i class="fas fa-trash-alt"></i> Clear Current Layer

            </button>

        </div>



        <div class="mt-8 flex justify-end">

            <button type="submit" onclick="prepareData()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-10 rounded-xl shadow-lg transform transition hover:-translate-y-1 text-lg">

                <i class="fas fa-save mr-2"></i> Save Changes

            </button>

        </div>

    </form>



@endsection



@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // --- 1. DIRECT DATA INJECTION (Ito ang Solusyon) ---
    // Kinukuha natin ang saved path direkta mula sa database papunta sa JS
    const savedPathFromDb = @json($route->path_data); 

    document.addEventListener('DOMContentLoaded', function () {
        
        // --- 2. INITIAL SETUP ---
        const map = L.map('admin-map').setView([11.5853, 122.7511], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

        // ... (Ipagpatuloy ang dating variables dito) ...
        let currentMode = 'path';
        let pathPoints = []; 
        let pathMarkers = []; 
        let routeLine = L.polyline([], { color: '#3b82f6', weight: 6 }).addTo(map);
        let originMarker, destMarker;

        // Stop Variables

        let stopsData = []; // Array of objects: {name, lat, lng}

        let stopMarkers = []; // Array of Leaflet markers



        // --- 3. ICONS ---

        const originIcon = L.divIcon({ className: '', html: '<div style="background:#22c55e; width:30px; height:30px; border-radius:50%; border:3px solid white; display:flex; justify-content:center; align-items:center; box-shadow:0 3px 5px rgba(0,0,0,0.3); color:white; font-weight:bold;">A</div>', iconSize: [30, 30], iconAnchor: [15, 30] });

        const destIcon = L.divIcon({ className: '', html: '<div style="background:#ef4444; width:30px; height:30px; border-radius:50%; border:3px solid white; display:flex; justify-content:center; align-items:center; box-shadow:0 3px 5px rgba(0,0,0,0.3); color:white; font-weight:bold;">B</div>', iconSize: [30, 30], iconAnchor: [15, 30] });

        const stopIcon = L.divIcon({ className: 'stop-marker-icon', html: '<i class="fas fa-bus"></i>', iconSize: [30, 30], iconAnchor: [15, 30], popupAnchor: [0, -30] });

        const nodeIcon = L.divIcon({ className: 'bg-transparent', html: '<div class="path-node-icon"></div>', iconSize: [10, 10], iconAnchor: [5, 5] });



        // --- 4. INITIALIZE FROM DB ---

        initPath();

        initStops();



        // --- 5. MODE SWITCHER LOGIC ---

        const btnPath = document.getElementById('modePath');

        const btnStops = document.getElementById('modeStops');

        const helpText = document.getElementById('helpText');



        btnPath.addEventListener('click', () => setMode('path'));

        btnStops.addEventListener('click', () => setMode('stops'));



        function setMode(mode) {

            currentMode = mode;

            if(mode === 'path') {

                btnPath.classList.add('active'); btnStops.classList.remove('active');

                helpText.innerHTML = '<i class="fas fa-info-circle"></i> <b>Path Mode:</b> Click anywhere to curve the line. Drag A/B markers.';

                routeLine.setStyle({ opacity: 1 });

                stopMarkers.forEach(m => m.setOpacity(0.5)); // Dim stops

            } else {

                btnStops.classList.add('active'); btnPath.classList.remove('active');

                helpText.innerHTML = '<i class="fas fa-info-circle"></i> <b>Stops Mode:</b> Click along the line to add a Bus Stop. <span class="text-red-500 font-bold">Right-click a pin to delete it.</span>';

                routeLine.setStyle({ opacity: 0.5 }); // Dim line

                stopMarkers.forEach(m => m.setOpacity(1));

            }

        }



        // --- 6. MAP CLICK HANDLER ---

        map.on('click', function(e) {

            if (currentMode === 'path') {

                // Add Path Node

                pathPoints.push(e.latlng);

                drawPathNodes();

                calculatePath();

            } else {

                // Add Bus Stop

                addStopMarker(e.latlng, "New Stop");

            }

        });



        document.getElementById('clearBtn').addEventListener('click', () => {

            if(currentMode === 'path') {

                if(confirm("Clear the drawn path? (Start/End will remain)")) {

                    pathPoints = [];

                    drawPathNodes();

                    calculatePath();

                }

            } else {

                if(confirm("Delete ALL bus stops?")) {

                    stopMarkers.forEach(m => map.removeLayer(m));

                    stopMarkers = [];

                    stopsData = [];

                }

            }

        });

        // ============================================================
        // LOGIC A: PATH DRAWING (Fixed: Prioritize Saved Data)
        // ============================================================
        function initPath() {
            // Get Coords form Inputs
            let oLat = document.getElementById('origin_lat').value || 11.5853;
            let oLng = document.getElementById('origin_lng').value || 122.7511;
            let dLat = document.getElementById('destination_lat').value || 11.5953;
            let dLng = document.getElementById('destination_lng').value || 122.7611;

            // 1. Setup Start/End Markers
            originMarker = L.marker([oLat, oLng], { draggable: true, icon: originIcon }).addTo(map);
            destMarker = L.marker([dLat, dLng], { draggable: true, icon: destIcon }).addTo(map);

            originMarker.on('dragend', updateEndpoints);
            destMarker.on('dragend', updateEndpoints);

            // 2. CHECK DIRECT VARIABLE (Ito ang nag-aayos ng issue mo)
            if (savedPathFromDb) {
                console.log("Checking saved path...", savedPathFromDb);
                
                let pointsToDraw = savedPathFromDb;

                // Kung string pa siya, i-parse natin
                if (typeof pointsToDraw === 'string') {
                    try {
                        pointsToDraw = JSON.parse(pointsToDraw);
                    } catch (e) {
                        console.error("JSON Parse Error:", e);
                        pointsToDraw = [];
                    }
                }

                // KUNG VALID ARRAY, I-DRAW NATIN AT WAG NA MAG-CALCULATE
                if (Array.isArray(pointsToDraw) && pointsToDraw.length > 0) {
                    console.log("✅ Using SAVED path from Database (" + pointsToDraw.length + " points)");
                    
                    // A. Update the Blue Line
                    routeLine.setLatLngs(pointsToDraw);
                    
                    // B. Update the "Blue Dots" (Nodes) para ma-edit mo ulit
                    // Convert array [lat, lng] to Leaflet objects
                    pathPoints = pointsToDraw.map(p => L.latLng(p[0], p[1]));
                    
                    // C. Draw the drag handles (dots)
                    drawPathNodes();

                    // D. Update Hidden Input para ready na ulit i-save
                    document.getElementById('path_data').value = JSON.stringify(pointsToDraw);
                    
                    // IMPORTANT: Stop here! Do NOT call calculatePath()
                    return; 
                }
            }

            // 3. Fallback: Kung walang saved data (New Route), saka lang mag-calculate
            console.log("⚠️ No saved path found. Calculating default OSRM route...");
            calculatePath();
        }



        function updateEndpoints() {

            const o = originMarker.getLatLng();

            const d = destMarker.getLatLng();

            document.getElementById('origin_lat').value = o.lat;

            document.getElementById('origin_lng').value = o.lng;

            document.getElementById('destination_lat').value = d.lat;

            document.getElementById('destination_lng').value = d.lng;

            calculatePath();

        }



        function drawPathNodes() {

            pathMarkers.forEach(m => map.removeLayer(m));

            pathMarkers = [];

            pathPoints.forEach(pt => {

                let m = L.marker(pt, { icon: nodeIcon }).addTo(map);

                pathMarkers.push(m);

            });

        }



        async function calculatePath() {
            const o = originMarker.getLatLng();
            const d = destMarker.getLatLng();
            
            // Construct OSRM URL...
            let coords = `${o.lng},${o.lat}`;
            pathPoints.forEach(p => coords += `;${p.lng},${p.lat}`);
            coords += `;${d.lng},${d.lat}`;

            const url = `https://router.project-osrm.org/route/v1/driving/${coords}?overview=full&geometries=geojson`;

            try {
                const res = await fetch(url);
                const data = await res.json();
                if(data.routes && data.routes.length > 0) {
                    const geo = data.routes[0].geometry.coordinates;
                    const latlngs = geo.map(c => [c[1], c[0]]);
                    routeLine.setLatLngs(latlngs);
                    
                    document.getElementById('path_data').value = JSON.stringify(latlngs);

                    // --- DAGDAG: CALCULATE DISTANCE ---
                    // Ang OSRM ay nagbibigay ng distance in meters. I-convert natin sa KM.
                    const distMeters = data.routes[0].distance; 
                    const distKm = (distMeters / 1000).toFixed(2); // Convert to KM with 2 decimals
                    
                    // Ipasok sa hidden input
                    document.getElementById('distance').value = distKm;
                    
                    console.log("Calculated Distance:", distKm + " km");
                }
            } catch(err) { console.error("OSRM Error", err); }
        }



        // ============================================================

        // LOGIC B: BUS STOPS MANAGEMENT (New Feature)

        // ============================================================

        function initStops() {

            const rawStops = document.getElementById('stops_json').value;

            try {

                if(rawStops) {

                    const parsed = JSON.parse(rawStops);

                    if(Array.isArray(parsed)) {

                        parsed.forEach(s => {

                            // Handle if s is just an object or a model

                            const lat = s.latitude || s.lat;

                            const lng = s.longitude || s.lng;

                            addStopMarker({lat: lat, lng: lng}, s.name);

                        });

                    }

                }

            } catch(e) { console.error("Stops JSON Error", e); }

        }



        // ============================================================
        // FIX: ROBUST STOP MARKER LOGIC (With Enter & Right-Click Fix)
        // ============================================================
        function addStopMarker(latlng, name = "New Stop") {
            const marker = L.marker(latlng, { icon: stopIcon, draggable: true }).addTo(map);
            
            // 1. POPUP HTML (Lagyan ng type="button" para hindi mag-submit kusa)
            const popupContent = document.createElement('div');
            popupContent.innerHTML = `
                <div class="p-1" style="min-width: 200px;">
                    <label class="block text-xs font-bold text-gray-500 uppercase">Stop Name</label>
                    
                    <input type="text" value="${name}" class="stop-name-input border border-gray-300 rounded p-1 text-sm w-full mt-1 mb-2 outline-none focus:border-blue-500" placeholder="Type name & press Enter">
                    
                    <button type="button" class="btn-delete-stop bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-2 py-2 rounded w-full transition">
                        <i class="fas fa-trash-alt"></i> Delete Stop
                    </button>
                    
                    <div class="text-[10px] text-gray-400 text-center mt-2 italic">
                        (Or Right-Click pin to delete)
                    </div>
                </div>
            `;

            // 2. ELEMENT HANDLERS
            const input = popupContent.querySelector('.stop-name-input');
            const delBtn = popupContent.querySelector('.btn-delete-stop');

            // A. UPDATE NAME ON TYPE
            input.addEventListener('input', (e) => {
                marker.stopName = e.target.value; 
            });

            // B. FIX ENTER KEY (Pigilan ang Form Submit!)
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault(); // <--- ITO ANG SOLUSYON SA RELOAD
                    marker.closePopup(); // Isara na lang ang popup pagtapos mag-type
                }
            });

            // C. DELETE BUTTON LOGIC
            delBtn.addEventListener('click', () => {
                removeStopMarker(marker);
            });

            // 3. ATTACH POPUP
            marker.bindPopup(popupContent);
            marker.stopName = name; 
            
            // 4. ADD RIGHT-CLICK DELETE (Context Menu)
            marker.on('contextmenu', function() {
                removeStopMarker(marker);
            });

            // Add to tracking array
            stopMarkers.push(marker);
            
            // Open popup immediately if it's a new add
            if(name === "New Stop") {
                setTimeout(() => {
                    marker.openPopup();
                    // Focus on input field automatically
                    setTimeout(() => {
                        const openInput = document.querySelector('.leaflet-popup .stop-name-input');
                        if(openInput) openInput.focus();
                    }, 100);
                }, 100);
            }
        }

        // Helper function para malinis ang pag-delete
        function removeStopMarker(marker) {
            map.removeLayer(marker); // Tanggalin sa map
            stopMarkers = stopMarkers.filter(m => m !== marker); // Tanggalin sa array
        }



        // ============================================================

        // FINAL: PREPARE DATA FOR SUBMIT

        // ============================================================

        window.prepareData = function() {

            // Collect all stop data from markers

            const finalStops = stopMarkers.map(m => ({

                name: m.stopName || "Unnamed Stop",

                lat: m.getLatLng().lat,

                lng: m.getLatLng().lng

            }));



            document.getElementById('stops_json').value = JSON.stringify(finalStops);

        };



    });

</script>

@endsection