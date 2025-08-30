<!DOCTYPE html>
<html>
<head>
    <title>📍 Real-Time Bus Tracker Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        html, body { margin: 0; padding: 0; height: 100%; }
        #map { height: 100vh; width: 100%; }
    </style>
</head>
<body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let map = L.map('map').setView([11.5853, 122.7517], 13); // Set to Roxas City

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        let busMarkers = {};

        async function fetchBusLocations() {
            const response = await fetch('/buses');
            const buses = await response.json();

            buses.forEach(bus => {
                const { latitude, longitude, plate_number, route, status, rating, fare, updated_at } = bus.latest_location || {};
                if (!latitude || !longitude) return;

                const popup = `
                    <b>${bus.plate_number}</b><br>
                    Route: ${bus.route}<br>
                    Status: ${bus.status}<br>
                    Rating: ${bus.rating || 'N/A'} ⭐<br>
                    Fare: ₱${parseFloat(bus.fare || 0).toFixed(2)}<br>
                    Last Seen: ${new Date(updated_at).toLocaleString()}
                `;

                if (busMarkers[bus.id]) {
                    busMarkers[bus.id].setLatLng([latitude, longitude]).setPopupContent(popup);
                } else {
                    busMarkers[bus.id] = L.marker([latitude, longitude], { icon: busIcon })
    .addTo(map)
    .bindPopup(popup);

                }
            });
        }

        fetchBusLocations();
        setInterval(fetchBusLocations, 5000);

        const busIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/2203/2203168.png',
    iconSize: [35, 35],
    iconAnchor: [17, 35],
    popupAnchor: [0, -35]
});


    </script>
</body>
</html>
