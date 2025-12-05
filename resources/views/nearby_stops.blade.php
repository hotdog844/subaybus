<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Nearby Stops - SubayBus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f4f6f8; }</style>
</head>
<body class="bg-gray-50 h-screen flex flex-col">

    <div class="bg-white px-6 py-4 flex items-center gap-4 shadow-sm sticky top-0 z-20">
        <a href="{{ route('mobile.dashboard') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-green-50 hover:text-green-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Nearby Stops</h1>
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <i class="fas fa-map-marker-alt text-green-500"></i>
                <span id="user-location-status">Locating you...</span>
            </p>
        </div>
    </div>

    <div id="stops-list" class="flex-grow overflow-y-auto px-6 py-6 space-y-4 pb-24">
        <div class="animate-pulse space-y-4">
            <div class="h-20 bg-gray-200 rounded-2xl"></div>
            <div class="h-20 bg-gray-200 rounded-2xl"></div>
            <div class="h-20 bg-gray-200 rounded-2xl"></div>
        </div>
    </div>

    <script>
        // 1. Get Stops Data from Laravel
        const stops = @json($stops);

        // 2. Haversine Formula (Calculate distance between two GPS points)
        function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
            var R = 6371; // Radius of the earth in km
            var dLat = deg2rad(lat2-lat1);  
            var dLon = deg2rad(lon2-lon1); 
            var a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2); 
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            var d = R * c; // Distance in km
            return d;
        }

        function deg2rad(deg) {
            return deg * (Math.PI/180)
        }

        // 3. Get User Location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            document.getElementById("stops-list").innerHTML = "<div class='text-center text-gray-500 mt-10'>Geolocation is not supported by this browser.</div>";
        }

        function showPosition(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            document.getElementById("user-location-status").innerText = "Location Found";
            document.getElementById("user-location-status").classList.add("text-green-600");

            // 4. Calculate Distances
            stops.forEach(stop => {
                stop.distance = getDistanceFromLatLonInKm(userLat, userLng, stop.latitude, stop.longitude);
            });

            // Sort nearest first
            stops.sort((a, b) => a.distance - b.distance);

            // 5. Render List
            const container = document.getElementById("stops-list");
            container.innerHTML = ""; // Clear skeleton

            // NEW: Handle Empty List
            if (stops.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-10 opacity-50">
                        <i class="fas fa-map-signs text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500 text-sm">No bus stops found in the database.</p>
                        <p class="text-xs text-gray-400">Try running the BusStopSeeder.</p>
                    </div>
                `;
                return;
            }

            stops.forEach((stop, index) => {
                // Formatting Distance
                let distDisplay = stop.distance < 1 
                    ? Math.round(stop.distance * 1000) + " m" 
                    : stop.distance.toFixed(1) + " km";

                // Highlight the closest one
                let badge = index === 0 
                    ? `<span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Closest</span>` 
                    : `<span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2 py-1 rounded-full">${distDisplay} away</span>`;

                let borderClass = index === 0 ? "border-green-500 ring-1 ring-green-100" : "border-transparent";

                // FIXED: Google Maps Link
                const item = `
                    <div class="bg-white p-4 rounded-2xl shadow-sm border ${borderClass} flex items-center justify-between group active:scale-95 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">${stop.name}</h4>
                                <p class="text-xs text-gray-400 mb-1">${stop.location_description || 'Bus Stop'}</p>
                                ${badge}
                            </div>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query=${stop.latitude},${stop.longitude}" target="_blank" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-green-500 hover:text-white transition">
                            <i class="fas fa-location-arrow"></i>
                        </a>
                    </div>
                `;
                container.innerHTML += item;
            });
        }

        function showError(error) {
            document.getElementById("user-location-status").innerText = "Location Error";
            document.getElementById("stops-list").innerHTML = `
                <div class='text-center p-8'>
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3 class="text-gray-800 font-bold mb-2">Location Required</h3>
                    <p class="text-gray-500 text-sm">Please allow location access to see the nearest bus stops.</p>
                </div>`;
        }
    </script>
</body>
</html>