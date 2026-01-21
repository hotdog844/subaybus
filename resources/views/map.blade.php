<!DOCTYPE html>
<html>
<head>
    <title>📍 SubayBus Real-Time Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html, body { margin: 0; padding: 0; height: 100%; }
        #map { height: 100vh; width: 100%; }
        
        /* Custom Popup Styling */
        .leaflet-popup-content-wrapper { border-radius: 12px; padding: 0; overflow: hidden; }
        .leaflet-popup-content { margin: 0; width: 200px !important; }

        /* Stop Marker Style */
        .stop-label {
            background: white;
            border: 1px solid #666;
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 10px;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    
    <script>
        // 1. Initialize Map (Centered on Roxas City)
        let map = L.map('map').setView([11.5853, 122.7517], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: 'SubayBus Tracker'
        }).addTo(map);

        // 2. Custom Icons
        const busIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3448/3448339.png', 
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        // Store references to update them later
        let busMarkers = {};
        let routeLayers = [];

        // ===========================================
        // FUNCTION A: DRAW ROUTES & STOPS (Run Once)
        // ===========================================
        async function fetchRoutes() {
            try {
                // Connect to the Controller we updated earlier
                const response = await fetch('/api/routes'); 
                const routes = await response.json();

                routes.forEach(route => {
                    // 1. Draw the Path (Colored Line)
                    // We need to extract just the [lat, lng] for the polyline
                    const latLngs = route.path.map(stop => [stop.lat, stop.lng]);
                    
                    const polyline = L.polyline(latLngs, {
                        color: route.color, // Red, Blue, or Green from DB
                        weight: 4,
                        opacity: 0.7
                    }).addTo(map);

                    // 2. Draw the Stops (White Circles)
                    route.path.forEach(stop => {
                        const circle = L.circleMarker([stop.lat, stop.lng], {
                            radius: 6,
                            fillColor: "#fff",
                            color: "#000",
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 1
                        }).addTo(map);

                        // Popup for the Stop Name
                        circle.bindPopup(`
                            <div class="text-center">
                                <strong class="text-sm">${stop.name}</strong><br>
                                <span class="text-xs text-gray-500">Stop #${stop.sequence}</span>
                            </div>
                        `);
                    });
                });

                console.log("Routes & Stops Loaded!");

            } catch (error) {
                console.error("Error loading routes:", error);
            }
        }

        // ===========================================
        // FUNCTION B: TRACK BUSES (Real-Time)
        // ===========================================
        async function fetchBusLocations() {
            try {
                const response = await fetch('/api/live-tracking'); 
                const buses = await response.json();

                buses.forEach(bus => {
                    if (!bus.lat || !bus.lng) return;

                    let statusColor = bus.status === 'active' ? 'green' : 
                                     (bus.status === 'full' ? 'orange' : 'red');

                    const popupContent = `
                        <div class="bg-gray-800 text-white p-4">
                            <h3 class="font-bold text-lg text-blue-400 mb-1">${bus.bus_number}</h3>
                            <p class="text-xs text-gray-400 mb-3">Last Update: ${new Date(bus.updated_at).toLocaleTimeString()}</p>
                            
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="bg-gray-700 p-2 rounded text-center">
                                    <span class="block text-xl font-bold">${bus.passenger_count}</span>
                                    <span class="text-xs text-gray-400">Passengers</span>
                                </div>
                                <div class="bg-gray-700 p-2 rounded text-center flex flex-col justify-center items-center">
                                    <span class="text-${statusColor}-400 font-bold uppercase text-xs">${bus.status}</span>
                                    <span class="text-xs text-gray-400">Status</span>
                                </div>
                            </div>
                        </div>
                    `;

                    if (busMarkers[bus.id]) {
                        busMarkers[bus.id].setLatLng([bus.lat, bus.lng]);
                        if (!busMarkers[bus.id].isPopupOpen()) {
                            busMarkers[bus.id].setPopupContent(popupContent);
                        }
                    } else {
                        busMarkers[bus.id] = L.marker([bus.lat, bus.lng], { icon: busIcon })
                            .addTo(map)
                            .bindPopup(popupContent);
                    }
                });

            } catch (error) {
                console.error("Error fetching bus data:", error);
            }
        }

        // --- EXECUTE ---
        
        // 1. Draw the static map layers (Routes & Stops)
        fetchRoutes();

        // 2. Start the live tracking loop
        fetchBusLocations();
        setInterval(fetchBusLocations, 2000);

        // ============================================
    // NEW: DRAW THE BUS STOPS (GENERALIZED)
    // ============================================
    async function drawBusStops() {
        try {
            // 1. Get the list of generalized stops
            const response = await fetch('/api/routes');
            const routes = await response.json();

            // 2. Define a simple Blue Dot Icon
            const stopIcon = L.divIcon({
                className: 'custom-stop-icon',
                html: `<div style="
                    background-color: #3b82f6; 
                    width: 12px; 
                    height: 12px; 
                    border-radius: 50%; 
                    border: 2px solid white; 
                    box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                </div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            // 3. Loop through routes and paint the dots
            routes.forEach(route => {
                if (route.path) {
                    route.path.forEach(stop => {
                        L.marker([stop.lat, stop.lng], { icon: stopIcon })
                         .addTo(map)
                         .bindPopup(`
                            <div style="text-align: center;">
                                <strong style="color: #1e3a8a;">🚏 ${stop.name}</strong><br>
                                <span style="font-size: 10px; color: gray;">Official Stop</span>
                            </div>
                         `);
                    });
                }
            });
            console.log("Bus Stops Added to Map!");
        } catch (error) {
            console.error("Could not load bus stops:", error);
        }
    }

    // Run this once when the map loads
    drawBusStops();
    </script>
</body>
</html>