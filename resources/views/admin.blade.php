<!DOCTYPE html>
<html>
<head>
    <title>Admin Map Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        #map {
            height: 90vh;
            width: 100%;
        }
    </style>
</head>
<body>
    <h2>🗺️ Real-Time Bus Location Map</h2>
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([11.597812, 122.753049], 13); // center in Roxas City

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        async function loadBuses() {
            const res = await fetch('/buses');
            const data = await res.json();

            data.forEach(bus => {
                if (bus.locations.length > 0) {
                    const loc = bus.locations[0];
                    L.marker([loc.latitude, loc.longitude])
                        .addTo(map)
                        .bindPopup(`Bus: ${bus.plate_number}`);
                }
            });
        }

        loadBuses();
        setInterval(loadBuses, 10000); // refresh every 10 sec
    </script>
</body>
</html>
