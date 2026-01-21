<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nearby Stops</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
</head>
<body class="bg-gray-50 min-h-screen pb-20">

    <div class="bg-white px-6 py-5 sticky top-0 z-50 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('mobile.dashboard') }}" class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="font-extrabold text-xl text-gray-900 tracking-tight">Nearby Stops</h1>
                <div id="gps-status" class="text-xs text-orange-500 font-semibold flex items-center gap-1.5 mt-0.5">
                    <i class="fas fa-circle-notch fa-spin text-[10px]"></i> Getting your location...
                </div>
            </div>
        </div>
    </div>

    <div class="p-5 space-y-4" id="stops-list">
        <div class="animate-pulse space-y-4">
            <div class="h-20 bg-gray-200 rounded-2xl w-full"></div>
            <div class="h-20 bg-gray-200 rounded-2xl w-full"></div>
            <div class="h-20 bg-gray-200 rounded-2xl w-full"></div>
        </div>
    </div>

    <script>
    // Global variable to store processed stops
    let globalStops = [];

    // Fallback Location (Roxas City)
    const FALLBACK_LAT = 11.5853; 
    const FALLBACK_LNG = 122.7511;

    document.addEventListener("DOMContentLoaded", () => {
        // 1. Fetch Data First
        fetchStopsData().then(() => {
            // 2. Then Get Location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(processLocation, useFallbackLocation, {
                    enableHighAccuracy: true,
                    timeout: 5000, 
                    maximumAge: 0
                });
            } else {
                useFallbackLocation();
            }
        });
    });

    // --- FETCH DATA FROM API (Consistency with Dashboard) ---
    async function fetchStopsData() {
        try {
            const response = await fetch('/api/routes'); // Uses your existing API
            const routes = await response.json();
            
            globalStops = [];

            // Flatten Routes into a single list of Stops
            routes.forEach(route => {
                if(route.stops && route.stops.length > 0) {
                    route.stops.forEach(stop => {
                        // Normalize data
                        globalStops.push({
                            name: stop.name,
                            lat: parseFloat(stop.latitude || stop.lat),
                            lng: parseFloat(stop.longitude || stop.lng),
                            route_name: route.name,
                            route_color: route.color || '#3b82f6',
                            distance: 9999, // Placeholder
                            walkTime: 0
                        });
                    });
                }
            });
            console.log("Loaded " + globalStops.length + " stops from API.");
        } catch (error) {
            console.error("Failed to load stops:", error);
            document.getElementById("stops-list").innerHTML = `<div class="text-center text-red-400 py-10">Failed to load data.</div>`;
        }
    } // <--- THIS WAS MISSING

    function processLocation(position) {
        document.getElementById("gps-status").innerHTML = `<i class="fas fa-map-marker-alt text-green-500"></i> Location Found`;
        calculateAndRender(position.coords.latitude, position.coords.longitude);
    }

    function useFallbackLocation() {
        document.getElementById("gps-status").innerHTML = `<i class="fas fa-exclamation-triangle"></i> Using Default Location`;
        calculateAndRender(FALLBACK_LAT, FALLBACK_LNG);
    }

    // --- LOGIC: CALCULATE & SORT ---
    function calculateAndRender(userLat, userLng) {
        if (globalStops.length === 0) return;

        // 1. Calculate Distance for every stop
        globalStops.forEach(stop => {
            const distKm = getDistance(userLat, userLng, stop.lat, stop.lng);
            stop.distance = distKm;
            stop.walkTime = Math.ceil((distKm * 1000) / 80); // Avg walking speed 80m/min
        });

        // 2. Sort: Nearest first
        globalStops.sort((a, b) => a.distance - b.distance);

        // 3. Render
        renderList(globalStops);
    }

    function renderList(stops) {
        const container = document.getElementById("stops-list");
        container.innerHTML = "";

        if (stops.length === 0) {
            container.innerHTML = `<div class="text-center py-10 opacity-50"><p>No stops found nearby.</p></div>`;
            return;
        }

        const displayStops = stops.slice(0, 20);

        displayStops.forEach((stop) => {
            // Format Distance
            let distText = stop.distance < 1 
                ? Math.round(stop.distance * 1000) + " m" 
                : stop.distance.toFixed(1) + " km";

            // Determine Color based on Route
            let badgeClass = "bg-gray-100 text-gray-600";
            let iconColor = "text-gray-400 bg-gray-50";
            
            if(stop.route_name.includes('Red')) { badgeClass = "bg-red-100 text-red-600"; iconColor = "text-red-500 bg-red-50"; }
            if(stop.route_name.includes('Green')) { badgeClass = "bg-green-100 text-green-600"; iconColor = "text-green-500 bg-green-50"; }
            if(stop.route_name.includes('Blue')) { badgeClass = "bg-blue-100 text-blue-600"; iconColor = "text-blue-500 bg-blue-50"; }

            // SAFE STRING ESCAPING for the click event
            const safeName = stop.name.replace(/'/g, "\\'");
            const safeRoute = stop.route_name.replace(/'/g, "\\'");

            const html = `
                <div onclick="goToDashboard(${stop.lat}, ${stop.lng}, '${safeName}', '${safeRoute}')" 
                     class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between cursor-pointer active:scale-95 transition mb-3">
                    
                    <div class="flex items-center gap-4 overflow-hidden">
                        <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center ${iconColor}">
                            <i class="fas fa-bus-alt text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-gray-800 text-sm truncate">${stop.name}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wide ${badgeClass}">
                                    ${stop.route_name}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-right flex-shrink-0 pl-2">
                        <div class="font-bold text-gray-800 text-lg leading-none">${distText}</div>
                        <div class="text-xs text-gray-400 mt-1">~${stop.walkTime} min walk</div>
                    </div>
                </div>`;
            container.innerHTML += html;
        });
    }

    // --- NAVIGATION UPDATE ---
    function goToDashboard(lat, lng, name, route) {
        // Send Name and Route to dashboard via URL
        const params = new URLSearchParams({
            focusLat: lat,
            focusLng: lng,
            focusName: name,   // Sending Stop Name
            focusRoute: route  // Sending Route Name (Green/Red/etc)
        });
        window.location.href = `/mobile/dashboard?${params.toString()}`;
    }

    // --- MATH: HAVERSINE FORMULA ---
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
    </script>
</body>
</html>