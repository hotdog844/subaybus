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
        // =========================================================
        // 1. HARDCODED DATA (Matches Dashboard Exactly)
        // =========================================================
        const stops = [
            { name: "RCITT Terminal", latitude: 11.559207, longitude: 122.750817, desc: "Main Transport Hub" },
            { name: "Robinsons Place Hub", latitude: 11.569562, longitude: 122.751172, desc: "Mall & Transport Terminal" },
            { name: "Pueblo de Panay", latitude: 11.566475, longitude: 122.751907, desc: "Commercial District" },
            { name: "Filamer / Capiz Doctors", latitude: 11.575570, longitude: 122.753158, desc: "University & Hospital Zone" },
            { name: "Roxas City Hall / Plaza", latitude: 11.583036, longitude: 122.752298, desc: "City Center" },
            { name: "Gaisano Grand / CityMall", latitude: 11.589697, longitude: 122.752243, desc: "Shopping District" },
            { name: "SM City Roxas", latitude: 11.596416, longitude: 122.748605, desc: "Shopping Mall" },
            { name: "Roxas Airport", latitude: 11.598791, longitude: 122.745879, desc: "Airport Terminal" },
            { name: "Peoples Park (Baybay)", latitude: 11.606579, longitude: 122.736620, desc: "Seafood & Recreation" },
            { name: "Culasi Terminal", latitude: 11.605087, longitude: 122.710023, desc: "Port Area" },
            { name: "Libas Terminal", latitude: 11.591211, longitude: 122.723769, desc: "Fishing Port Area" },
            { name: "Capiz Provincial Capitol", latitude: 11.584374, longitude: 122.769649, desc: "Government Center" },
            { name: "Villareal Stadium", latitude: 11.576415, longitude: 122.758533, desc: "Sports Complex" },
            { name: "Tanza Welcome Arc", latitude: 11.585504, longitude: 122.777546, desc: "Boundary Marker" }
        ];

        // 2. Haversine Formula (Calculate distance in km)
        function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
            var R = 6371; 
            var dLat = deg2rad(lat2-lat1);  
            var dLon = deg2rad(lon2-lon1); 
            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2); 
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            return R * c; 
        }

        function deg2rad(deg) { return deg * (Math.PI/180); }

        // 3. Get User Location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            showError();
        }

        function showPosition(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            const statusEl = document.getElementById("user-location-status");
            statusEl.innerText = "Location Found";
            statusEl.classList.remove("text-gray-400");
            statusEl.classList.add("text-green-600", "font-bold");

            // 4. Calculate Distances
            stops.forEach(stop => {
                stop.distance = getDistanceFromLatLonInKm(userLat, userLng, stop.latitude, stop.longitude);
            });

            // Sort: Nearest first
            stops.sort((a, b) => a.distance - b.distance);

            // 5. Render List
            const container = document.getElementById("stops-list");
            container.innerHTML = ""; 

            stops.forEach((stop, index) => {
                // Format distance
                let distDisplay;
                if (stop.distance < 1) {
                    distDisplay = Math.round(stop.distance * 1000) + " m";
                } else {
                    distDisplay = stop.distance.toFixed(1) + " km";
                }

                // Logic: Highlight the single closest stop
                let badge = index === 0 
                    ? `<span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Nearest</span>` 
                    : `<span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2 py-1 rounded-full">${distDisplay}</span>`;

                let borderClass = index === 0 ? "border-green-500 ring-2 ring-green-50" : "border-gray-100";

                // Directions Link (Walking Mode)
                const mapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${stop.latitude},${stop.longitude}&travelmode=walking`;

                const item = `
                    <div class="bg-white p-4 rounded-2xl shadow-sm border ${borderClass} flex items-center justify-between group active:scale-95 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm flex-shrink-0">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm leading-tight">${stop.name}</h4>
                                <p class="text-[11px] text-gray-400 mb-1.5">${stop.desc}</p>
                                ${badge}
                            </div>
                        </div>
                        <a href="${mapsUrl}" target="_blank" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-blue-500 hover:text-white transition shadow-sm border border-gray-100">
                            <i class="fas fa-walking"></i>
                        </a>
                    </div>
                `;
                container.innerHTML += item;
            });
        }

        function showError(error) {
            document.getElementById("user-location-status").innerText = "GPS Off";
            document.getElementById("stops-list").innerHTML = `
                <div class='text-center p-8 mt-10'>
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-location-slash"></i>
                    </div>
                    <h3 class="text-gray-800 font-bold mb-2">Location Required</h3>
                    <p class="text-gray-500 text-sm">Please enable GPS to calculate distances to the bus stops.</p>
                </div>`;
        }
    </script>
</body>
</html>