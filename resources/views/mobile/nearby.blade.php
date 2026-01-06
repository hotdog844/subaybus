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
            <div class="h-24 bg-gray-200 rounded-2xl w-full"></div>
            <div class="h-24 bg-gray-200 rounded-2xl w-full"></div>
            <div class="h-24 bg-gray-200 rounded-2xl w-full"></div>
        </div>
    </div>

    <script>
    // 1. Get Data from Laravel
    const rawStops = @json($stops);

    // 2. BACKUP LOCATION (Roxas City) - Safety Net
    const FALLBACK_LAT = 11.5853; 
    const FALLBACK_LNG = 122.7511;

    document.addEventListener("DOMContentLoaded", () => {
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

    function processLocation(position) {
        runApp(position.coords.latitude, position.coords.longitude);
    }

    function useFallbackLocation() {
        // Only run if the list is empty to prevent overwriting valid data
        if(document.getElementById("stops-list").innerHTML.trim() === "") {
            console.warn("Using fallback location.");
            runApp(FALLBACK_LAT, FALLBACK_LNG);
        }
    }

    function runApp(userLat, userLng) {
        // Calculate Distance
        rawStops.forEach(stop => {
            const distKm = getDistance(userLat, userLng, stop.latitude, stop.longitude);
            stop.distance = distKm;
            stop.walkTime = Math.ceil((distKm * 1000) / 80); 
        });

        // Sort by Distance
        rawStops.sort((a, b) => a.distance - b.distance);

        renderList(rawStops);
    }

    function renderList(stops) {
        const container = document.getElementById("stops-list");
        container.innerHTML = "";

        if (stops.length === 0) {
            container.innerHTML = `<div class="text-center py-10 opacity-50"><p>No bus stops found.</p></div>`;
            return;
        }

        stops.forEach((stop, index) => {
            let distText = stop.distance < 1 
                ? Math.round(stop.distance * 1000) + " m" 
                : stop.distance.toFixed(1) + " km";

            let borderClass = index === 0 ? "border-green-500 ring-1 ring-green-100" : "border-transparent";

            // FIXED: We pass the INDEX 'i' to the function
            const html = `
                <div onclick="goToMap(${index})" 
                     class="bg-white p-5 rounded-2xl shadow-sm border ${borderClass} flex items-center justify-between cursor-pointer group hover:shadow-md mb-3">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fas fa-bus-alt text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">${stop.name}</h3>
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-walking"></i> ~${stop.walkTime} min</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300"></i>
                </div>`;
            container.innerHTML += html;
        });
    }

    // --- THE CRITICAL FIX ---
    function goToMap(index) {
        // 1. Look up the ACTUAL STOP object using the index
        const stop = rawStops[index];

        if (!stop) {
            console.error("Stop data missing for index:", index);
            return; 
        }

        // 2. Prepare URL Params with REAL coordinates
        const params = new URLSearchParams({
            focusLat: stop.latitude,  // Send 11.xxxx, NOT 0
            focusLng: stop.longitude, // Send 122.xxxx, NOT undefined
            stopName: stop.name
        });

        // 3. Attach Blue Line Data (If Admin Saved It)
        if (stop.route && stop.route.origin_lat) {
            params.append('oLat', stop.route.origin_lat);
            params.append('oLng', stop.route.origin_lng);
            params.append('dLat', stop.route.destination_lat);
            params.append('dLng', stop.route.destination_lng);
        }

        // 4. Go to Dashboard
        window.location.href = `/mobile/dashboard?${params.toString()}`;
    }

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; 
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
</script>
</body>
</html>