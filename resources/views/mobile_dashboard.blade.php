<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage - SubayBus</title>
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
            --shadow-color: rgba(0, 0, 0, 0.08);
            --star-color: #FFD700;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); color: var(--text-dark); }
        .main-container { padding-bottom: 80px; }
        .header { background-color: var(--header-bg); color: white; padding: 1.2rem; text-align: center; position: sticky; top: 0; z-index: 1000; }
        .header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; letter-spacing: 1px; }
        .content-padding { padding: 1rem; }
        .search-bar { position: relative; margin-bottom: 1rem; }
        .search-bar input { width: 100%; padding: 0.9rem 1.2rem 0.9rem 3rem; border: 1px solid var(--border-color); border-radius: 50px; font-size: 1rem; box-shadow: 0 4px 15px var(--shadow-color); }
        .search-bar .fa-search { position: absolute; left: 1.2rem; top: 50%; transform: translateY(-50%); color: var(--text-light); }
        
        .filters-container { margin-bottom: 1rem; }
        .filter-group { display: flex; align-items: center; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.8rem; scrollbar-width: none; }
        .filter-group::-webkit-scrollbar { display: none; }
        .filter-group-title { font-size: 0.8rem; color: var(--text-light); white-space: nowrap; margin-right: 0.5rem; }
        .search-pill { background-color: #e9ecef; color: var(--text-light); padding: 0.4rem 0.8rem; border-radius: 50px; cursor: pointer; font-size: 0.8rem; }

        .view-switcher { display: flex; background-color: #e9ecef; border-radius: 50px; padding: 5px; margin-bottom: 1.5rem; }
        .view-switcher button { flex: 1; padding: 0.7rem; border: none; border-radius: 50px; background-color: transparent; font-weight: 500; cursor: pointer; transition: all 0.2s ease-in-out; }
        .view-switcher button.active { background-color: var(--card-bg); color: var(--header-bg); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        .content-view { display: none; }
        .content-view.active { display: block; }

        .bus-card-link { text-decoration: none; color: inherit; display: block; }
        .bus-card { background: var(--card-bg); border-radius: 16px; margin-bottom: 1.2rem; box-shadow: 0 6px 20px var(--shadow-color); overflow: hidden; }
        .bus-card-content { padding: 1rem; }
        .bus-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; }
        .bus-card-header h2 { font-size: 1.3rem; font-weight: 700; margin: 0; }
        .bus-card-info p { margin: 0.3rem 0; font-size: 0.9rem; color: var(--text-light); }
        .bus-card-info .route-name { font-weight: 500; color: var(--text-dark); }
        .status-pill { background-color: var(--primary-color); color: white; font-size: 0.8rem; font-weight: 500; padding: 0.3rem 0.8rem; border-radius: 50px; display: inline-block; }
        .bus-card-details { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem 1rem; margin-top: 1rem; font-size: 0.85rem; }
        .map-preview { height: 150px; width: 100%; margin-top: 1rem; border-radius: 12px; background-color: #e9ecef; }
        
        #main-map-container { height: 60vh; border-radius: 16px; overflow: hidden; box-shadow: 0 6px 20px var(--shadow-color); }
        .stop-card { background: var(--card-bg); border-radius: 12px; margin-bottom: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .stop-card-header { padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .stop-name { font-weight: 600; font-size: 1.1rem; }
        .stop-distance { font-size: 0.9rem; color: var(--header-bg); font-weight: 500; }
        .stop-card-body { padding: 0 1rem 1rem; font-size: 0.9rem; }
        .route-info { color: var(--text-light); border-top: 1px solid var(--border-color); padding-top: 1rem; }
        .favorite-star { font-size: 1.5rem; color: var(--border-color); cursor: pointer; transition: color 0.2s ease, transform 0.2s ease; }
        .favorite-star:hover { transform: scale(1.2); }
        .favorite-star.is-favorite { color: var(--star-color); }
        .star-icon { width: 24px; height: 24px; }
        .star-icon path { fill: currentColor; }
        .status-message { text-align: center; padding: 2rem; color: var(--text-light); }

        .locate-btn { position: fixed; bottom: 80px; right: 20px; z-index: 401; background-color: #ffffff; color: #222; width: 44px; height: 44px; border-radius: 50%; border: 1px solid #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; }
        .user-location-marker { background-color: #007bff; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 10px rgba(0,0,0,0.3); }

        .bottom-nav { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--card-bg); border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; padding: 0.5rem 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 9999; }
        .nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; color: var(--text-light); font-size: 0.75rem; text-decoration: none; }
        .nav-item i { font-size: 1.4rem; margin-bottom: 5px; }
        .nav-item.active { color: var(--header-bg); font-weight: 500; }
        .filter-group-title {
    font-size: 0.8rem;
    color: var(--text-light);
    white-space: nowrap;
    margin-right: 0.5rem;
}
.search-pill {
    background-color: #e9ecef;
    color: var(--text-light);
    padding: 0.4rem 0.8rem;
    border-radius: 50px;
    white-space: nowrap;
    cursor: pointer;
    font-size: 0.8rem;
    transition: background-color 0.2s;
}
.search-pill:hover {
    background-color: #d8dde2;
}
    </style>
</head>
<body>

    <div class="main-container">
        <header class="header"><h1>SubayBus 🚌</h1></header>
        <div class="content-padding">
            <section class="search-bar">
                <i class="fas fa-search"></i>
                <input type="search" id="searchInput" placeholder="Search for a landmark (e.g. Robinsons)...">
            </section>
            
            <div class="filters-container">
                <section class="filter-group" id="recentSearchesContainer"></section>
                <section class="filter-group" id="routeFiltersContainer">
        {{-- Example route filter pill: Add more as needed --}}
        <span class="search-pill">All Routes</span>
        <span class="search-pill">To Lawaan</span>
        <span class="search-pill">To Baybay</span>
        {{-- Add additional route filter pills here following the pattern above --}}
    </section>
            </div>

            <section class="view-switcher">
                <button id="show-list-btn" class="active">Bus List</button>
                <button id="show-map-btn">Map View</button>
                <button id="show-stops-btn">Nearby Stops</button>
            </section>
            
            <div style="position: relative;">
                <section id="list-container" class="content-view active">
                    {{-- Your three sample bus cards are here --}}
                </section>
                
                <section id="map-container" class="content-view">
                    <div id="main-map-container"></div>
                </section>

                <section id="stops-container" class="content-view">
                    <p class="status-message">Click the <i class="fa-solid fa-location-crosshairs"></i> button to find stops near you.</p>
                </section>
                
                <button class="locate-btn" id="locateBtn" title="Find My Location"><i class="fa-solid fa-location-crosshairs"></i></button>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
    <a href="{{ route('home') }}" class="nav-item">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="#" onclick="goToLastViewedBus(); return false;" class="nav-item">
        <i class="fas fa-bus"></i>
        <span>Bus</span>
    </a>
    <a href="{{ route('route.planner') }}" class="nav-item">
        <i class="fas fa-map-signs"></i>
        <span>Planner</span>
    </a>
    <a href="{{ route('profile.show') }}" class="nav-item">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="{{ route('settings') }}" class="nav-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
    </a>
</nav>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // --- UTILITY FUNCTIONS ---
        function saveLastViewedBus(busId) { localStorage.setItem('lastViewedBusId', busId); }
        function goToLastViewedBus() {
            const lastBusId = localStorage.getItem('lastViewedBusId');
            if (lastBusId) { window.location.href = `/bus/${lastBusId}`; } 
            else { alert('Please tap on a bus from the list first to see its details.'); }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // --- ELEMENT REFERENCES ---
            const searchInput = document.getElementById('searchInput');
            const locateBtn = document.getElementById('locateBtn');
            const showListBtn = document.getElementById('show-list-btn');
            const showMapBtn = document.getElementById('show-map-btn');
            const showStopsBtn = document.getElementById('show-stops-btn');
            const listContainer = document.getElementById('list-container');
            const mapContainer = document.getElementById('map-container');
            const stopsContainer = document.getElementById('stops-container');
            const recentSearchesContainer = document.getElementById('recentSearchesContainer');
            let debounceTimer;

            // --- Functions for Recent Searches ---
function loadRecentSearches() {
    recentSearchesContainer.innerHTML = '';
    const searches = JSON.parse(localStorage.getItem('recentSearches')) || [];
    if (searches.length > 0) {
        const title = document.createElement('span');
        title.className = 'filter-group-title';
        title.textContent = 'Recent:';
        recentSearchesContainer.appendChild(title);
    }
    searches.forEach(term => {
        const pill = document.createElement('div');
        pill.className = 'search-pill';
        pill.textContent = term;
        recentSearchesContainer.appendChild(pill);
    });
}

recentSearchesContainer.addEventListener('click', (event) => {
    if (event.target.classList.contains('search-pill')) {
        const searchTerm = event.target.textContent;
        searchInput.value = searchTerm;
        filterBusCards(); // Re-run the filter with the new term
    }
});


function saveRecentSearch(term) {
    if (!term || term.length < 3) return; // Don't save empty or short searches
    let searches = JSON.parse(localStorage.getItem('recentSearches')) || [];
    searches = searches.filter(s => s.toLowerCase() !== term.toLowerCase());
    searches.unshift(term);
    const limitedSearches = searches.slice(0, 5);
    localStorage.setItem('recentSearches', JSON.stringify(limitedSearches));
    loadRecentSearches();
}

            // --- PROTOTYPE DATA ---
            const sampleBuses = [
                { id: 1, plate: "RXS-001", driver: "Mario Espinosa", routeName: "Roxas City Pueblo Terminal to Lawaan", position: [11.585, 122.752], status: "On Route", fare: 13.00, description: "Robinsons Gaisano City Mall People's Park" },
                { id: 2, plate: "RXS-002", driver: "Jose Rizal", routeName: "Roxas City Pueblo Terminal to Baybay", position: [11.588, 122.758], status: "On Route", fare: 15.00, description: "Airport Peoples Park" },
                { id: 3, plate: "RXS-003", driver: "Andres Bonifacio", routeName: "Roxas City Pueblo Terminal to Lawaan", position: [11.5833, 122.75], status: "At Terminal", fare: 13.00, description: "Robinsons Gaisano City Mall" }
            ];
            const sampleStops = [
                { id: 1, name: "Robinsons Place Roxas", latitude: 11.5585, longitude: 122.7533, route: { name: "To Lawaan" } },
                { id: 2, name: "Roxas City Plaza", latitude: 11.5833, longitude: 122.7525, route: { name: "To Baybay" } },
                { id: 3, name: "Roxas City Airport", latitude: 11.5975, longitude: 122.7515, route: { name: "To Baybay" } }
            ];
            let favoriteStopIds = new Set(JSON.parse(localStorage.getItem('favoriteStops')) || []);

            // --- MAP INITIALIZATION ---
            const busIcon = L.icon({ iconUrl: '{{ asset("images/bus-green.png") }}', iconSize: [35, 35], iconAnchor: [17, 35] });
            const mainMap = L.map('main-map-container').setView([11.5833, 122.75], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mainMap);
            
            // --- VIEW SWITCHING ---
            function switchView(activeView) {
                document.querySelectorAll('.content-view').forEach(v => v.classList.remove('active'));
                document.querySelectorAll('.view-switcher button').forEach(b => b.classList.remove('active'));
                document.getElementById(`${activeView}-container`).classList.add('active');
                document.getElementById(`show-${activeView}-btn`).classList.add('active');
                if (activeView === 'map') {
                    setTimeout(() => mainMap.invalidateSize(), 10);
                }
                if (activeView === 'stops') {
                    findNearbyStops();
                }
            }
            showListBtn.addEventListener('click', () => switchView('list'));
            showMapBtn.addEventListener('click', () => switchView('map'));
            showStopsBtn.addEventListener('click', () => switchView('stops'));

            // --- RENDER BUS LIST & MINIMAPS (PROTOTYPE) ---
            function renderBusList(buses) {
                listContainer.innerHTML = '';
                buses.forEach(bus => {
                    const discountedFare = (bus.fare * 0.8).toFixed(2);
                    const cardLink = document.createElement('a');
                    cardLink.href = `/bus/${bus.id}`;
                    cardLink.className = 'bus-card-link';
                    cardLink.onclick = () => saveLastViewedBus(bus.id);
                    cardLink.innerHTML = `
                        <div class="bus-card" data-description="${bus.description}">
                            <div class="bus-card-content">
                                <div class="bus-card-header"><h2>${bus.plate}</h2><span class="status-pill" style="${bus.status !== 'On Route' ? 'background-color: var(--text-light);' : ''}">${bus.status}</span></div>
                                <div class="bus-card-info"><p><strong>Driver:</strong> ${bus.driver}</p><p><strong>Route:</strong> <span class="route-name">${bus.routeName}</span></p></div>
                                <div class="bus-card-details"><p><strong>Distance:</strong> -- km</p><p><strong>Fare:</strong> ₱${bus.fare.toFixed(2)}</p><p><strong>Last seen:</strong> --</p><p><strong>Discounted:</strong> ₱${discountedFare}</p><p><strong>ETA:</strong> -- min</p><p><strong>Next Stop:</strong> --</p></div>
                                <div class="map-preview" id="map-preview-${bus.id}"></div>
                            </div>
                        </div>`;
                    listContainer.appendChild(cardLink);
                    
                    const mapEl = document.getElementById(`map-preview-${bus.id}`);
                    const miniMap = L.map(mapEl, { zoomControl: false, scrollWheelZoom: false, dragging: false, doubleClickZoom: false, boxZoom: false, keyboard: false, tap: false, attributionControl: false }).setView(bus.position, 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMap);
                    L.marker(bus.position, { icon: busIcon, interactive: false }).addTo(miniMap);
                });
            }
            
            // --- GEOLOCATION & NEARBY STOPS ---
            function findNearbyStops() {
                if (!navigator.geolocation) return alert('Geolocation is not supported.');
                stopsContainer.innerHTML = '<p class="status-message">Getting your location...</p>';
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLatLng = L.latLng(position.coords.latitude, position.coords.longitude);
                        L.marker(userLatLng).addTo(mainMap).bindPopup('You are here');
                        mainMap.setView(userLatLng, 15);
                        
                        sampleStops.forEach(stop => {
                            stop.distance = userLatLng.distanceTo([stop.latitude, stop.longitude]);
                        });
                        sampleStops.sort((a, b) => a.distance - b.distance);
                        renderStopsList(sampleStops);
                    },
                    () => {
                        stopsContainer.innerHTML = '<p class="status-message">Could not get your location. Please enable location services.</p>';
                    }
                );
            }
            function renderStopsList(stops) {
                stopsContainer.innerHTML = '';
                const starSVG = `<svg class="star-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27l-5.18 3.73l1.99-6.03l-4.81-4.17l6.16-.54L12 5l2.84 5.26l6.16.54l-4.81 4.17l1.99 6.03z"></path></svg>`;
                stops.forEach(stop => {
                    const isFavorited = favoriteStopIds.has(stop.id);
                    const distanceKm = stop.distance ? `${(stop.distance / 1000).toFixed(2)} km away` : '';
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
                    stopsContainer.appendChild(item);
                });
            }
            locateBtn.addEventListener('click', findNearbyStops);

            // --- FAVORITES LOGIC ---
            function saveFavorites() { localStorage.setItem('favoriteStops', JSON.stringify([...favoriteStopIds])); }
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
            stopsContainer.addEventListener('click', function(event) {
                const starElement = event.target.closest('.favorite-star');
                if (starElement) {
                    const stopId = starElement.dataset.stopId;
                    toggleFavorite(stopId, starElement);
                }
            });

            // --- PROTOTYPE SEARCH & RECENT SEARCHES ---
            function filterBusCards() {
                const searchQuery = searchInput.value.toLowerCase();
                const filteredBuses = sampleBuses.filter(bus => 
                    bus.plate.toLowerCase().includes(searchQuery) ||
                    bus.driver.toLowerCase().includes(searchQuery) ||
                    bus.routeName.toLowerCase().includes(searchQuery) ||
                    bus.description.toLowerCase().includes(searchQuery)
                );
                renderBusList(filteredBuses);
            }
            searchInput.addEventListener('input', (event) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        filterBusCards();
        // Only save if the search term is at least 3 characters
        if (searchInput.value.length >= 3) {
            saveRecentSearch(searchInput.value);
        }
    }, 500);
});

// Also save on Enter key for more accurate searches
searchInput.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && searchInput.value.length >= 3) {
        saveRecentSearch(searchInput.value);
    }
});
            
            // ... Other event listeners for recent searches ...
            
            // --- INITIAL LOAD ---
            renderBusList(sampleBuses);
            loadRecentSearches();
        });
    </script>
</body>
</html>