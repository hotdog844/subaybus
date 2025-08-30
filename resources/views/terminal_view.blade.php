<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="300"> <title>SubayBus - Live Terminal Display</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        :root {
            --header-bg: #0A5C36;
            --sidebar-bg: #ffffff;
            --map-bg: #e9ecef;
            --text-light: #ffffff;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --primary-accent: #1EEA92;
            --border-color: #dee2e6;
        }

        * { box-sizing: border-box; }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
            font-family: 'Roboto', sans-serif;
            background-color: var(--map-bg);
        }

        .dashboard-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Header Styling */
        .header {
            background-color: var(--header-bg);
            color: var(--text-light);
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .header-logo h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        .header-clock {
            font-size: 1.8rem;
            font-weight: 500;
            min-width: 250px;
            text-align: right;
        }

        /* Main Content Styling */
        .main-content {
            display: flex;
            flex-grow: 1;
            overflow: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 380px;
            background-color: var(--sidebar-bg);
            padding: 1.5rem;
            overflow-y: auto;
            flex-shrink: 0;
            border-right: 1px solid var(--border-color);
        }
        .sidebar h2 {
            margin-top: 0;
            font-size: 1.5rem;
            color: var(--text-dark);
            border-bottom: 2px solid var(--primary-accent);
            padding-bottom: 0.8rem;
            margin-bottom: 1.5rem;
        }
        #bus-list {
            list-style: none;
            padding: 0;
        }
        .bus-list-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .bus-list-item:last-child {
            border-bottom: none;
        }
        .bus-plate {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
        }
        .bus-route {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0.2rem 0;
        }
        .bus-status-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.8rem;
            font-size: 0.9rem;
        }
        .bus-status {
            font-weight: 500;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            color: white;
        }
        .bus-status.on-route { background-color: #27ae60; }
        .bus-status.at-terminal { background-color: #f39c12; }
        .bus-status.offline { background-color: #7f8c8d; }

        /* Map Styling */
        .map-container {
            flex-grow: 1;
        }
        #map {
            height: 100%;
            width: 100%;
        }
        .bus-label {
            background-color: rgba(0, 0, 0, 0.7);
            border: 1px solid white;
            border-radius: 4px;
            color: #ffffff;
            box-shadow: none;
            font-weight: bold;
            font-size: 14px;
            padding: 3px 8px;
        }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        <header class="header">
            <div class="header-logo">
                <h1>SubayBus 🚌</h1>
            </div>
            <div class="header-clock" id="clock">
                Loading time...
            </div>
        </header>

        <div class="main-content">
            <aside class="sidebar">
                <h2>Active Buses</h2>
                <ul id="bus-list">
                    </ul>
            </aside>

            <main class="map-container">
                <div id="map"></div>
            </main>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Live Clock ---
            const clockElement = document.getElementById('clock');
            function updateClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
                clockElement.textContent = timeString;
            }
            updateClock();
            setInterval(updateClock, 1000);

            // --- Map Initialization ---
            const map = L.map('map').setView([11.5833, 122.75], 14);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO',
                maxZoom: 20
            }).addTo(map);
            
            // --- Real-Time Update Logic ---
            let busMarkers = new Map();
            const busIcon = L.icon({
                iconUrl: '/images/bus-green.png', // Local icon path
                iconSize: [35, 35],
                iconAnchor: [17, 35]
            });
            const busListElement = document.getElementById('bus-list');

            async function updateBusData() {
                try {
                    const response = await fetch('/api/buses/search');
                    if (!response.ok) return;
                    const buses = await response.json();
                    
                    const updatedBusIds = new Set();
                    busListElement.innerHTML = ''; // Clear the sidebar list

                    if (buses.length === 0) {
                        busListElement.innerHTML = '<li class="bus-list-item">No active buses found.</li>';
                    }

                    buses.forEach(bus => {
                        const busId = bus.id;
                        const position = [bus.latitude, bus.longitude];
                        updatedBusIds.add(busId);

                        // Update Marker on Map
                        if (busMarkers.has(busId)) {
                            busMarkers.get(busId).setLatLng(position);
                        } else {
                            const newMarker = L.marker(position, { icon: busIcon }).addTo(map);
                            newMarker.bindTooltip(bus.plate_number, {
                                permanent: true,
                                direction: 'top',
                                className: 'bus-label',
                                offset: [0, -35]
                            });
                            busMarkers.set(busId, newMarker);
                        }
                        
                        // Update Bus List in Sidebar
                        const listItem = document.createElement('li');
                        listItem.className = 'bus-list-item';
                        const statusClass = bus.status.replace(' ', '-'); // e.g., 'on route' -> 'on-route'
                        listItem.innerHTML = `
                            <div>
                                <span class="bus-plate">${bus.plate_number}</span>
                                <p class="bus-route">${bus.route ? bus.route.name : 'N/A'}</p>
                            </div>
                            <div class="bus-status-details">
                                <span class="bus-status ${statusClass}">${bus.status.replace(/^\w/, c => c.toUpperCase())}</span>
                                <span>ETA: 5 mins</span> </div>
                        `;
                        busListElement.appendChild(listItem);
                    });

                    // Remove old markers
                    busMarkers.forEach((marker, busId) => {
                        if (!updatedBusIds.has(busId)) {
                            map.removeLayer(marker);
                            busMarkers.delete(busId);
                        }
                    });
                } catch (error) {
                    console.error('Error updating bus data:', error);
                }
            }

            // Initial Load & Polling
            updateBusData();
            setInterval(updateBusData, 10000);
        });
    </script>
</body>
</html>