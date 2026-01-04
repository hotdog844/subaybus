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

        // 2. Custom Bus Icon
        const busIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3448/3448339.png', // A nicer bus icon
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        let busMarkers = {};

        // 3. The Function to Get Data
        async function fetchBusLocations() {
            try {
                // Call our API (We will define this route in the next step!)
                const response = await fetch('/api/bus-locations'); 
                const buses = await response.json();

                buses.forEach(bus => {
                    // Skip if no GPS data yet
                    if (!bus.lat || !bus.lng) return;

                    // Color-code status
                    let statusColor = bus.status === 'active' ? 'green' : 
                                     (bus.status === 'full' ? 'orange' : 'red');

                    // Create the Popup HTML
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

                    // Update or Create Marker
                    if (busMarkers[bus.id]) {
                        busMarkers[bus.id].setLatLng([bus.lat, bus.lng]);
                        // Only update popup content if we aren't currently hovering/reading it
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

        // 4. Run it every 2 seconds
        fetchBusLocations();
        setInterval(fetchBusLocations, 2000);

    </script>
</body>
</html>