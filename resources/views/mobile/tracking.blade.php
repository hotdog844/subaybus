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
        .no-scrollbar::-webkit-scrollbar { display: none; }

        /* Grab Style Pulse */
        .pulse-green {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse-green 2s infinite;
        }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
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
            <span class="text-[10px] font-bold text-white uppercase tracking-wider">Live Tracking</span>
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
            <textarea id="feedback-text" class="w-full bg-gray-50 border border-gray-100 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 mb-4" rows="3" placeholder="Comments?"></textarea>
            <div class="grid grid-cols-2 gap-3">
                <button onclick="toggleFeedback()" class="py-3 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-100">Cancel</button>
                <button onclick="submitFeedback()" class="py-3 rounded-xl font-bold text-sm bg-gray-900 text-white shadow-lg active:scale-95">Submit</button>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0 z-50 bg-white rounded-t-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-0">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto my-3"></div>
        <div id="grab-ui-container">
            <div class="p-8 text-center text-gray-400 italic">Initializing live tracking...</div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // --- 1. REAL CONFIGURATION (For Database) ---
    // We use the real Bus ID so the feedback saves to the correct unit
    const TARGET_BUS_ID = {{ $busId }}; 
    let selectedRating = 0;

    // --- 2. DEMO CONFIGURATION (For Visuals) ---
    const DEMO_FARE = "₱10.20"; 
    const DEMO_SPEED = 2500; 
    
    // ROUTE: Roxas Integrated Terminal -> Brgy. Tanza
    const DEMO_PATH = [
        [11.5705, 122.7460], // Terminal
        [11.5740, 122.7475], 
        [11.5780, 122.7490], 
        [11.5820, 122.7505], 
        [11.5850, 122.7520], 
        [11.5880, 122.7535], 
        [11.5910, 122.7550], 
        [11.5940, 122.7570]  // Tanza
    ];

    let currentStep = 0;
    let movingForward = true;
    let busMarker = null;
    let routeLayer = null; 

    // --- INITIALIZE MAP ---
    const map = L.map('map', { zoomControl: false, attributionControl: false }).setView(DEMO_PATH[0], 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(map);
    routeLayer = L.layerGroup().addTo(map);

    // --- DRAW BLUE LINE ---
    const polyline = L.polyline(DEMO_PATH, {
        color: '#3b82f6', weight: 6, opacity: 0.6, lineCap: 'round'
    }).addTo(routeLayer);

    L.circleMarker(DEMO_PATH[0], {radius: 6, color: '#10b981', fillOpacity: 1, stroke: false}).addTo(routeLayer);
    L.circleMarker(DEMO_PATH[DEMO_PATH.length-1], {radius: 6, color: '#ef4444', fillOpacity: 1, stroke: false}).addTo(routeLayer);
    map.fitBounds(polyline.getBounds(), {padding: [50, 50]});

    // --- CREATE BUS MARKER ---
    const icon = L.divIcon({
        className: 'custom-bus-marker',
        html: `<div class="relative w-20 h-20 flex items-center justify-center">
                <div class="radar-ring"></div>
                <div class="relative z-10 bg-gray-900 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-2xl border-2 border-white"><i class="fas fa-bus"></i></div>
               </div>`,
        iconSize: [80, 80], iconAnchor: [40, 40]
    });
    busMarker = L.marker(DEMO_PATH[0], {icon: icon, zIndexOffset: 1000}).addTo(map);

    // --- ANIMATION LOOP ---
    function animateBus() {
        const nextCoord = DEMO_PATH[currentStep];
        busMarker.setLatLng(nextCoord);
        if (movingForward) {
            currentStep++;
            if (currentStep >= DEMO_PATH.length) { movingForward = false; currentStep = DEMO_PATH.length - 2; }
        } else {
            currentStep--;
            if (currentStep < 0) { movingForward = true; currentStep = 1; }
        }
        setTimeout(animateBus, DEMO_SPEED);
    }

    // --- UI SIMULATION ---
    function loadStaticUI() {
        const container = document.getElementById('grab-ui-container');
        const arrivalTime = new Date(new Date().getTime() + 15*60000).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

        container.innerHTML = `
            <div class="px-6 pb-8 space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 italic">Arriving by ${arrivalTime}</h3>
                        <p class="text-[11px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-md inline-block mt-1 uppercase tracking-wide">FARE: ${DEMO_FARE}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-[#00b894] shadow-sm"><i class="fas fa-bus-alt text-2xl"></i></div>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white border-2 border-white overflow-hidden flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name=Jeep+Driver&background=00b894&color=fff" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-gray-900 truncate">Test User</h4>
                            <span class="text-[10px] font-black bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-md">⭐ 5.0</span>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">PLATE: 9176466392</p>
                    </div>
                </div>
                <div class="pt-2">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Live Progress</h3>
                        <button onclick="toggleFeedback()" class="text-green-600 hover:underline font-bold text-[10px] uppercase">Rate Trip</button>
                    </div>
                    <div class="relative pl-8 space-y-8 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-gray-100">
                        <div class="relative flex items-center justify-between"><div class="absolute -left-[27px] w-2.5 h-2.5 rounded-full bg-gray-300 z-10"></div><p class="text-sm text-gray-500 font-medium">Transport Terminal</p><span class="text-[10px] font-bold text-gray-400">PASSED</span></div>
                        <div class="relative flex items-center justify-between"><div class="absolute -left-[27px] w-3 h-3 rounded-full bg-green-500 pulse-green z-10"></div><p class="text-sm text-gray-900 font-black">City Proper</p><span class="text-[10px] font-bold text-gray-400">NOW</span></div>
                        <div class="relative flex items-center justify-between"><div class="absolute -left-[27px] w-2.5 h-2.5 rounded-full bg-gray-300 z-10"></div><p class="text-sm text-gray-500 font-medium">Brgy. Tanza</p><span class="text-[10px] font-bold text-gray-400">SOON</span></div>
                    </div>
                </div>
            </div>`;
    }

    // --- REAL FEEDBACK LOGIC (RESTORED) ---
    function toggleFeedback() { document.getElementById('feedback-modal').classList.toggle('hidden'); }
    
    function rate(star) {
        selectedRating = star;
        const stars = document.querySelectorAll('#feedback-modal .fa-star');
        stars.forEach((s, idx) => { s.classList.toggle('text-yellow-400', idx < star); s.classList.toggle('text-gray-300', idx >= star); });
    }

    function submitFeedback() {
        if (selectedRating === 0) return alert("Select a rating");
        const token = document.querySelector('meta[name="csrf-token"]').content;
        
        // This is the REAL API Call
        fetch('/api/feedback', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ 
                bus_id: TARGET_BUS_ID, 
                rating: selectedRating, 
                comment: document.getElementById('feedback-text').value 
            })
        })
        .then(res => res.json())
        .then(() => { 
            alert("Feedback saved to database!"); 
            toggleFeedback(); 
        })
        .catch(err => {
            console.error(err);
            alert("Error saving feedback, but keeping demo running.");
        });
    }

    // START
    loadStaticUI();
    animateBus();
</script>
</body>
</html>