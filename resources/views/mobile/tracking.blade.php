<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Live Trip</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        #map { z-index: 0; }
        
        /* 1. PULSING RADAR (Bus Icon) */
        .radar-ring {
            position: absolute;
            border: 2px solid rgba(16, 185, 129, 0.6);
            background-color: rgba(16, 185, 129, 0.1);
            width: 80px; height: 80px;
            border-radius: 50%;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation: pulse-ring 2s infinite;
            pointer-events: none;
        }
        @keyframes pulse-ring {
            0% { transform: translate(-50%, -50%) scale(0.5); opacity: 1; }
            100% { transform: translate(-50%, -50%) scale(2.0); opacity: 0; }
        }
        .custom-bus-marker { background: none; border: none; }

        /* 2. STOP STYLES */
        .stop-passed { filter: grayscale(100%); opacity: 0.5; }
        .stop-next { transform: scale(1.2); transition: transform 0.3s; }

        /* Hide Scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="h-screen w-screen overflow-hidden bg-gray-100 relative">

    <div id="map" class="absolute inset-0 h-full w-full"></div>

    <div class="absolute top-0 left-0 right-0 p-6 flex justify-between items-start z-40 pointer-events-none">
        <a href="/mobile/dashboard" class="pointer-events-auto bg-white/90 backdrop-blur w-10 h-10 rounded-full shadow-lg flex items-center justify-center text-gray-700 active:scale-95 transition">
            <i class="fas fa-chevron-left"></i>
        </a>
        <div class="bg-black/80 backdrop-blur px-3 py-1.5 rounded-full flex items-center gap-2 shadow-lg">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_#22c55e]"></div>
            <span class="text-[10px] font-bold text-white uppercase tracking-wider">Live</span>
        </div>
    </div>

    <div id="feedback-modal" class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm hidden flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="text-center mb-4">
                <div class="w-12 h-12 bg-yellow-100 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-2 text-xl">
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-800">Rate Your Ride</h3>
                <p class="text-xs text-gray-400">Help us improve the transport system.</p>
            </div>
            
            <div class="flex justify-center gap-2 mb-4 text-2xl text-gray-300">
                <i class="fas fa-star cursor-pointer hover:text-yellow-400 transition" onclick="rate(1)"></i>
                <i class="fas fa-star cursor-pointer hover:text-yellow-400 transition" onclick="rate(2)"></i>
                <i class="fas fa-star cursor-pointer hover:text-yellow-400 transition" onclick="rate(3)"></i>
                <i class="fas fa-star cursor-pointer hover:text-yellow-400 transition" onclick="rate(4)"></i>
                <i class="fas fa-star cursor-pointer hover:text-yellow-400 transition" onclick="rate(5)"></i>
            </div>

            <textarea id="feedback-text" class="w-full bg-gray-50 border border-gray-100 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 mb-4" rows="3" placeholder="Any comments? (Cleanliness, Speed, etc.)"></textarea>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="toggleFeedback()" class="py-3 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-100">Cancel</button>
                <button onclick="submitFeedback()" class="py-3 rounded-xl font-bold text-sm bg-gray-900 text-white shadow-lg active:scale-95">Submit</button>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0 z-50 bg-white rounded-t-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-5 pb-8">
        
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-4"></div>

        <div class="flex items-center justify-between mb-5">
            <div>
                <span id="route-badge" class="inline-block px-2 py-1 rounded-lg text-[10px] font-bold uppercase bg-gray-100 text-gray-500 mb-1">Locating...</span>
                <h1 id="bus-name" class="text-2xl font-black text-gray-900 leading-none">...</h1>
            </div>
            <div class="flex flex-col items-center">
                <div class="bg-gray-900 text-white w-14 h-14 rounded-2xl flex flex-col items-center justify-center shadow-xl">
                    <span class="text-xl font-bold leading-none" id="eta-mins">--</span>
                    <span class="text-[9px] uppercase font-bold text-gray-400">Min</span>
                </div>
            </div>
        </div>

        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 flex justify-between">
            <span>Route Progress</span>
            <button onclick="toggleFeedback()" class="text-green-600 hover:underline cursor-pointer">Rate Trip</button>
        </h3>
        
        <div id="stops-list" class="flex gap-3 overflow-x-auto pb-2 no-scrollbar scroll-smooth">
            <div class="flex-shrink-0 text-xs text-gray-400 italic">Loading route...</div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // --- CONFIGURATION ---
        const TARGET_BUS_ID = {{ $busId }}; 
        const UPDATE_INTERVAL = 1500; // 1.5 seconds

        // --- MAP SETUP ---
        const map = L.map('map', { zoomControl: false, attributionControl: false }).setView([11.5853, 122.7511], 16);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(map);

        // Layers
        let routeLayer = L.layerGroup().addTo(map); // For the Blue Line
        let busMarker = null;
        let routePolyline = null;
        let currentStops = [];

        // --- 1. SMOOTH ANIMATION LOGIC ---
        // Leaflet markers jump by default. We will use a simple slide function.
        function slideMarker(marker, newLat, newLng, duration) {
            const startLat = marker.getLatLng().lat;
            const startLng = marker.getLatLng().lng;
            const startTime = performance.now();

            function animate(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1); // 0 to 1

                // Interpolate
                const currentLat = startLat + (newLat - startLat) * progress;
                const currentLng = startLng + (newLng - startLng) * progress;

                marker.setLatLng([currentLat, currentLng]);

                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            }
            requestAnimationFrame(animate);
        }

        // --- 2. MAIN TRACKING LOOP ---
        async function trackBus() {
            try {
                const res = await fetch('/api/bus-locations');
                const buses = await res.json();
                const bus = buses.find(b => b.id == TARGET_BUS_ID);

                if (!bus) return;

                // Update Texts
                document.getElementById('bus-name').innerText = bus.bus_number;
                updateBadge(bus.bus_number);

                const lat = parseFloat(bus.lat);
                const lng = parseFloat(bus.lng);

                // Update Marker (Smoothly)
                if (busMarker) {
                    slideMarker(busMarker, lat, lng, UPDATE_INTERVAL);
                } else {
                    // Create Marker First Time
                    createBusMarker(lat, lng);
                    loadRouteData(bus); // Load stops & Draw line
                }

                // Camera Follow (Offset to show above panel)
                map.panTo([lat - 0.0025, lng], {animate: true, duration: 1.5});

                // Update Stops Status (Passed/Next) & ETA
                if(currentStops.length > 0) {
                    updateStopsUI(lat, lng);
                }

            } catch (err) { console.error(err); }
        }

        function createBusMarker(lat, lng) {
            const icon = L.divIcon({
                className: 'custom-bus-marker',
                html: `
                    <div class="relative w-20 h-20 flex items-center justify-center">
                        <div class="radar-ring"></div>
                        <div class="relative z-10 bg-gray-900 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-2xl border-2 border-white">
                            <i class="fas fa-bus"></i>
                        </div>
                    </div>
                `,
                iconSize: [80, 80],
                iconAnchor: [40, 40]
            });
            busMarker = L.marker([lat, lng], {icon: icon, zIndexOffset: 1000}).addTo(map);
        }

        // --- 3. ROUTE LINE & STOPS ---
        function loadRouteData(bus) {
    console.log("Loading Route Data for:", bus); // Debugging line

    // 1. Safety Check
    if (!bus || !bus.route) {
        console.warn("Bus has no route assigned!");
        return;
    }

    routeLayer.clearLayers();

    // 2. CHECK FOR COORDINATES (The new feature)
    if (bus.route.origin_lat && bus.route.dest_lat) {
        console.log("Found coordinates:", bus.route.origin_lat, bus.route.dest_lat);

        const origin = [parseFloat(bus.route.origin_lat), parseFloat(bus.route.origin_lng)];
        const dest = [parseFloat(bus.route.dest_lat), parseFloat(bus.route.dest_lng)];

        // A. Origin Pin (Green)
        L.marker(origin, {
            zIndexOffset: 1000,
            icon: L.divIcon({
                className: '',
                html: '<div class="w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-lg"></div>',
                iconSize: [16, 16]
            })
        }).addTo(routeLayer).bindPopup(`<b>Start:</b> ${bus.route.start_location}`);

        // B. Destination Pin (Red)
        L.marker(dest, {
            zIndexOffset: 1000,
            icon: L.divIcon({
                className: '',
                html: '<div class="w-4 h-4 bg-red-600 border-2 border-white rounded-full shadow-lg"></div>',
                iconSize: [16, 16]
            })
        }).addTo(routeLayer).bindPopup(`<b>End:</b> ${bus.route.end_location}`);

        // C. Draw the Line via Stops
        fetch(`/api/stops?route_id=${bus.route.id}`)
            .then(r => r.json())
            .then(stops => {
                let pathPoints = [origin]; // Start with Origin

                // Add middle stops
                stops.forEach(stop => {
                    pathPoints.push([stop.lat, stop.lng]);
                    L.circleMarker([stop.lat, stop.lng], {
                        radius: 4, color: 'white', fillColor: '#3b82f6', fillOpacity: 1, weight: 2
                    }).addTo(routeLayer);
                });

                pathPoints.push(dest); // End with Dest

                // Draw Blue Line
                if (pathPoints.length > 1) {
                    const polyline = L.polyline(pathPoints, {
                        color: '#3b82f6', 
                        weight: 5, 
                        opacity: 0.7, 
                        lineCap: 'round'
                    }).addTo(routeLayer);
                    
                    map.fitBounds(polyline.getBounds(), {padding: [50, 50]});
                }
            });

    } else {
        console.warn("Route exists but has no GPS coordinates set in Admin.");
    }
}

        // --- 4. UPDATE STOPS UI & CALCULATE ETA ---
        function updateStopsUI(busLat, busLng) {
            let closestDist = 999999;
            let closestIndex = -1;

            // Find closest stop to determine where we are
            currentStops.forEach((stop, index) => {
                const dist = getDistance(busLat, busLng, stop.lat, stop.lng);
                if(dist < closestDist) {
                    closestDist = dist;
                    closestIndex = index;
                }
            });

            // Update List UI
            const listContainer = document.getElementById('stops-list');
            const items = listContainer.children;

            if(closestIndex !== -1 && items.length === currentStops.length) {
                // Calculate ETA to the NEXT stop (Index + 1)
                // Assuming average city speed 30km/h = 500 meters/min
                const nextStop = currentStops[closestIndex]; // Or closestIndex + 1
                if(nextStop) {
                    const distToNext = getDistance(busLat, busLng, nextStop.lat, nextStop.lng);
                    const mins = Math.ceil(distToNext / 500); // Rough estimate
                    document.getElementById('eta-mins').innerText = mins;
                }

                // Visual Updates
                for (let i = 0; i < currentStops.length; i++) {
                    const item = items[i];
                    if (i < closestIndex) {
                        // PASSED STOPS
                        item.classList.add('opacity-50', 'grayscale');
                        item.querySelector('.stop-icon').innerHTML = '<i class="fas fa-check text-white"></i>';
                        item.querySelector('.stop-icon').className = "stop-icon w-5 h-5 rounded-full bg-green-500 flex items-center justify-center text-[9px] mb-1";
                    } else if (i === closestIndex) {
                        // CURRENT/NEXT STOP
                        item.classList.remove('opacity-50', 'grayscale');
                        item.querySelector('.stop-icon').className = "stop-icon w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold mb-1 shadow-lg shadow-blue-300 scale-110 transition";
                        item.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    } else {
                        // FUTURE
                        item.classList.remove('opacity-50', 'grayscale');
                    }
                }
            }
        }

        function renderStopsList() {
            const listContainer = document.getElementById('stops-list');
            listContainer.innerHTML = '';
            
            currentStops.forEach((stop, index) => {
                // Map Dot
                L.circleMarker([stop.lat, stop.lng], {
                    radius: 4, color: 'white', fillColor: '#3b82f6', fillOpacity: 1, weight: 2
                }).addTo(routeLayer);

                // List Item
                const item = document.createElement('div');
                item.className = "flex-shrink-0 w-24 bg-gray-50 p-2 rounded-xl border border-gray-100 flex flex-col items-center text-center transition-all duration-500";
                item.innerHTML = `
                    <div class="stop-icon w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[9px] font-bold mb-1">${index+1}</div>
                    <h4 class="font-bold text-gray-800 text-[10px] truncate w-full">${stop.name}</h4>
                `;
                listContainer.appendChild(item);
            });
        }

        // --- UTILS ---
        function getDistance(lat1, lon1, lat2, lon2) {
            // Haversine Formula (Returns Meters)
            const R = 6371e3; // Earth radius
            const φ1 = lat1 * Math.PI/180;
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lon2-lon1) * Math.PI/180;
            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        }

        function updateBadge(name) {
            let badge = document.getElementById('route-badge');
            if(name.includes('Red')) { badge.className="inline-block px-2 py-1 rounded-lg text-[10px] font-bold uppercase bg-red-100 text-red-600"; badge.innerText="Red Route"; }
            else if(name.includes('Blue')) { badge.className="inline-block px-2 py-1 rounded-lg text-[10px] font-bold uppercase bg-blue-100 text-blue-600"; badge.innerText="Blue Route"; }
            else { badge.className="inline-block px-2 py-1 rounded-lg text-[10px] font-bold uppercase bg-green-100 text-green-600"; badge.innerText="Green Route"; }
        }

        // --- FEEDBACK LOGIC ---
        let selectedRating = 0; // 1. Variable to store the star rating

        function toggleFeedback() {
            const modal = document.getElementById('feedback-modal');
            modal.classList.toggle('hidden');
        }

        // 2. Updated Rate Function to save the rating
        function rate(star) {
            selectedRating = star; // Store the clicked star value
            
            // Visual Update for Stars
            const stars = document.querySelectorAll('#feedback-modal .fa-star'); // Target only modal stars
            stars.forEach((s, idx) => {
                // Determine if this star should be yellow or gray
                if (idx < star) {
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        }

        // 3. Updated Submit Function with Real Server Connection
        function submitFeedback() {
            // Basic validation
            if (selectedRating === 0) {
                alert("Please select a star rating.");
                return;
            }

            // Get CSRF Token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const commentText = document.getElementById('feedback-text').value;

            // Send to Laravel
            fetch('/api/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, // Secure connection
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    bus_id: TARGET_BUS_ID, // Uses the variable defined at top of script
                    rating: selectedRating,
                    comment: commentText
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                alert("Thank you! Your feedback has been sent to the admin.");
                toggleFeedback(); // Close modal
                
                // Reset form
                document.getElementById('feedback-text').value = '';
                rate(0); // Reset stars
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Failed to send feedback. Please try again.");
            });
        }

        // START
        trackBus();
        setInterval(trackBus, UPDATE_INTERVAL);

    </script>
</body>
</html>