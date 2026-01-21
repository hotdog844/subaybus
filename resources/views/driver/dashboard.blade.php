<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cockpit - {{ $bus->bus_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: white; overflow: hidden; }
        .hud-font { font-family: 'Orbitron', sans-serif; letter-spacing: 2px; }
        
        /* Map container */
        #driver-map { height: 50vh; width: 100%; border-radius: 24px; z-index: 0; box-shadow: 0 0 20px rgba(0, 255, 179, 0.1); border: 2px solid rgba(255,255,255,0.1); }
        
        /* Pulse Animation for Radar */
        .passenger-marker { animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(255, 234, 167, 0.7); }
            70% { transform: scale(1.1); opacity: 0; box-shadow: 0 0 0 10px rgba(255, 234, 167, 0); }
            100% { transform: scale(1); opacity: 0; }
        }

        /* Glassmorphism Panels */
        .glass-panel {
            background: rgba(15, 23, 42, 0.85); /* Darker for better contrast */
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .neon-text { text-shadow: 0 0 10px rgba(46, 204, 113, 0.6); }
        .modal-enter { opacity: 0; transform: scale(0.95); pointer-events: none; }
        .modal-active { opacity: 1; transform: scale(1); pointer-events: auto; }
    </style>
</head>
<body class="h-screen flex flex-col p-4 pb-6 gap-4">

    <div class="flex justify-between items-start">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center shadow-[0_0_15px_rgba(37,99,235,0.5)]">
                <i class="fas fa-bus text-xl text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold leading-none">{{ $bus->bus_number }}</h1>
                <p class="text-xs text-gray-400 uppercase tracking-widest mt-1">
                    Route: <span class="text-blue-400 font-bold">{{ $routeName }}</span>
                </p>
            </div>
        </div>
        
        <div class="text-right">
            <div id="live-clock" class="text-2xl font-bold hud-font text-white">00:00</div>
            <button onclick="endShift()" class="text-[10px] bg-red-900/50 text-red-400 px-2 py-1 rounded border border-red-500/30 hover:bg-red-600 hover:text-white transition mt-1">
                END SHIFT
            </button>
        </div>
    </div>

    <div class="relative flex-grow rounded-3xl overflow-hidden border border-gray-700 shadow-2xl">
        <div id="driver-map" class="h-full w-full"></div>
        
        <div class="absolute top-4 left-4 z-[500] glass-panel px-4 py-2 rounded-full flex items-center gap-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_10px_#2ecc71]"></div>
            <span class="text-xs font-bold tracking-wide text-gray-200">GPS LIVE</span>
        </div>

        <div class="absolute top-4 right-4 z-[500] glass-panel pl-6 pr-4 py-3 rounded-l-2xl border-r-4 border-blue-500 text-right shadow-lg">
            <span class="text-[10px] text-blue-400 font-bold uppercase tracking-wider block mb-1">Next Stop</span>
            <h3 id="next-stop-display" class="text-lg font-bold text-white leading-none">Calculating...</h3>
            <p id="dist-display" class="text-xs text-gray-400 mt-1">-- km away</p>
        </div>

        <div class="absolute bottom-4 left-4 z-[500] glass-panel px-5 py-3 rounded-2xl flex flex-col items-center border-l-4 border-green-500 shadow-lg">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Speed</span>
            <div class="flex items-baseline gap-1">
                <span id="speed-display" class="text-4xl font-black hud-font text-white">0</span>
                <span class="text-xs text-gray-400 font-bold">km/h</span>
            </div>
        </div>

        <div class="absolute bottom-4 right-4 z-[500]">
            <button onclick="recenterMap()" class="w-12 h-12 rounded-full bg-white text-gray-900 flex items-center justify-center shadow-lg hover:bg-gray-200 active:scale-95 transition">
                <i class="fas fa-crosshairs text-lg"></i>
            </button>
        </div>
    </div>

    <div class="h-auto flex flex-col gap-3">
        
        <div class="grid grid-cols-2 gap-3">
            <div class="glass-panel rounded-2xl p-4 flex flex-col items-center justify-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-blue-500/10 group-hover:bg-blue-500/20 transition"></div>
                <span class="text-xs text-blue-300 font-bold uppercase tracking-wider mb-1">On Board</span>
                <span id="passenger-display" class="text-5xl font-black hud-font text-white neon-text">{{ $bus->passenger_count }}</span>
            </div>

            <div class="glass-panel rounded-2xl p-4 flex flex-col items-center justify-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-green-500/10 group-hover:bg-green-500/20 transition"></div>
                <span class="text-xs text-green-300 font-bold uppercase tracking-wider mb-1">Est. Revenue</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg text-green-400 font-bold">₱</span>
                    <span id="revenue-display" class="text-4xl font-black hud-font text-white">0</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-3 h-16">
            <button onclick="updateCount(-1)" class="col-span-1 rounded-xl bg-gray-800 border border-gray-700 text-red-400 hover:bg-gray-700 hover:text-red-300 transition active:scale-95 flex items-center justify-center shadow-lg">
                <i class="fas fa-minus text-2xl"></i>
            </button>
            
            <button onclick="openReportModal()" class="col-span-2 rounded-xl glass-panel text-xs font-bold text-gray-300 hover:text-white hover:bg-white/10 hover:border-red-500 hover:text-red-400 transition flex flex-col items-center justify-center gap-1 border border-transparent">
                <i class="fas fa-exclamation-triangle text-lg mb-1"></i> REPORT ISSUE
            </button>

            <button onclick="updateCount(1)" class="col-span-1 rounded-xl bg-gradient-to-br from-blue-600 to-blue-800 border border-blue-500/50 text-white hover:from-blue-500 hover:to-blue-700 transition active:scale-95 flex items-center justify-center shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                <i class="fas fa-plus text-2xl"></i>
            </button>
        </div>
    </div>

    <div id="report-modal" class="fixed inset-0 z-[1000] bg-black/80 backdrop-blur-sm flex items-center justify-center transition-all duration-300 modal-enter">
        <div class="bg-gray-900 border border-gray-700 w-11/12 max-w-sm rounded-3xl p-6 shadow-2xl relative">
            <button onclick="closeReportModal()" class="absolute top-4 right-4 text-gray-500 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2"><i class="fas fa-broadcast-tower text-red-500"></i> Report Incident</h3>
            <p class="text-gray-400 text-xs mb-6">This will alert the admin and nearby passengers.</p>
            <div class="space-y-3">
                <button onclick="submitReport('Traffic', 'warning')" class="w-full p-4 rounded-xl bg-orange-500/10 border border-orange-500/30 text-orange-400 hover:bg-orange-500 hover:text-white transition flex items-center gap-4 group">
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center group-hover:bg-white/20"><i class="fas fa-traffic-light text-lg"></i></div>
                    <div class="text-left"><div class="font-bold">Heavy Traffic</div><div class="text-[10px] opacity-70">~15 min delay</div></div>
                </button>
                <button onclick="submitReport('Accident', 'danger')" class="w-full p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white transition flex items-center gap-4 group">
                    <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center group-hover:bg-white/20"><i class="fas fa-car-crash text-lg"></i></div>
                    <div class="text-left"><div class="font-bold">Road Accident</div><div class="text-[10px] opacity-70">Major stoppage</div></div>
                </button>
                <button onclick="submitReport('Mechanical', 'warning')" class="w-full p-4 rounded-xl bg-gray-700/50 border border-gray-600 text-gray-300 hover:bg-white hover:text-black transition flex items-center gap-4 group">
                    <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center group-hover:bg-gray-200"><i class="fas fa-wrench text-lg"></i></div>
                    <div class="text-left"><div class="font-bold">Mechanical Issue</div><div class="text-[10px] opacity-70">Bus breakdown</div></div>
                </button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // --- 1. CONFIG ---
        const FARE_PRICE = 15; 
        const assignedRouteId = {{ $bus->route_id ?? 'null' }};
        const assignedRouteName = "{{ $routeName }}";
        const busId = {{ $bus->id }};
        
        // ✅ GLOBAL STORE FOR STOPS (Needed for Next Stop Logic)
        let routeStopsList = []; 

        // --- 2. MAP SETUP ---
        var map = L.map('driver-map', { zoomControl: false }).setView([11.5853, 122.7511], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '' }).addTo(map);

        var passengerIcon = L.divIcon({
            className: 'passenger-marker',
            html: '<div style="background-color: #f1c40f; width: 14px; height: 14px; border-radius: 50%; box-shadow: 0 0 15px #f1c40f; border: 2px solid white;"></div>',
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        function recenterMap() {
            map.setView([11.5853, 122.7511], 14);
        }

        // --- 3. DRAW ROUTE LINES & STOPS (With Data Capture) ---
        function loadAssignedRoute() {
            if(!assignedRouteId) return;

            console.log("🔍 Driver Map: Loading Route ID", assignedRouteId);

            fetch('/api/routes') 
                .then(res => res.json())
                .then(routes => {
                    const myRoute = routes.find(r => r.id === assignedRouteId);
                    
                    if (myRoute) {
                        console.log("✅ Route Found:", myRoute.name);

                        // A. DRAW LINE
                        const routeColor = myRoute.color || '#3498db';
                        if(myRoute.path_data) {
                            let path = typeof myRoute.path_data === 'string' ? JSON.parse(myRoute.path_data) : myRoute.path_data;
                            
                            L.polyline(path, { color: routeColor, weight: 12, opacity: 0.2 }).addTo(map);
                            const line = L.polyline(path, { color: routeColor, weight: 6, opacity: 0.9 }).addTo(map);
                            map.fitBounds(line.getBounds(), { padding: [50, 50] });
                        }

                        // B. DRAW STOPS & SAVE DATA
                        let rawStops = myRoute.stops || myRoute.stops_json;

                        if (rawStops) {
                            let stops = typeof rawStops === 'string' ? JSON.parse(rawStops) : rawStops;
                            
                            // ✅ SAVE STOPS FOR LATER CALCULATION
                            routeStopsList = stops;

                            console.log(`📍 Drawing ${stops.length} stops for Driver.`);

                            stops.forEach((stop, index) => {
                                const stopIcon = L.divIcon({
                                    className: '',
                                    html: `<div style="
                                        background-color: white; 
                                        width: 18px; height: 18px; 
                                        border-radius: 50%; 
                                        border: 4px solid ${routeColor}; 
                                        box-shadow: 0 0 10px ${routeColor};
                                        z-index: 1000;
                                        display:flex; align-items:center; justify-content:center;
                                        font-weight:bold; font-size:10px; color:${routeColor};
                                    ">${index + 1}</div>`,
                                    iconSize: [18, 18],
                                    iconAnchor: [9, 9]
                                });

                                L.marker([stop.lat, stop.lng], { icon: stopIcon })
                                    .addTo(map)
                                    .bindPopup(`<b>${stop.name}</b>`);
                            });
                        } else {
                            console.log("Stops data empty for this route.");
                        }
                    }
                })
                .catch(err => console.error("Error:", err));
        }
        loadAssignedRoute();

        // --- 4. MATH HELPERS FOR DISTANCE ---
        function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
            var R = 6371; // Earth radius in km
            var dLat = deg2rad(lat2-lat1);  
            var dLon = deg2rad(lon2-lon1); 
            var a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2); 
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            var d = R * c; 
            return d;
        }

        function deg2rad(deg) {
            return deg * (Math.PI/180)
        }

        // --- 5. REAL-TIME NEXT STOP UPDATE ---
        function updateNextStop(lat, lng) {
            if (!routeStopsList || routeStopsList.length === 0) return;

            let minDistance = Infinity;
            let nearestStop = null;

            // Find nearest stop
            routeStopsList.forEach(stop => {
                let d = getDistanceFromLatLonInKm(lat, lng, stop.lat, stop.lng);
                if (d < minDistance) {
                    minDistance = d;
                    nearestStop = stop;
                }
            });

            // Update UI
            const nextStopEl = document.getElementById('next-stop-display');
            const distEl = document.getElementById('dist-display');

            if (nearestStop) {
                // If closer than 50 meters (0.05 km)
                if (minDistance < 0.05) {
                    nextStopEl.innerText = "ARRIVING AT:";
                    // Flash effect for attention
                    nextStopEl.className = "text-lg font-bold text-green-400 leading-none animate-pulse";
                    distEl.innerText = nearestStop.name;
                    distEl.className = "text-xl font-bold text-white mt-1";
                } else {
                    nextStopEl.innerText = nearestStop.name;
                    nextStopEl.className = "text-lg font-bold text-white leading-none";
                    distEl.innerText = minDistance.toFixed(2) + " km away";
                    distEl.className = "text-xs text-gray-400 mt-1";
                }
            }
        }

        // --- 6. WATCH GPS & CALL UPDATES ---
        navigator.geolocation.watchPosition((pos) => {
            const speed = pos.coords.speed; 
            const kmh = speed ? Math.round(speed * 3.6) : 0;
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            // Update Speedometer
            document.getElementById('speed-display').innerText = kmh;
            const speedBox = document.getElementById('speed-display').parentElement.parentElement;
            if(kmh > 60) {
                speedBox.classList.replace('border-green-500', 'border-red-500');
                speedBox.classList.add('animate-pulse');
            } else {
                speedBox.classList.replace('border-red-500', 'border-green-500');
                speedBox.classList.remove('animate-pulse');
            }

            // ✅ Update Next Stop
            updateNextStop(lat, lng);

        }, (err) => console.log(err), { enableHighAccuracy: true });


        // --- EXISTING LOGIC FOR PASSENGERS/REPORTS ---
        let currentCount = {{ $bus->passenger_count }};
        const display = document.getElementById('passenger-display');
        const revenueDisplay = document.getElementById('revenue-display');

        function updateRevenue() {
            let total = currentCount * FARE_PRICE;
            revenueDisplay.innerText = total.toLocaleString();
        }
        updateRevenue(); 

        function updateCount(change) {
            let newCount = currentCount + change;
            if(newCount < 0) newCount = 0;
            currentCount = newCount;
            display.innerText = currentCount;
            updateRevenue();
            fetch(`/driver/update/${busId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ passenger_count: currentCount })
            });
        }

        function fetchHails() {
            fetch('/api/driver/hails')
                .then(res => res.json())
                .then(data => {
                    data.forEach(req => {
                        L.marker([req.latitude, req.longitude], {icon: passengerIcon})
                            .addTo(map)
                            .bindPopup(`<div style="color:black"><b>PASSENGER</b><br>${req.user.name}</div>`);
                    });
                });
        }
        setInterval(fetchHails, 5000);
        fetchHails();

        function updateTime() {
            const now = new Date();
            document.getElementById('live-clock').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        setInterval(updateTime, 1000);
        updateTime();

        const modal = document.getElementById('report-modal');
        function openReportModal() { modal.classList.remove('modal-enter'); modal.classList.add('modal-active'); }
        function closeReportModal() { modal.classList.remove('modal-active'); modal.classList.add('modal-enter'); }
        function submitReport(title, type) {
            closeReportModal();
            fetch('/api/driver/report-incident', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ bus_id: busId, title: title, type: type })
            });
        }

        // --- END SHIFT LOGIC ---
        function endShift() {
            if(!confirm("Are you sure you want to end your shift? This will mark the bus as OFFLINE.")) {
                return;
            }

            fetch(`/driver/end-shift/${busId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    window.location.href = '/driver/menu'; 
                } else {
                    alert("Error ending shift. Please try again.");
                }
            });
        }
    </script>
</body>
</html>