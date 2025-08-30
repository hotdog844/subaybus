<!DOCTYPE html>
<html>
<head>
    <title>Public Terminal Screen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        #map {
            height: 100vh;
            width: 100vw;
        }

        .leaflet-popup-content-wrapper {
            font-size: 1.1em;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([11.597812, 122.753049], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        let markers = [];

        function getBusIcon(status) {
            const iconUrl = status === 'On Route' ? '/images/bus-green.png'
                          : status === 'At Terminal' ? '/images/bus-yellow.png'
                          : '/images/bus-gray.png';

            return L.icon({
                iconUrl,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
        }

        async function loadBuses() {
            const res = await fetch('/buses');
            const data = await res.json();

            // Clear old markers
            markers.forEach(m => map.removeLayer(m));
            markers = [];

            data.forEach(bus => {
                if (bus.locations && bus.locations.length > 0) {
                    const loc = bus.locations[0];
                    const popupInfo = `
                        <strong>${bus.plate_number}</strong><br>
                        👨‍✈️ ${bus.driver_name || 'N/A'}<br>
                        📍 ${bus.route || 'N/A'}<br>
                        ⭐ ${bus.rating || 'N/A'}<br>
                        💰 ₱${parseFloat(bus.fare || 0).toFixed(2)}<br>
                        🟢 Status: ${bus.status || 'Unknown'}<br>
                        🕒 Last Seen: ${new Date(loc.updated_at).toLocaleString()}
                    `;

                    const marker = L.marker([loc.latitude, loc.longitude], {
                        icon: getBusIcon(bus.status)
                    }).addTo(map).bindPopup(popupInfo);

                    markers.push(marker);
                }
            });
        }

        loadBuses();
        setInterval(loadBuses, 10000); // refresh every 10s
    </script>
</body>
</html>
