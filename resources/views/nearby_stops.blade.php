<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nearby Bus Stops - SubayBus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        :root {
            --primary-color: #1EEA92;
            --header-bg: #0A5C36;
            --text-dark: #222;
            --text-light: #6c757d;
            --page-bg: #f4f7fa;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
            --star-color: #FFD700; /* Gold color for stars */
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); color: var(--text-dark); }
        .header { background-color: var(--header-bg); color: white; padding: 1.2rem; display: flex; align-items: center; position: sticky; top: 0; z-index: 1000; }
        .header .back-arrow { font-size: 1.5rem; color: white; text-decoration: none; margin-right: 1rem; }
        .header h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
        
        #map {
            width: 100%;
            height: 40vh; /* 40% of the viewport height */
        }

        .stops-list-container {
            padding: 1rem;
            background-color: var(--page-bg);
        }
        .status-message { text-align: center; padding: 2rem; color: var(--text-light); }
        
        .stop-card {
            background: var(--card-bg);
            border-radius: 12px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .stop-card-header {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stop-name { font-weight: 600; font-size: 1.1rem; }
        .stop-distance { font-size: 0.9rem; color: var(--header-bg); font-weight: 500; }
        
        .stop-card-body {
            padding: 0 1rem 1rem;
            font-size: 0.9rem;
        }
        .route-info {
            color: var(--text-light);
            border-top: 1px solid var(--border-color);
            padding-top: 1rem;
        }
        .buses-list-title {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            margin-top: 1rem;
        }
        .bus-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .bus-pill {
            background-color: var(--page-bg);
            border: 1px solid var(--border-color);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .arrival-info {
            margin-top: 1rem;
            padding-top: 0.8rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--primary-color);
        }

        .favorite-star {
            font-size: 1.5rem;
            color: var(--border-color);
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
        }
        .favorite-star:hover {
            transform: scale(1.2);
        }
        .favorite-star.is-favorite {
            color: var(--star-color);
        }
    </style>
</head>
<body>

    <header class="header">
        <a href="{{ route('home') }}" class="back-arrow">&larr;</a>
        <h1>Nearby Bus Stops</h1>
    </header>

    <div id="map"></div>

    <div class="stops-list-container" id="stopsList">
        <p class="status-message">Getting your location to find nearby stops...</p>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const stopsListContainer = document.getElementById('stopsList');
        const allStops = @json($stops);
        const map = L.map('map').setView([11.5833, 122.75], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // --- FAVORITES LOGIC ---
        let favoriteStopIds = new Set(JSON.parse(localStorage.getItem('favoriteStops')) || []);

        function saveFavorites() {
            localStorage.setItem('favoriteStops', JSON.stringify([...favoriteStopIds]));
        }

        function toggleFavorite(stopId, starElement) {
            stopId = parseInt(stopId);
            if (favoriteStopIds.has(stopId)) {
                favoriteStopIds.delete(stopId);
                starElement.classList.remove('is-favorite');
                starElement.classList.replace('fas', 'far');
            } else {
                favoriteStopIds.add(stopId);
                starElement.classList.add('is-favorite');
                starElement.classList.replace('far', 'fas');
            }
            saveFavorites();
        }

        // Use event delegation to handle clicks on star icons
        stopsListContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('favorite-star')) {
                const stopId = event.target.dataset.stopId;
                toggleFavorite(stopId, event.target);
            }
        });

        // --- GEOLOCATION AND RENDERING LOGIC ---
        if (!navigator.geolocation) {
            stopsListContainer.innerHTML = '<p class="status-message">Geolocation is not supported.</p>';
            renderStopsList(allStops); // Still show all stops if geo fails
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLocation = L.latLng(position.coords.latitude, position.coords.longitude);
                L.circleMarker(userLocation, { radius: 8, color: '#007bff', fillColor: '#fff', fillOpacity: 1 }).addTo(map).bindPopup('You are here').openPopup();
                map.setView(userLocation, 15);

                allStops.forEach(stop => {
                    const stopLocation = L.latLng(stop.latitude, stop.longitude);
                    stop.distance = userLocation.distanceTo(stopLocation);
                    L.marker([stop.latitude, stop.longitude]).addTo(map).bindPopup(stop.name);
                });
                
                allStops.sort((a, b) => a.distance - b.distance);
                renderStopsList(allStops);
            },
            () => {
                stopsListContainer.innerHTML = '<p class="status-message">Could not get your location. Showing all stops.</p>';
                renderStopsList(allStops); // Still show all stops if geo fails
            }
        );

        function renderStopsList(stops) {
            stopsListContainer.innerHTML = '';
            if (stops.length === 0) {
                stopsListContainer.innerHTML = '<p class="status-message">No bus stops have been added to the system yet.</p>';
                return;
            }

            stops.forEach(stop => {
                const distanceText = stop.distance ? `${(stop.distance / 1000).toFixed(2)} km away` : '';
                const isFavorited = favoriteStopIds.has(stop.id);
                const starClass = isFavorited ? 'fas is-favorite' : 'far';
                let busPillsHtml = '<p style="font-size:0.8rem; color: #999;">No active buses on this route.</p>';
                
                if (stop.route && stop.route.buses && stop.route.buses.length > 0) {
                    busPillsHtml = stop.route.buses.map(bus => `<span class="bus-pill">${bus.plate_number}</span>`).join('');
                }

                const item = document.createElement('div');
                item.className = 'stop-card';
                item.innerHTML = `
                    <div class="stop-card-header">
                        <span class="stop-name">${stop.name}</span>
                        <i class="${starClass} fa-star favorite-star" data-stop-id="${stop.id}"></i>
                    </div>
                    <div class="stop-card-body">
                        <div class="route-info">
                            Route: <strong>${stop.route ? stop.route.name : 'N/A'}</strong>
                        </div>
                        <p class="buses-list-title">Buses on this route:</p>
                        <div class="bus-pills">
                            ${busPillsHtml}
                        </div>
                        <div class="arrival-info">
                            Next Arrival: <strong>~ 5 min</strong> (Placeholder)
                        </div>
                    </div>
                `;
                stopsListContainer.appendChild(item);
            });
        }
    });
</script>
</body>
</html>