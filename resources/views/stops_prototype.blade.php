<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nearby Bus Stops - SubayBus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            --star-color: #FFD700;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); color: var(--text-dark); }
        .header { background-color: var(--header-bg); color: white; padding: 1.2rem; display: flex; align-items: center; position: sticky; top: 0; z-index: 1000; }
        .header .back-arrow { font-size: 1.5rem; color: white; text-decoration: none; margin-right: 1rem; }
        .header h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
        #map { width: 100%; height: 40vh; }
        .stops-list-container { padding: 1rem; }
        .status-message { text-align: center; padding: 2rem; color: var(--text-light); }
        .stop-card { background: var(--card-bg); border-radius: 12px; margin-bottom: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .stop-card-header { padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .stop-name { font-weight: 600; font-size: 1.1rem; }
        .stop-card-body { padding: 0 1rem 1rem; font-size: 0.9rem; }
        .route-info { color: var(--text-light); border-top: 1px solid var(--border-color); padding-top: 1rem; }
        .favorite-star {
            font-size: 1.5rem;
            color: var(--border-color);
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
        }
        .favorite-star:hover { transform: scale(1.2); }
        .favorite-star.is-favorite { color: var(--star-color); }
        /* SVG icons don't need Font Awesome, we style them directly */
        .star-icon { width: 24px; height: 24px; }
        .star-icon path { fill: currentColor; }
    </style>
</head>
<body>

    <header class="header">
        <a href="{{ route('home') }}" class="back-arrow">&larr;</a>
        <h1>Nearby Bus Stops</h1>
    </header>

    <div id="map"></div>

    <div class="stops-list-container" id="stopsList"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const stopsListContainer = document.getElementById('stopsList');
        const map = L.map('map').setView([11.5833, 122.75], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // --- PROTOTYPE DATA (SELF-CONTAINED) ---
        const sampleStops = [
            { id: 1, name: "Robinsons Place Roxas", latitude: 11.5585, longitude: 122.7533, route: { name: "To Lawaan" } },
            { id: 2, name: "Roxas City Plaza", latitude: 11.5833, longitude: 122.7525, route: { name: "To Baybay" } },
            { id: 3, name: "Roxas City Airport", latitude: 11.5975, longitude: 122.7515, route: { name: "To Baybay" } }
        ];

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
            } else {
                favoriteStopIds.add(stopId);
                starElement.classList.add('is-favorite');
            }
            saveFavorites();
        }

        stopsListContainer.addEventListener('click', function(event) {
            const starElement = event.target.closest('.favorite-star');
            if (starElement) {
                const stopId = starElement.dataset.stopId;
                toggleFavorite(stopId, starElement);
            }
        });
        
        // --- RENDER LOGIC ---
        function renderStopsList(stops) {
            stopsListContainer.innerHTML = '';
            if (stops.length === 0) return;

            // Using SVG for the star icon to guarantee it renders
            const starSVG = `<svg class="star-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27l-5.18 3.73l1.99-6.03l-4.81-4.17l6.16-.54L12 5l2.84 5.26l6.16.54l-4.81 4.17l1.99 6.03z"></path></svg>`;

            stops.forEach(stop => {
                L.marker([stop.latitude, stop.longitude]).addTo(map).bindPopup(stop.name);
                
                const isFavorited = favoriteStopIds.has(stop.id);
                const item = document.createElement('div');
                item.className = 'stop-card';
                item.innerHTML = `
                    <div class="stop-card-header">
                        <span class="stop-name">${stop.name}</span>
                        <span class="favorite-star ${isFavorited ? 'is-favorite' : ''}" data-stop-id="${stop.id}">${starSVG}</span>
                    </div>
                    <div class="stop-card-body">
                        <div class="route-info">
                            Route: <strong>${stop.route.name}</strong>
                        </div>
                    </div>
                `;
                stopsListContainer.appendChild(item);
            });
        }

        // --- INITIAL LOAD ---
        renderStopsList(sampleStops);
    });
</script>
</body>
</html>