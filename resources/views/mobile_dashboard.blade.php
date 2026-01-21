<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Subaybus - Capiz Tracker</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Apply Inter font */
        body { font-family: 'Inter', sans-serif; overflow: hidden; }
        
        /* --- Custom Map Markers --- */
        .custom-bus-marker {
            background: transparent;
            border: none;
        }
        
        /* --- Glassmorphism Utilities --- */
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* --- Responsive Layout --- */
        @media (min-width: 768px) {
            .mobile-frame {
                max-width: 420px; 
                margin: 20px;
                left: 0; right: auto; 
                top: 0; bottom: 0;
                height: auto;
                background: transparent;
                pointer-events: none;
            }
            .sidebar-container {
                 height: 100%;
                 display: flex;
                 flex-direction: column;
                 pointer-events: auto;
                 background: white;
                 border-radius: 24px;
                 box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
                 overflow: hidden;
            }
            .mobile-only { display: none; }
        }
        @media (max-width: 767px) {
            .desktop-only { display: none; }
            .sidebar-container { background: transparent; box-shadow: none; }
        }
        /* Ensure Leaflet stays below our UI */
        .leaflet-top, .leaflet-bottom {
            z-index: 5 !important;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

@keyframes pulse-ring {
    0% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(0, 0, 0, 0.2); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(0, 0, 0, 0); }
    100% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(0, 0, 0, 0); }
}

.pin-pulse {
    /* Background color is now set inline by JS */
    border: 3px solid white;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(0,0,0,0.4); /* Stronger shadow */
    animation: pulse-ring 2s infinite;
}
    </style>
</head>
<body class="bg-gray-100 antialiased h-screen w-screen relative">
    
    <div id="search-overlay" class="fixed inset-0 bg-gray-50/95 backdrop-blur-xl z-[70] hidden flex-col transition-all duration-300">
    <div class="p-4 pt-6 flex items-center gap-3 bg-white border-b border-gray-100 shadow-sm">
        <button id="close-search" class="p-2 -ml-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-full transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
        </button>
        <div class="relative flex-grow">
            <input type="text" id="search-input" placeholder="Search routes, stops, locations" class="w-full bg-gray-100 rounded-xl py-3 px-4 pl-11 text-gray-800 font-medium outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
    </div>

    <div id="search-results" class="flex-grow overflow-y-auto p-4 no-scrollbar">
        <div id="live-search-container" class="hidden space-y-2 mb-4"></div>
        <div id="default-search-content" class="mt-4">
    
    <a href="{{ route('mobile.planner') }}" class="block mb-6">
        <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-center gap-4 active:scale-95 transition">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white shadow-md">
                <i class="fas fa-directions"></i>
            </div>
            <div>
                <h4 class="font-bold text-blue-900 text-sm">Get Directions</h4>
                <p class="text-xs text-blue-600">Plan a trip from A to B</p>
            </div>
            <i class="fas fa-chevron-right text-blue-300 ml-auto"></i>
        </div>
    </a>

    <div class="text-center mt-12 opacity-50">
        <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500 text-sm">Type above to find your bus stop.</p>
    </div>
</div>
    </div>
</div>

    <div id="map" class="absolute inset-0 z-0 bg-[#eefbf7]"></div>

    <div class="absolute top-0 left-0 right-0 z-50 p-4 pt-6 pointer-events-none flex flex-col items-center bg-gradient-to-b from-white/80 to-transparent">

    <div class="flex items-center gap-2 mb-4 drop-shadow-sm animate-fade-in">
        <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
        <h1 class="text-2xl font-black tracking-tight text-gray-800 font-inter">
            Subay<span class="text-green-600">Bus</span>
        </h1>
    </div>

    <div id="main-search-trigger" class="pointer-events-auto glass-panel border border-gray-200/80 rounded-full h-12 px-5 flex items-center shadow-[0_8px_20px_-6px_rgba(0,0,0,0.1)] cursor-pointer transition active:scale-95 bg-white/90 backdrop-blur hover:bg-white w-full md:w-96 group">

        <div class="mr-3 text-gray-400 group-hover:text-green-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <div class="flex-grow">
            <span class="text-gray-500 text-sm font-medium group-hover:text-gray-700">Where to go?</span>
        </div>

        <div class="w-px h-5 bg-gray-200 mx-3"></div>

        <div class="text-right">
            <span id="mobile-clock" class="text-xs font-bold text-gray-600 block leading-none mb-0.5">--:--</span>
            <span class="text-[9px] text-green-600 font-extrabold uppercase tracking-wider">PHT</span>
        </div>
    </div>
</div>

<style>
@keyframes wave {
    0% { transform: rotate(0deg); }
    25% { transform: rotate(-15deg); }
    50% { transform: rotate(10deg); }
    75% { transform: rotate(-5deg); }
    100% { transform: rotate(0deg); }
}
.group-hover\:animate-wave:hover {
    animation: wave 1s infinite;
}
</style>
    <div class="absolute top-36 right-2 z-40 flex flex-col items-center gap-3 transition-all">
    
    <button id="btn-notifications" class="w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:text-green-600 active:scale-95 transition relative border border-gray-100">
        <i class="fas fa-bell"></i>
        <div class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></div>
    </button>

    <button id="btn-hail" class="w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center text-yellow-500 hover:text-yellow-600 active:scale-95 transition border border-gray-100 group relative">
        <i class="fas fa-hand-paper text-lg group-hover:animate-wave"></i>
        <span class="absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-0 group-hover:animate-ping"></span>
    </button>

    <div class="relative group">
        <button id="btn-map-layers" class="w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:text-green-600 active:scale-95 transition border border-gray-100">
            <i class="fas fa-layer-group"></i>
        </button>

        <div id="map-layers-menu" class="absolute right-12 top-0 w-40 bg-white rounded-xl shadow-xl border border-gray-100 hidden p-2 flex-col gap-1 z-50 origin-top-right">
            <button onclick="setMapStyle('standard')" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg text-xs text-gray-700 w-full text-left">
                <div class="w-4 h-4 rounded bg-blue-200 border border-gray-200"></div>
                <span>Standard</span>
            </button>
            <button onclick="setMapStyle('satellite')" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg text-xs text-gray-700 w-full text-left">
                <div class="w-4 h-4 rounded bg-green-700 border border-gray-200"></div>
                <span>Satellite</span>
            </button>
            <button onclick="setMapStyle('dark')" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 rounded-lg text-xs text-gray-700 w-full text-left">
                <div class="w-4 h-4 rounded bg-gray-800 border border-gray-200"></div>
                <span>Dark Mode</span>
            </button>
        </div>
    </div>

    <button id="btn-toggle-stops" class="w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center text-green-600 border border-gray-100 active:scale-95 transition relative group">
        <i class="fas fa-map-signs text-lg"></i>
        <span class="absolute right-12 bg-black/70 text-white text-[10px] px-2 py-1 rounded hidden group-hover:block whitespace-nowrap">
            Toggle Stops
        </span>
    </button>

    <a href="{{ route('mobile.nearby') }}" class="w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center text-green-600 hover:bg-green-50 active:scale-95 transition border border-gray-100 group">
        <i class="fas fa-location-crosshairs text-lg group-hover:animate-pulse"></i>
    </a>

    <div class="flex flex-col bg-white rounded-full shadow-md border border-gray-100 overflow-hidden">
        <button id="zoom-in" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-green-600 active:bg-gray-50 transition border-b border-gray-50">
            <i class="fas fa-plus"></i>
        </button>
        <button id="zoom-out" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-green-600 active:bg-gray-50 transition">
            <i class="fas fa-minus"></i>
        </button>
    </div>
</div>

    <div id="bus-list-panel" class="fixed inset-x-0 bottom-0 z-[80] bg-gray-50 rounded-t-[30px] shadow-[0_-10px_40px_rgba(0,0,0,0.2)] transform translate-y-full transition-transform duration-300 ease-out flex flex-col h-[80vh]">
    
    <div class="w-full flex justify-center pt-3 pb-1 bg-white rounded-t-[30px]" id="close-bus-list">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
    </div>

    <div class="px-6 py-4 bg-white border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Active Routes</h2>

        <div class="relative mb-4">
            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" id="route-search-input" placeholder="Search routes" class="w-full bg-gray-100 text-gray-800 rounded-xl py-3 pl-10 pr-4 outline-none focus:ring-2 focus:ring-green-500 transition">
        </div>

        <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
            <button id="filter-all" class="px-4 py-2 bg-[#00b894] text-white text-sm font-medium rounded-lg shadow-sm whitespace-nowrap">
    All Routes
</button>
<button id="filter-distance" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg whitespace-nowrap transition">
    By Distance
</button>
<button id="filter-stops" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg whitespace-nowrap transition">
    By Stop
</button>
        </div>
    </div>

    <div id="bus-list-container" class="flex-grow overflow-y-auto p-4 space-y-3 no-scrollbar">
        </div>
</div>

<div id="bus-detail-panel" class="fixed inset-0 z-[90] bg-gray-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col h-full">
    
    <div class="bg-white px-4 py-4 flex items-center justify-between border-b border-gray-100 shadow-sm sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <button id="close-detail-panel" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h2 id="detail-title" class="text-lg font-bold text-gray-800">Route Details</h2>
        </div>
        
        <button id="btn-favorite" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-red-500 hover:border-red-200 transition shadow-sm active:scale-90">
            <i class="fas fa-heart"></i>
        </button>
    </div>

    <div class="flex-grow overflow-y-auto p-4 space-y-6 pb-24 no-scrollbar">
        
        <div class="bg-gray-100 rounded-2xl p-6 flex flex-col items-center justify-center relative overflow-hidden">
            <div class="w-full h-24 relative flex items-center">
                <div class="absolute w-full h-1 bg-green-500 rounded-full"></div>
                <div class="absolute left-0 w-4 h-4 bg-white border-4 border-green-500 rounded-full"></div>
                <div class="absolute right-0 w-4 h-4 bg-white border-4 border-green-500 rounded-full"></div>
                <div class="absolute left-1/2 -translate-x-1/2 w-10 h-10 bg-[#00b894] rounded-full flex items-center justify-center text-white shadow-md z-10 animate-bounce">
                    <i class="fas fa-bus"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Live Progress Visualization</p>
        </div>

        <button id="btn-track-live" class="w-full bg-[#00b894] text-white font-bold py-4 rounded-xl shadow-lg shadow-green-500/30 active:scale-95 transition">
            Track Bus Live
        </button>

        <div>
            <h3 class="text-gray-800 font-bold mb-4">All Stops</h3>
            <div id="stops-timeline" class="space-y-0 pl-2">
                </div>
        </div>
    </div>
</div>

   <div id="main-trip-card" class="fixed inset-x-4 bottom-28 z-[100] transform translate-y-[200%] pointer-events-none transition-transform duration-300 ease-out">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
        
        <div id="trip-card-header" class="px-5 py-3 bg-gray-800 flex justify-between items-center transition-colors duration-300">
            <div>
                <h3 id="card-bus-number" class="text-white font-bold text-lg leading-none">Bus --</h3>
                <p id="card-route-name" class="text-white/80 text-xs mt-0.5 font-medium">Loading Route...</p>
            </div>
            
            <button id="close-trip-card" class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4 flex items-center justify-between gap-4">
            
            <div class="flex flex-col">
                 <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Capacity</span>
                 <div id="live-passenger-count" class="text-gray-800 font-bold text-sm mt-0.5">
                    <i class="fas fa-spinner fa-spin"></i>
                 </div>
            </div>

            <button id="btn-track-immediate" class="flex-grow bg-gray-900 text-white text-sm font-bold py-3 px-4 rounded-xl shadow-lg active:scale-95 transition flex items-center justify-center gap-2">
                <i class="fas fa-crosshairs"></i> Track Live
            </button>
        </div>
    </div>
</div>

    <div id="alerts-panel" class="fixed inset-0 z-[100] bg-black/20 backdrop-blur-sm hidden transition-opacity duration-300">
    
    <div id="alerts-card" class="absolute top-20 right-4 w-80 bg-white rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300 origin-top-right">
        
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
            <h3 class="font-bold text-gray-800">Notifications</h3>
            <button id="close-alerts" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="max-h-[60vh] overflow-y-auto p-2">
            
            <div class="p-3 mb-2 rounded-xl bg-red-50 border border-red-100 flex gap-3">
                <div class="mt-1 flex-shrink-0 text-red-500">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-red-700">Service Suspended</h4>
                    <p class="text-xs text-red-600 mt-1">Route 10 (North Loop) is temporarily halted due to flooding at Km 4.</p>
                    <span class="text-[10px] text-red-400 font-bold mt-2 block">10 mins ago</span>
                </div>
            </div>

            <div class="p-3 mb-2 rounded-xl bg-orange-50 border border-orange-100 flex gap-3">
                <div class="mt-1 flex-shrink-0 text-orange-500">
                    <i class="fas fa-traffic-light"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-orange-800">Heavy Traffic</h4>
                    <p class="text-xs text-orange-700 mt-1">Expect 15-20 min delays along Roxas Avenue due to road works.</p>
                    <span class="text-[10px] text-orange-400 font-bold mt-2 block">1 hour ago</span>
                </div>
            </div>

            <div class="p-3 rounded-xl hover:bg-gray-50 transition flex gap-3">
                <div class="mt-1 flex-shrink-0 text-blue-500">
                    <i class="fas fa-cloud-rain"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800">Weather Update</h4>
                    <p class="text-xs text-gray-500 mt-1">Light rain expected this afternoon. Drive safely!</p>
                    <span class="text-[10px] text-gray-400 font-bold mt-2 block">2 hours ago</span>
                </div>
            </div>

        </div>
        
        <div class="p-3 border-t border-gray-100 text-center">
            <button class="text-xs font-bold text-green-600 hover:text-green-700">Mark all as read</button>
        </div>
    </div>
</div>

<div class="fixed bottom-0 w-full bg-white border-t border-gray-100 px-8 py-4 flex justify-between items-center z-[70] rounded-t-[30px] shadow-[0_-5px_20px_rgba(0,0,0,0.03)]">
    
    <a href="{{ route('mobile.dashboard') }}" class="flex flex-col items-center gap-1.5 text-green-600 transition group">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 transition group-hover:scale-110">
            <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
            <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
        </svg>
        <span class="text-[10px] font-bold">Home</span>
    </a>
    
    <a href="#" id="nav-buses" class="flex flex-col items-center gap-1.5 text-gray-400 hover:text-green-600 transition group">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 transition group-hover:scale-110">
            <path d="M4.5 6.375a4.125 4.125 0 118.25 0 1.125 1.125 0 11-2.25 0 3 3 0 00-6 0l.991 2.786a1.49 1.49 0 01.304.568l2.667 9.335a.75.75 0 11-1.442.412l-2.567-8.988a5.25 5.25 0 01-1.173.427l-1.37 4.792a.75.75 0 01-1.442-.412l1.957-6.849c.156-.547.422-1.053.778-1.486L4.5 6.375z" />
            <path d="M14.894 14.418l-2.205-3.15a.75.75 0 111.229-.858l2.613 3.733a.75.75 0 11-1.23.861l-.407-.586zM13.669 17.866l-2.18-2.18a.75.75 0 111.06-1.06l2.18 2.18a.75.75 0 11-1.06 1.06z" />
            <path d="M21.75 4.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM22.5 12a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM21.75 19.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
        </svg>
        <span class="text-[10px] font-medium">Routes</span>
    </a>

    <a href="{{ route('mobile.profile') }}" class="flex flex-col items-center gap-1.5 text-gray-400 hover:text-green-600 transition group">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 transition group-hover:scale-110">
            <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
        </svg>
        <span class="text-[10px] font-medium">Profile</span>
    </a>
</div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // --- GLOBAL VARIABLES ---
    const tripCard = document.getElementById('main-trip-card');
    const closeCardBtn = document.getElementById('close-trip-card');

    var map = L.map('map', { zoomControl: false, attributionControl: false }).setView([11.5853, 122.7511], 14);
    
    // NEW: Variables for Route Lines
    let routeShapesData = {};   // Stores the path data (hidden)
    let activePolyline = null;  // The currently drawn line
    let activeMarkers = [];     // The Start/End icons
    let nearbyStopMarker = null;
    let guideLine = null;

    // --- NEW VARIABLES FOR SMART STOPS ---
    let stopLayer = L.layerGroup().addTo(map); // A special invisible layer for dots
    let allStopsData = []; // We will store the database information here

    // NEW: Store the ID of the bus we are following
    let trackedBusId = null;

    // Close card when X is clicked
    closeCardBtn.addEventListener('click', () => {
        // Bury it deep and disable clicks
        tripCard.classList.add('translate-y-[200%]', 'pointer-events-none');
        tripCard.classList.remove('pointer-events-auto');
        
        if (typeof trackedBusId !== 'undefined') {
            trackedBusId = null; 
        }
    });

    // --- MAP LAYER LOGIC ---
    const layerMenu = document.getElementById('map-layers-menu');
    const layerBtn = document.getElementById('btn-map-layers');
    let currentTileLayer = null; // Store reference to remove old one

    // 1. Define the Tile Styles
    const tileStyles = {
        standard: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', // Standard OSM (Colorful)
        satellite: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', // Real Satellite
        dark: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' // Night Mode
    };

    // 2. Function to Switch Style
    function setMapStyle(style) {
        // Remove old layer if exists
        if (currentTileLayer) map.removeLayer(currentTileLayer);

        // Add new layer
        currentTileLayer = L.tileLayer(tileStyles[style], {
            maxZoom: 20,
            attribution: '&copy; OpenStreetMap &copy; CartoDB &copy; Esri'
        }).addTo(map);

        // Close menu
        layerMenu.classList.add('hidden');
        
        // Optional: Adjust text colors for Dark Mode
        if(style === 'dark') {
            document.body.classList.add('dark-theme'); // You can add CSS for this later
        } else {
            document.body.classList.remove('dark-theme');
        }
    }

    // 3. Initialize Default
    setMapStyle('standard');

    // 4. Toggle Menu Logic
    layerBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent map click from closing it immediately
        layerMenu.classList.toggle('hidden');
    });

    // --- 2. LIVE TRACKING LOGIC ---
    var busMarkers = {}; // Object to store our markers: { bus_id: marker_object }

    // --- DRAW ROUTE LINES ---
    // --- 1. FEATURE: GUMAWA NG LISTAHAN SA SEARCH BAR ---
    function renderInitialSuggestions() {
        const defaultContent = document.getElementById('default-search-content');
        if(!defaultContent) return;

        console.log("Creating suggestions list for", allStopsData.length, "stops");

        // Panatilihin ang Trip Planner Button sa taas
        let html = `
            <a href="/mobile/planner" class="block mb-4">
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-center gap-4 active:scale-95 transition">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-directions"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-900 text-sm">Trip Planner</h4>
                        <p class="text-xs text-blue-600">Get directions from A to B</p>
                    </div>
                    <i class="fas fa-chevron-right text-blue-300 ml-auto"></i>
                </div>
            </a>
            <h3 class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-3 px-1">Suggested Stops</h3>
            <div class="space-y-2 pb-10">
        `;

        if (allStopsData.length === 0) {
            html += `<div class="text-center text-gray-400 text-xs py-4">No stops available.</div>`;
        } else {
            // Sort Alphabetically
            const sorted = allStopsData.slice().sort((a, b) => a.name.localeCompare(b.name));
            
            sorted.forEach(stop => {
                // Determine Icon Color based on Route
                let iconColor = "text-gray-400";
                if(stop.route_name) {
                    if(stop.route_name.includes('Red')) iconColor = "text-red-500";
                    else if(stop.route_name.includes('Green')) iconColor = "text-green-500";
                    else if(stop.route_name.includes('Blue')) iconColor = "text-blue-500";
                }

                // Add Item to List
                html += `
                    <div onclick="window.focusOnStopFromSearch(${stop.lat}, ${stop.lng})" 
                         class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3 active:bg-gray-50 transition cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-gray-50 ${iconColor} flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-pin text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">${stop.name}</h4>
                            <p class="text-[10px] text-gray-500">Ride: ${stop.route_name}</p>
                        </div>
                    </div>
                `;
            });
        }
        
        html += `</div>`;
        defaultContent.innerHTML = html;
    }

    // --- 2. HELPER: KAPAG KINLICK ANG SUGGESTION ---
    window.focusOnStopFromSearch = function(lat, lng) {
        // Hide Search Overlay
        document.getElementById('search-overlay').classList.add('hidden');
        document.getElementById('search-overlay').classList.remove('flex');
        
        // Fly to Map Location
        map.flyTo([lat, lng], 16, { animate: true, duration: 1.0 });
    };

    function loadRouteShapes() {
        console.log("Fetching system data...");
        
        fetch('/api/routes?t=' + new Date().getTime())
            .then(res => res.json())
            .then(routes => {
                
                // Reset Data Containers
                allStopsData = [];
                stopLayer.clearLayers();
                routeShapesData = {}; // Clear old shapes

                routes.forEach(route => {
                    // --- FIX IS HERE: Change 'route.path' to 'route.path_data' ---
                    if (route.path_data) {
                        let parsedPath = route.path_data; // <--- CORRECTED VARIABLE
                        
                        // Parse JSON string if needed
                        if (typeof parsedPath === 'string') {
                            try {
                                parsedPath = JSON.parse(parsedPath);
                            } catch (e) {
                                console.error("JSON Parse error for route:", route.name);
                                parsedPath = [];
                            }
                        }

                        if(Array.isArray(parsedPath)) {
                            // Store data using Name AND ID for easier lookup
                            const shapeData = { color: route.color || '#3b82f6', path: parsedPath };
                            routeShapesData[route.name] = shapeData;
                            routeShapesData[route.id] = shapeData;
                        }
                    }

                    // B. SAVE STOPS (Pins & Search)
                    if (route.stops && Array.isArray(route.stops) && route.stops.length > 0) { // Added Array check
                        route.stops.forEach(stop => {
                            const stopObj = {
                                ...stop,
                                lat: parseFloat(stop.lat),
                                lng: parseFloat(stop.lng),
                                route_name: route.name,
                                route_id: route.id,
                                route_color: route.color
                            };
                            
                            allStopsData.push(stopObj);

                            if(typeof createStopMarker === 'function') {
                                createStopMarker(stopObj);
                            }
                        });
                    }
                });

                console.log("✅ Data Loaded. Routes:", Object.keys(routeShapesData).length, "Stops:", allStopsData.length);
                renderInitialSuggestions(); 
            })
            .catch(err => console.error("Error loading routes:", err));
    }

    // --- 3. CREATE STOP PINS 
   // --- 3. CREATE STOP PINS (Fixed Popup Size) ---
    function createStopMarker(stop) {
        
        const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${stop.lat},${stop.lng}&travelmode=walking`;

        const stopIcon = L.divIcon({
            className: 'official-stop-marker',
            html: `<div style="background-color: white; color: #475569; width: 18px; height: 18px; border-radius: 50%; border: 2px solid #475569; box-shadow: 0 1px 3px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 9px;"><i class="fas fa-map-pin"></i></div>`,
            iconSize: [18, 18], 
            iconAnchor: [9, 9],
            popupAnchor: [0, -10]
        });

        // TINAASAN KO ANG MIN-WIDTH para magkasya agad ang buttons
        const popupContent = `
            <div class="text-center p-1 min-w-[240px] font-sans">
                <div class="mb-3">
                    <h4 class="font-black text-gray-800 text-base leading-tight mb-0.5">${stop.name}</h4>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Bus Stop</span>
                </div>

                <div class="flex flex-col gap-2">
                    <button onclick="window.visualizeRoute('${stop.route_id}')" 
                        class="w-full bg-[#00b894] hover:bg-[#00a383] text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md active:scale-95 transition flex items-center justify-center gap-2">
                       <i class="fas fa-route"></i> Show Route Line
                    </button>

                    <a href="${googleMapsUrl}" target="_blank" 
                       class="w-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm active:scale-95 transition flex items-center justify-center gap-2">
                        <i class="fas fa-walking text-blue-500"></i> Get Directions
                    </a>
                </div>

                <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center text-[10px] text-gray-500">
                    <span>Route:</span>
                    <span class="font-bold bg-gray-100 px-2 py-0.5 rounded text-gray-600">${stop.route_name}</span>
                </div>
            </div>
        `;

        // --- DITO ANG FIX: Nagdagdag tayo ng options sa bindPopup ---
        const marker = L.marker([stop.lat, stop.lng], { icon: stopIcon })
            .bindPopup(popupContent, {
                maxWidth: 300,   // Hangganan ng lapad
                minWidth: 250,   // Siguradong lapad (para di masira buttons)
                autoPanPadding: [20, 20] // Padding mula sa gilid ng screen
            });
        
        stopLayer.addLayer(marker);
        stop.marker = marker;
    }
    // Call it ONCE when map loads
    loadRouteShapes();

    // ============================================
    // 🎨 DRAW ROUTE ON MAP (Fixed: Exact ID Match First)
    // ============================================
    window.showRouteOnMap = function(inputIdentifier) { 
        console.log("Attempting to draw route for:", inputIdentifier);

        // 1. Clear previous line and markers
        if (typeof activePolyline !== 'undefined' && activePolyline) {
            map.removeLayer(activePolyline);
            activePolyline = null;
        }
        if (typeof activeMarkers !== 'undefined') {
            activeMarkers.forEach(m => map.removeLayer(m));
            activeMarkers = [];
        }

        // 2. FIND THE ROUTE DATA
        let routeData = null;
        let routeKey = null;

        // A. SUBUKAN ANG DIRECT MATCH (Priority: ID Match)
        // Ito ang mag-aayos ng problema mo. Hahanapin niya agad ang ID (e.g., "2") sa database.
        if (routeShapesData[inputIdentifier]) {
            routeData = routeShapesData[inputIdentifier];
            routeKey = inputIdentifier;
            console.log("✅ Exact match found via ID/Name:", routeKey);
        } 
        
        // B. KUNG WALANG DIRECT MATCH, SUBUKAN ANG KEYWORD SEARCH (Fallback)
        else {
            let searchStr = String(inputIdentifier).toLowerCase();
            
            // Hanapin sa keys ng routeShapesData kung may tugma
            let foundKey = Object.keys(routeShapesData).find(key => 
                key.toLowerCase().includes(searchStr)
            );

            if (foundKey) {
                routeData = routeShapesData[foundKey];
                routeKey = foundKey;
                console.log("🔍 Fuzzy match found:", routeKey);
            }
        }

        // C. KUNG WALA TALAGANG MAHANAP
        if (!routeData) {
            console.error("❌ Route data not found for:", inputIdentifier);
            // alert("Sorry, the map line for this route is not yet available."); 
            return; // Huminto dito, HUWAG mag-default sa Green
        }

        // 3. Draw the Line (Ito yung existing drawing logic mo)
        activePolyline = L.polyline(routeData.path, {
            color: routeData.color,
            weight: 6,
            opacity: 0.9,
            lineCap: 'round',
            lineJoin: 'round',
            dashArray: '1, 10', 
        }).addTo(map);
        
        // Background line
        const bgLine = L.polyline(routeData.path, {
            color: routeData.color,
            weight: 3,
            opacity: 0.4
        }).addTo(map);
        
        activePolyline = L.layerGroup([bgLine, activePolyline]).addTo(map);

        // 4. Add Start & End Markers
        if (routeData.path.length > 0) {
            const start = routeData.path[0];
            const end = routeData.path[routeData.path.length - 1];

            const startIcon = L.divIcon({
                className: '',
                html: `<div style="background: white; border: 4px solid #2ecc71; width: 16px; height: 16px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            const endIcon = L.divIcon({
                className: '',
                html: `<div style="background: white; border: 4px solid #e74c3c; width: 16px; height: 16px; border-radius: 0%; transform: rotate(45deg); box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            const m1 = L.marker(start, {icon: startIcon}).addTo(map).bindPopup("Start: " + routeKey);
            const m2 = L.marker(end, {icon: endIcon}).addTo(map).bindPopup("End: " + routeKey);
            
            activeMarkers.push(m1, m2);

            // 5. Zoom to fit
            map.fitBounds(bgLine.getBounds(), { padding: [50, 50] });
        }
    };

    // --- REPLACED: REAL-TIME BUS TRACKING WITH PASSENGER DATA ---
    async function fetchBusPositions() {
        try {
            const response = await fetch('/api/bus-locations');
            if (!response.ok) throw new Error("API Connection Failed");
            
            const buses = await response.json();

            buses.forEach(bus => {
                // 1. SAFETY CHECKS
                if (!bus.lat || !bus.lng) return; 
                
                var lat = parseFloat(bus.lat);
                var lng = parseFloat(bus.lng);

                // 2. COLOR LOGIC
                let markerColor = '#636e72'; // Default Gray
                if (bus.route_name) {
                    if (bus.route_name.includes("Red")) markerColor = '#e74c3c';
                    else if (bus.route_name.includes("Blue")) markerColor = '#0984e3';
                    else if (bus.route_name.includes("Green")) markerColor = '#00b894';
                    else if (bus.route_name.includes("UV")) markerColor = '#6c5ce7';
                }
                
                // 3. CREATE/UPDATE MARKER
                if (busMarkers[bus.id]) {
                    // UPDATE EXISTING
                    var existingMarker = busMarkers[bus.id];
                    existingMarker.setLatLng([lat, lng]);
                    
                    // Update Color if changed
                    var updatedIcon = L.divIcon({
                        className: 'custom-bus-marker',
                        html: `<div style="background-color: ${markerColor}; width: 35px; height: 35px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;"><i class="fas fa-bus" style="font-size: 16px;"></i></div>`,
                        iconSize: [35, 35],
                        iconAnchor: [17, 17],
                        popupAnchor: [0, -20]
                    });
                    existingMarker.setIcon(updatedIcon);

                } else {
                    // CREATE NEW
                    var busIcon = L.divIcon({
                        className: 'custom-bus-marker',
                        html: `<div style="background-color: ${markerColor}; width: 35px; height: 35px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;"><i class="fas fa-bus" style="font-size: 16px;"></i></div>`,
                        iconSize: [35, 35],
                        iconAnchor: [17, 17],
                        popupAnchor: [0, -20]
                    });

                    var newMarker = L.marker([lat, lng], {icon: busIcon}).addTo(map);
                    
                    // --- UPDATE CLICK EVENT ---
                    newMarker.on('click', function(e) {
                        L.DomEvent.stopPropagation(e); 

                        // 1. Update the Card Info
                        if (typeof updateTripCard === 'function') updateTripCard(bus);
                        
                        // 2. Draw the Route Line
                        if (typeof showRouteOnMap === 'function') {
                            // Priority: Route Name -> Route ID -> Bus Number
                            // Ito ang mag-aayos ng "Not Found" error
                            const identifier = bus.route_name || bus.route_id || bus.bus_number;
                            showRouteOnMap(identifier);
                        }
                    });

                    busMarkers[bus.id] = newMarker;
                }
            });

        } catch (error) {
            console.error('Error fetching bus positions:', error);
        }
    }

    // --- PASSENGER HAIL LOGIC ---
    const btnHail = document.getElementById('btn-hail');
    let isHailing = false;

    btnHail.addEventListener('click', () => {
        if (isHailing) return; // Prevent double clicks
        
        // 1. Get User Location
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser.");
            return;
        }

        // Show loading state
        btnHail.innerHTML = '<i class="fas fa-spinner fa-spin text-yellow-500"></i>';
        isHailing = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // 2. Send to Server
                fetch('/api/hail', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ lat: lat, lng: lng })
                })
                .then(res => res.json())
                .then(data => {
                    isHailing = false;
                    
                    if (data.status === 'success') {
                        // Success Feedback
                        btnHail.innerHTML = '<i class="fas fa-check text-green-500"></i>';
                        btnHail.classList.add('bg-green-50');
                        
                        // Show a temporary alert popup
                        showToast("Driver Notified! 👋", "Wait here. Your signal is active for 15 mins.");
                        
                        // Reset button after 3 seconds
                        setTimeout(() => {
                            btnHail.innerHTML = '<i class="fas fa-hand-paper text-yellow-500"></i>';
                            btnHail.classList.remove('bg-green-50');
                        }, 5000);

                    } else {
                        // Error (e.g., Spam protection)
                        btnHail.innerHTML = '<i class="fas fa-exclamation text-red-500"></i>';
                        showToast("Please Wait", data.message || "You are hailing too often.");
                        
                        setTimeout(() => {
                            btnHail.innerHTML = '<i class="fas fa-hand-paper text-yellow-500"></i>';
                        }, 3000);
                    }
                })
                .catch(err => {
                    isHailing = false;
                    console.error(err);
                    btnHail.innerHTML = '<i class="fas fa-times text-red-500"></i>';
                    showToast("Error", "Could not connect to server.");
                });
            },
            (error) => {
                isHailing = false;
                btnHail.innerHTML = '<i class="fas fa-times text-red-500"></i>';
                showToast("Location Error", "Please enable GPS to hail a bus.");
            }
        );
    });

    // Helper: Simple Toast Notification
    function showToast(title, message) {
        // Reuse your existing alerts panel logic or create a simple temporary one
        // For now, let's use a standard browser alert or console if no toast UI exists
        // But since you have an alerts-panel, let's try to inject it there or use a simple alert
        alert(`${title}\n${message}`); 
    }

    // --- 3. FUNCTION TO UPDATE THE CARD (With Dynamic Colors) ---
    function updateTripCard(bus) {
        selectedBusData = bus;
        
        // 1. Get Real Data
        let passengerCount = bus.passenger_count || 0;
        let maxCapacity = bus.capacity || bus.max_capacity || 40; 
        let routeName = bus.route_name || "Active Service";
        
        // 2. Update Text Content
        document.getElementById('card-bus-number').innerText = bus.bus_number;
        document.getElementById('card-route-name').innerText = routeName;

        let passengerElement = document.getElementById('live-passenger-count');
        if (passengerElement) {
            // Color code the capacity (Red if full, Green if open)
            let capColor = "text-green-600";
            if(passengerCount >= maxCapacity) capColor = "text-red-600";
            else if(passengerCount >= (maxCapacity * 0.8)) capColor = "text-orange-500";

            passengerElement.innerHTML = `<span class="${capColor}">${passengerCount}</span> <span class="text-gray-400">/ ${maxCapacity}</span>`;
        }

        // 3. DYNAMIC COLOR LOGIC 🎨
        const header = document.getElementById('trip-card-header');
        const actionBtn = document.getElementById('btn-track-immediate');
        
        // Reset base classes
        header.className = "px-5 py-3 flex justify-between items-center transition-colors duration-300";
        
        // Detect Color based on Route Name or Bus Number
        let bgClass = "bg-gray-800"; // Default Dark Gray
        let btnClass = "bg-gray-900";

        // Convert to lowercase to match safely
        let searchStr = (bus.bus_number + " " + routeName).toLowerCase();

        if (searchStr.includes('green') || searchStr.includes('01')) {
            bgClass = "bg-[#00b894]"; // Green
            btnClass = "bg-[#00b894]";
        } 
        else if (searchStr.includes('red') || searchStr.includes('42')) {
            bgClass = "bg-[#d63031]"; // Red
            btnClass = "bg-[#d63031]";
        } 
        else if (searchStr.includes('blue') || searchStr.includes('10')) {
            bgClass = "bg-[#0984e3]"; // Blue
            btnClass = "bg-[#0984e3]";
        }
        else if (searchStr.includes('uv') || searchStr.includes('pontevedra')) {
            bgClass = "bg-[#6c5ce7]"; // Purple
            btnClass = "bg-[#6c5ce7]";
        }

        // Apply new colors
        header.classList.add(...bgClass.split(" ")); // Spread operator to add class safely
        // Optional: Make the button match the route color too?
        // actionBtn.className = `flex-grow text-white text-sm font-bold py-3 px-4 rounded-xl shadow-lg active:scale-95 transition flex items-center justify-center gap-2 ${btnClass}`;

        // 4. Show the card (Slide it up)
        const tripCard = document.getElementById('main-trip-card');
        
        // Remove the hiding class
        tripCard.classList.remove('translate-y-[200%]', 'pointer-events-none');
        
        // Add interaction
        tripCard.classList.add('pointer-events-auto');
    }

    // Run immediately, then every 1 second
    fetchBusPositions();
    setInterval(fetchBusPositions, 1000);

    function showStopsForBus(busNumber) {
        stopLayer.clearLayers(); 

        let targetRoute = 'Green'; 
        let dotColor = '#00b894';

        if (busNumber.includes('Red')) { targetRoute = 'Red'; dotColor = '#d63031'; }
        if (busNumber.includes('Blue')) { targetRoute = 'Blue'; dotColor = '#0984e3'; }
        if (busNumber.includes('UV')) { targetRoute = 'UV'; dotColor = '#6c5ce7'; }

        let myStops = allStopsData.filter(stop => stop.route_name.includes(targetRoute));

        myStops.forEach(stop => {
            let stopIcon = L.divIcon({
                className: 'custom-stop-marker',
                html: `<div style="background-color: white; width: 12px; height: 12px; border-radius: 50%; border: 3px solid ${dotColor}; box-shadow: 0 1px 3px rgba(0,0,0,0.3);"></div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            let marker = L.marker([stop.lat, stop.lng], {icon: stopIcon})
                .bindPopup(`<div class="text-center font-bold text-sm">${stop.name}</div>`);
                
            stopLayer.addLayer(marker);
        });
    }

    // --- 3. UI CONTROLS (Search, Zoom, Clock) ---
    document.getElementById('zoom-in').addEventListener('click', function() { map.zoomIn(); });
    document.getElementById('zoom-out').addEventListener('click', function() { map.zoomOut(); });

    const searchOverlay = document.getElementById('search-overlay');
    const searchInput = document.getElementById('search-input');
    
    document.getElementById('main-search-trigger').addEventListener('click', () => {
        searchOverlay.classList.remove('hidden');
        searchOverlay.classList.add('flex');
        searchInput.focus(); 
    });

    document.getElementById('close-search').addEventListener('click', () => {
        searchOverlay.classList.add('hidden');
        searchOverlay.classList.remove('flex');
    });

    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const mobileClock = document.getElementById('mobile-clock');
        if(mobileClock) mobileClock.innerText = timeString;
        const desktopClock = document.getElementById('desktop-clock');
        if(desktopClock) desktopClock.innerText = timeString + " PHST";
    }
    setInterval(updateClock, 1000);
    updateClock();

    // --- 4. BUS LIST PANEL LOGIC ---
    const busListPanel = document.getElementById('bus-list-panel');
    const busListContainer = document.getElementById('bus-list-container');
    const navBuses = document.getElementById('nav-buses');
    const closeBusList = document.getElementById('close-bus-list');

    navBuses.addEventListener('click', (e) => {
        e.preventDefault(); 
        busListPanel.classList.remove('translate-y-full'); 
        renderBusList(); 
    });

    closeBusList.addEventListener('click', () => {
        busListPanel.classList.add('translate-y-full'); 
    });

    // --- 5. BUS DETAIL PANEL LOGIC ---
    const detailPanel = document.getElementById('bus-detail-panel');
    const closeDetailBtn = document.getElementById('close-detail-panel');
    const trackLiveBtn = document.getElementById('btn-track-live');
    const stopsContainer = document.getElementById('stops-timeline');
    let selectedBusData = null; 

    closeDetailBtn.addEventListener('click', () => {
        detailPanel.classList.add('translate-x-full'); 
    });

    trackLiveBtn.addEventListener('click', () => {
        if(selectedBusData) {
            window.location.href = '/mobile/track/' + selectedBusData.id;
        }
    });

    map.on('dragstart', function() {
        if (trackedBusId) {
            trackedBusId = null; 
        }
    });

    function generateStops(busNumber) {
        // 1. Tukuyin kung anong route ito base sa Bus Number/Name
        let targetRoute = "";
        let routeColor = "bg-gray-300";
        let activeColor = "bg-green-500";

        if (String(busNumber).includes("Green")) { targetRoute = "Green"; routeColor = "bg-green-200"; }
        else if (String(busNumber).includes("Red")) { targetRoute = "Red"; routeColor = "bg-red-200"; activeColor = "bg-red-500"; }
        else if (String(busNumber).includes("Blue")) { targetRoute = "Blue"; routeColor = "bg-blue-200"; activeColor = "bg-blue-500"; }
        else if (String(busNumber).includes("UV")) { targetRoute = "UV"; routeColor = "bg-purple-200"; activeColor = "bg-purple-500"; }

        // 2. I-filter ang stops mula sa loaded API data
        // (Make sure exact match or includes)
        const relevantStops = allStopsData.filter(stop => 
            stop.route_name && stop.route_name.includes(targetRoute)
        );

        // 3. Render HTML
        let html = '';
        
        if (relevantStops.length === 0) {
            stopsContainer.innerHTML = '<div class="text-center p-4 text-gray-400 text-sm">No stop details available for this route.</div>';
            return;
        }

        relevantStops.forEach((stop, index) => {
            const isLast = index === relevantStops.length - 1;
            const lineClass = isLast ? '' : 'h-full w-0.5 bg-gray-200 absolute left-[7px] top-4';
            
            // Simulation lang: Kunwari nasa first 3 stops ang bus (Active logic needs real data)
            const isActive = index === 0; 

            html += `
                <div class="relative flex gap-4 pb-8 last:pb-0">
                    <div class="${lineClass}"></div>
                    <div class="relative z-10 w-4 h-4 rounded-full ${isActive ? activeColor + ' ring-4 ring-gray-100' : 'bg-gray-300'} flex-shrink-0 mt-1"></div>
                    <div class="flex-grow flex justify-between items-start -mt-0.5">
                        <div>
                            <h4 class="${isActive ? 'text-gray-900 font-bold' : 'text-gray-600 font-medium'} text-sm">${stop.name}</h4>
                            <p class="text-xs text-gray-400">Stop #${index + 1}</p>
                        </div>
                    </div>
                </div>
            `;
        });
        stopsContainer.innerHTML = html;
    }

    function renderBusList(filter = 'all') {
    // Ensure we use the working API route
    fetch('/api/bus-locations')
        .then(res => res.json())
        .then(data => {
            
            // --- 1. SORTING LOGIC ---
            if (filter === 'stops') {
                // Sort Alphabetically by Route Name (A-Z)
                data.sort((a, b) => (a.route_name || "").localeCompare(b.route_name || ""));
            } else if (filter === 'distance') {
                // Sort by ID as a temporary proxy for distance
                data.sort((a, b) => a.id - b.id);
            }

            busListContainer.innerHTML = ''; 

            data.forEach(bus => {
                // 1. SMART IDENTIFIER (Fixes the "?" and overflowing numbers)
                let rawMatch = bus.bus_number.match(/\d+/);
                let displayIdentifier = rawMatch ? rawMatch[0] : bus.bus_number.charAt(0).toUpperCase();
                let iconContent = displayIdentifier.length > 3 
                    ? '<i class="fas fa-bus text-sm"></i>' 
                    : displayIdentifier;

                let displayName = bus.bus_number.replace('(REAL)', '').replace('(GHOST)', '').trim();

                // 2. DYNAMIC CAPACITY & COLOR CALCULATION (Fixes the ReferenceError)
                let load = bus.passenger_count || 0;
                let max = bus.capacity || 20; 
                let percent = Math.round((load / max) * 100);
                
                // Initialize the variable here so the template can see it
                let capacityColor = "bg-green-100 text-green-700"; 
                if(percent > 50) capacityColor = "bg-yellow-100 text-yellow-700";
                if(percent >= 90) capacityColor = "bg-red-100 text-red-700";

                // 3. BOX COLOR BASED ON ROUTE NAME
                let boxColor = "#00b894"; // Default Green
                if (displayName.toLowerCase().includes('red')) boxColor = "#eb4d4b";
                if (displayName.toLowerCase().includes('blue')) boxColor = "#0984e3";

                const item = document.createElement('div');
                item.className = "bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between cursor-pointer hover:shadow-md transition mb-3";
                
                item.innerHTML = `
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 flex-shrink-0 rounded-xl text-white flex items-center justify-center font-bold text-base shadow-sm overflow-hidden" 
                             style="background-color: ${boxColor}">
                            ${iconContent}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-gray-800 text-[15px] truncate">${displayName}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide border border-gray-100 bg-green-100 text-green-700">
                                    Active
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide border border-gray-100 ${capacityColor}">
                                    <i class="fas fa-users mr-1"></i> ${percent}%
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-gray-300 ml-2">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                `;

                item.addEventListener('click', () => {
                    selectedBusData = bus; 
                    document.getElementById('detail-title').innerText = displayName + " Details"; 
                    if (typeof generateStops === 'function') generateStops(bus.bus_number); 
                    detailPanel.classList.remove('translate-x-full'); 
                });

                busListContainer.appendChild(item);
            });
        });
}

    // --- 6. REAL NOTIFICATION SYSTEM (With Dismissal) ---
    const btnNotifications = document.getElementById('btn-notifications');
    const alertsPanel = document.getElementById('alerts-panel');
    const alertsCard = document.getElementById('alerts-card');
    const alertsContainer = document.querySelector('#alerts-card .max-h-\\[60vh\\]'); 

    function loadAlerts() {
        fetch('/api/alerts')
            .then(res => res.json())
            .then(data => {
                alertsContainer.innerHTML = ''; 

                // 1. Get list of dismissed IDs from phone memory
                const dismissed = JSON.parse(localStorage.getItem('dismissedAlerts') || '[]');

                // 2. Filter out dismissed alerts
                const activeAlerts = data.filter(alert => !dismissed.includes(alert.id));

                if (activeAlerts.length === 0) {
                    alertsContainer.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">No new notifications</div>';
                    // Hide Red Dot
                    const redDot = document.querySelector('#btn-notifications .bg-red-500');
                    if(redDot) redDot.style.display = 'none';
                    return;
                }

                activeAlerts.forEach(alert => {
                    // Colors
                    let colors = "bg-blue-50 border-blue-100 text-blue-500";
                    let icon = "fa-info-circle";
                    if(alert.type === 'warning') { colors = "bg-orange-50 border-orange-100 text-orange-500"; icon = "fa-exclamation-triangle"; }
                    if(alert.type === 'danger') { colors = "bg-red-50 border-red-100 text-red-500"; icon = "fa-ban"; }

                    const html = `
                        <div class="relative p-3 mb-2 rounded-xl border flex gap-3 ${colors} group">
                            <div class="mt-1 flex-shrink-0">
                                <i class="fas ${icon}"></i>
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-sm font-bold text-gray-800 pr-4">${alert.title}</h4>
                                <p class="text-xs text-gray-600 mt-1">${alert.message}</p>
                                <span class="text-[10px] text-gray-400 font-bold mt-2 block">
                                    ${new Date(alert.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                </span>
                            </div>
                            
                            <button onclick="dismissAlert(${alert.id})" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    alertsContainer.innerHTML += html;
                });
                
                // Show Red Dot
                const redDot = document.querySelector('#btn-notifications .bg-red-500');
                if(redDot) redDot.style.display = 'block';
            });
    }

    // --- NEW: Function to Dismiss Alert ---
    window.dismissAlert = function(id) {
        // 1. Get existing list
        const dismissed = JSON.parse(localStorage.getItem('dismissedAlerts') || '[]');
        
        // 2. Add new ID
        if(!dismissed.includes(id)) {
            dismissed.push(id);
            localStorage.setItem('dismissedAlerts', JSON.stringify(dismissed));
        }

        // 3. Reload list to make it disappear instantly
        loadAlerts();
    };

    // "Mark All as Read" Logic
    const markReadBtn = document.querySelector('#alerts-card button.text-green-600');
    if(markReadBtn) {
        markReadBtn.addEventListener('click', () => {
            fetch('/api/alerts')
            .then(res => res.json())
            .then(data => {
                const ids = data.map(a => a.id);
                // Save all current IDs to ignore list
                const dismissed = JSON.parse(localStorage.getItem('dismissedAlerts') || '[]');
                const combined = [...new Set([...dismissed, ...ids])];
                localStorage.setItem('dismissedAlerts', JSON.stringify(combined));
                loadAlerts(); // Refresh UI
            });
        });
    }

    // Load alerts when the bell is clicked
    btnNotifications.addEventListener('click', () => {
        alertsPanel.classList.remove('hidden');
        loadAlerts(); // <--- Fetch fresh data
        
        setTimeout(() => {
            alertsCard.classList.remove('scale-95', 'opacity-0');
            alertsCard.classList.add('scale-100', 'opacity-100');
        }, 10);
    });

    // --- FIX: CLOSE ALERTS LOGIC ---
    const closeAlertsBtn = document.getElementById('close-alerts');

    closeAlertsBtn.addEventListener('click', () => {
        // 1. Animate the card out (Scale down and fade out)
        alertsCard.classList.remove('scale-100', 'opacity-100');
        alertsCard.classList.add('scale-95', 'opacity-0');

        // 2. Hide the entire panel after the animation finishes (300ms)
        setTimeout(() => {
            alertsPanel.classList.add('hidden');
        }, 300);
    });

    // Optional: Close if clicking outside the card (on the blurred background)
    alertsPanel.addEventListener('click', (e) => {
        if (e.target === alertsPanel) {
            closeAlertsBtn.click(); // Trigger the same close logic
        }
    });

    // --- 7. FAVORITES LOGIC ---
    const btnFavorite = document.getElementById('btn-favorite');
    let currentFavorites = []; 

    function loadFavorites() {
        fetch('/api/favorites/ids')
            .then(res => res.json())
            .then(ids => {
                currentFavorites = ids;
            });
    }
    loadFavorites(); 

    function updateHeartButton(busId) {
        if (currentFavorites.includes(busId)) {
            btnFavorite.classList.remove('text-gray-400', 'bg-white');
            btnFavorite.classList.add('text-red-500', 'bg-red-50', 'border-red-200');
            btnFavorite.innerHTML = '<i class="fas fa-heart"></i>'; 
        } else {
            btnFavorite.classList.add('text-gray-400', 'bg-white');
            btnFavorite.classList.remove('text-red-500', 'bg-red-50', 'border-red-200');
            btnFavorite.innerHTML = '<i class="far fa-heart"></i>'; 
        }
    }

    btnFavorite.addEventListener('click', () => {
        if (!selectedBusData) return;
        const busId = selectedBusData.id;

        const isFav = currentFavorites.includes(busId);
        if (isFav) {
            currentFavorites = currentFavorites.filter(id => id !== busId); 
        } else {
            currentFavorites.push(busId); 
        }
        updateHeartButton(busId); 

        fetch('/api/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            },
            body: JSON.stringify({ bus_id: busId })
        });
    });

    // --- NEW: CONNECT THE BOTTOM CARD BUTTON ---
    const immediateTrackBtn = document.getElementById('btn-track-immediate');

    immediateTrackBtn.addEventListener('click', () => {
        if(selectedBusData && selectedBusData.id) {
            window.location.href = '/mobile/track/' + selectedBusData.id;
        } else {
            console.error("No bus selected!");
        }
    });

    // --- FIX: CLOSE MAP LAYERS WHEN CLICKING OUTSIDE ---
    document.addEventListener('click', (e) => {
        const layerMenu = document.getElementById('map-layers-menu');
        const layerBtn = document.getElementById('btn-map-layers');

        // If the menu is open, and the click was NOT inside the menu or the button...
        if (!layerMenu.classList.contains('hidden') && 
            !layerMenu.contains(e.target) && 
            !layerBtn.contains(e.target)) {
            
            // ...then close the menu.
            layerMenu.classList.add('hidden');
        }
    });

    // 7. EVENT LISTENERS
        
        // Map Click (Clear selection)
        map.on('click', function() {
            console.log("Map Clicked: Clearing active route line..."); // Debugging check

            // 1. Clear the Blue Route Line
            if (activePolyline) { 
                map.removeLayer(activePolyline); 
                activePolyline = null; 
            }

            // 2. Clear Start/End Markers (Green A / Red B)
            activeMarkers.forEach(m => map.removeLayer(m)); 
            activeMarkers = [];
            
            // 3. Hide the Bottom Trip Card
            if(tripCard) {
                tripCard.classList.add('translate-y-[200%]', 'pointer-events-none');
                tripCard.classList.remove('pointer-events-auto');
            }
            
            // 4. Hide Menus
            document.getElementById('map-layers-menu').classList.add('hidden');

            // NOTE: We deliberately DO NOT clear 'stopsLayer' here, so stops remain visible.
        });

    // ========================================================
    // 🚀 MASTER FUNCTION: SMART ROUTE GUIDE
    // ========================================================
    window.focusOnLocation = function(lat, lng, name = "Destination", routeName = null) {
        
        // 1. Close Search Overlay
        const searchOverlay = document.getElementById('search-overlay');
        searchOverlay.classList.add('hidden');
        searchOverlay.classList.remove('flex');

        // 2. Fly to the destination (Zoom level 16 is good context)
        map.flyTo([lat, lng], 16, { animate: true, duration: 1.5 });

        // 3. Remove old markers/lines
        if (nearbyStopMarker) map.removeLayer(nearbyStopMarker);
        if (guideLine) map.removeLayer(guideLine);

        // 4. Determine Bus Details
        let busColor = "gray";
        let rideMsg = "Bus Stop";
        let routeClass = "bg-gray-600";
        let busNumberForLogic = null; // We need this to trigger the line drawing
        
        // Map Route Names to IDs/Colors
        if (routeName) {
            if (routeName.includes('Red')) { 
                busColor = "#ef4444"; 
                rideMsg = "Ride the <b>Red Route</b>"; 
                routeClass = "bg-red-500";
                busNumberForLogic = "42"; // ID for Red
            }
            else if (routeName.includes('Green')) { 
                busColor = "#00b894"; 
                rideMsg = "Ride the <b>Green Route</b>"; 
                routeClass = "bg-[#00b894]";
                busNumberForLogic = "01"; // ID for Green
            }
            else if (routeName.includes('Blue')) { 
                busColor = "#0984e3"; 
                rideMsg = "Ride the <b>Blue Route</b>"; 
                routeClass = "bg-blue-500";
                busNumberForLogic = "10"; // ID for Blue
            }
        }

        // 5. Create Pulse Icon
        const pulseIcon = L.divIcon({
            className: '',
            html: `<div class="pin-pulse" style="background:${busColor}; border-color:white; width: 24px; height: 24px;"></div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        // 6. Add Marker
        nearbyStopMarker = L.marker([lat, lng], { icon: pulseIcon })
            .addTo(map)
            .bindPopup(`
                <div class="text-center p-3 min-w-[180px]">
                    <div class="text-[10px] uppercase font-bold text-gray-400 mb-1">Destination</div>
                    <div class="font-black text-gray-800 text-lg leading-tight mb-3">${name}</div>
                    
                    ${routeName ? `
                    <div class="mb-3 p-2 rounded-xl bg-gray-50 border border-gray-100 shadow-inner">
                        <div class="text-xs text-gray-500 mb-1 font-semibold">Suggested Route:</div>
                        <div class="text-white text-xs font-bold py-1.5 px-3 rounded-lg ${routeClass} shadow-md inline-block">
                            ${rideMsg}
                        </div>
                    </div>
                    
                    <button onclick="visualizeRoute('${busNumberForLogic}')" 
                        class="w-full bg-gray-900 text-white text-xs font-bold py-3 px-4 rounded-xl shadow-lg active:scale-95 transition flex items-center justify-center gap-2 hover:bg-gray-800">
                        <i class="fas fa-map-marked-alt"></i> View Route & Buses
                    </button>
                    ` : `
                    <div class="text-xs text-gray-400 italic">No specific route data available.</div>
                    `}
                </div>
            `)
            .openPopup();
    };

    // ============================================
    // 👁️ NEW: VISUALIZE ROUTE ACTION
    // ============================================
    window.visualizeRoute = function(busNumber) {
        
        if (!busNumber) {
            alert("Route data not found for this stop.");
            return;
        }

        // 1. Draw the Route Line (Using your existing function!)
        if (typeof showRouteOnMap === 'function') {
            showRouteOnMap(busNumber);
        }

        // 2. Close the popup so they can see the map
        map.closePopup();

        // 3. Give feedback
        // You can add a simple toast or console log here
        console.log("Visualizing route for bus: " + busNumber);
    };

    // ============================================
    // 🔗 UNIFIED LINKER: NEARBY -> DASHBOARD PIN (FIXED)
    // ============================================
    const urlParams = new URLSearchParams(window.location.search);
    const targetLat = parseFloat(urlParams.get('focusLat'));
    const targetLng = parseFloat(urlParams.get('focusLng'));

    // Check kung may coordinates galing sa URL
    if (!isNaN(targetLat) && !isNaN(targetLng)) {
        
        console.log("🔗 Connecting to Nearby Stop...", targetLat, targetLng);

        // Linisin ang URL agad-agad
        window.history.replaceState({}, document.title, window.location.pathname);

        // Maghintay hanggang sa ma-load ang mga bus stops
        const waitForStops = setInterval(() => {
            
            // CORRECTION: Gamitin ang 'stopLayer' (WALANG 's')
            if (typeof stopLayer !== 'undefined' && stopLayer.getLayers().length > 0) {
                
                clearInterval(waitForStops); // Stop waiting

                let found = false;

                // Hanapin ang pin sa 'stopLayer'
                stopLayer.eachLayer(function(layer) {
                    const lat = layer.getLatLng().lat;
                    const lng = layer.getLatLng().lng;

                    // Match coordinates
                    if (Math.abs(lat - targetLat) < 0.00001 && Math.abs(lng - targetLng) < 0.00001) {
                        
                        console.log("✅ Stop found! Zooming...");

                        // 1. Zoom
                        map.flyTo([lat, lng], 18, { animate: true, duration: 1.5 });

                        // 2. Open Popup
                        setTimeout(() => {
                            layer.openPopup();
                        }, 1600); 
                        
                        found = true;
                    }
                });

                if (!found) {
                    console.warn("Stop not found on map layer.");
                }
            }
        }, 500); 
    }

// ============================================
    // 🔍 LIVE SEARCH LOGIC
    // ============================================
    const searchInputRef = document.getElementById('search-input');
    const liveContainer = document.getElementById('live-search-container');
    const defaultContent = document.getElementById('default-search-content');

    searchInputRef.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();

        // A. If search is empty, show default suggestions
        if (query.length < 2) {
            liveContainer.classList.add('hidden');
            defaultContent.style.display = 'block';
            return;
        }

        // B. Filter the data (allStopsData is already loaded from your existing fetch!)
        // We filter by Name OR Route Name
        const matches = allStopsData.filter(stop => 
            (stop.name && stop.name.toLowerCase().includes(query)) ||
            (stop.route_name && stop.route_name.toLowerCase().includes(query))
        );

        // C. Render Results
        renderSearchResults(matches);
    });

    function renderSearchResults(results) {
        defaultContent.style.display = 'none';
        liveContainer.classList.remove('hidden');
        liveContainer.innerHTML = '';

        if (results.length === 0) {
            liveContainer.innerHTML = `<div class="text-center py-8 text-gray-400">No stops found.</div>`;
            return;
        }

        results.forEach(stop => {
            const el = document.createElement('div');
            el.className = "bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3 active:bg-gray-50 transition cursor-pointer group";
            
            // Detect Bus Color
            let badge = `<span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-bold">Bus Stop</span>`;
            
            if(stop.route_name) {
                if(stop.route_name.includes('Red')) badge = `<span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded font-bold"><i class="fas fa-bus"></i> Take Red Bus</span>`;
                if(stop.route_name.includes('Green')) badge = `<span class="text-[10px] bg-green-100 text-green-600 px-2 py-0.5 rounded font-bold"><i class="fas fa-bus"></i> Take Green Bus</span>`;
                if(stop.route_name.includes('Blue')) badge = `<span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded font-bold"><i class="fas fa-bus"></i> Take Blue Bus</span>`;
            }

            el.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center flex-shrink-0 group-hover:bg-green-50 group-hover:text-green-600 transition">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm">${stop.name}</h4>
                    <div class="mt-1">${badge}</div>
                </div>
                <div class="ml-auto text-gray-300">
                    <i class="fas fa-chevron-right text-xs"></i>
                </div>
            `;

            // Pass the route_name to the master function
            el.addEventListener('click', () => {
                focusOnLocation(parseFloat(stop.lat), parseFloat(stop.lng), stop.name, stop.route_name);
            });

            liveContainer.appendChild(el);
        });
    }
    // ============================================
    // 🔗 ROBUST DEEP LINK LISTENER (With Force Zoom Fix)
    // ============================================
    const plannerParams = new URLSearchParams(window.location.search);
    const routeParam = plannerParams.get('show_route');

    if (routeParam) {
        console.log("🚀 Trip Planner requested Route ID:", routeParam);

        // 1. Define what we are looking for
        let routeNameFragment = "";
        let busKeyword = "";

        // Check your Database IDs here.
        if (routeParam == "1") { routeNameFragment = "Green"; busKeyword = "Bus 01"; }
        else if (routeParam == "2" || routeParam == "14") { routeNameFragment = "Red"; busKeyword = "Bus 42"; }
        else if (routeParam == "3") { routeNameFragment = "Blue"; busKeyword = "Bus 10"; }
        else if (routeParam == "4") { routeNameFragment = "UV"; busKeyword = "UV Express"; }

        let attempts = 0;
        const checkDataInterval = setInterval(() => {
            attempts++;
            
            // 2. Check if the route shapes are loaded from the API
            if (typeof routeShapesData !== 'undefined' && Object.keys(routeShapesData).length > 0) {
                
                clearInterval(checkDataInterval); 
                console.log("✅ API Data Loaded.");

                // 3. Find the matching Route Name in the data
                // This looks for "PdP Green Route" if the fragment is "Green"
                const dataKey = Object.keys(routeShapesData).find(k => 
                    k.toLowerCase().includes(routeNameFragment.toLowerCase())
                );

                if (dataKey) {
                    console.log("🎯 Match Found:", dataKey);
                    
                    // A. Draw the line (Standard function)
                    if (typeof showRouteOnMap === 'function') {
                        showRouteOnMap(busKeyword);
                    }

                    // B. FORCE ZOOM (The Critical Fix)
                    const pathData = routeShapesData[dataKey].path;
                    
                    if(pathData && pathData.length > 0) {
                        console.log("🗺️ Calculating bounds for force zoom...");
                        
                        // 1. Wake up the map
                        map.invalidateSize();

                        // 2. Calculate the box around the route manually
                        const bounds = L.latLngBounds(pathData);
                        
                        // 3. Zoom with a slight delay to ensure map is ready
                        setTimeout(() => {
                            map.fitBounds(bounds, { 
                                padding: [50, 50],
                                maxZoom: 15,
                                animate: true,
                                duration: 1.0
                            });
                            console.log("✨ Zoom executed.");
                        }, 500); 
                    }
                } else {
                    console.error("❌ ERROR: Could not find route data for:", routeNameFragment);
                }

                // 4. Clean URL
                window.history.replaceState({}, document.title, window.location.pathname);
            
            } else if (attempts > 20) {
                clearInterval(checkDataInterval);
                console.error("❌ Timed out waiting for route data.");
            }
        }, 500); 
    }

    document.getElementById('route-search-input').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const items = document.querySelectorAll('#bus-list-container > div');
    
    items.forEach(item => {
        const routeName = item.querySelector('h4').innerText.toLowerCase();
        const routeNum = item.querySelector('.w-12').innerText.toLowerCase();
        
        if (routeName.includes(searchTerm) || routeNum.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

// Filter by Distance (Requires bus.distance_km in your API)
document.getElementById('filter-distance').addEventListener('click', function() {
    renderBusList('distance'); // We update the render function below
});

// Filter by Stop (Alphabetical)
document.getElementById('filter-stops').addEventListener('click', function() {
    renderBusList('stops');
});
// Wrap in a function to ensure HTML is loaded first
window.addEventListener('load', function() {
    const btnAll = document.getElementById('filter-all');
    const btnDist = document.getElementById('filter-distance');
    const btnStop = document.getElementById('filter-stops');

    // Only add the listener if the button is actually found (not null)
    if (btnAll) {
        btnAll.addEventListener('click', function() { renderBusList('all'); });
    }
    if (btnDist) {
        btnDist.addEventListener('click', function() { renderBusList('distance'); });
    }
    if (btnStop) {
        btnStop.addEventListener('click', function() { renderBusList('stops'); });
    }
});

// Ensure these run after the HTML is ready
document.addEventListener('DOMContentLoaded', function() {
    const btnAll = document.getElementById('filter-all');
    const btnDist = document.getElementById('filter-distance');
    const btnStop = document.getElementById('filter-stops');

    if (btnAll) btnAll.addEventListener('click', () => renderBusList('all'));
    if (btnDist) btnDist.addEventListener('click', () => renderBusList('distance'));
    if (btnStop) btnStop.addEventListener('click', () => renderBusList('stops'));
});

// Function to handle the button colors
function setActiveFilter(activeId) {
    const buttons = {
        'all': document.getElementById('filter-all'),
        'distance': document.getElementById('filter-distance'),
        'stops': document.getElementById('filter-stops')
    };

    // Loop through all buttons to reset them to "Inactive" style
    Object.values(buttons).forEach(btn => {
        if (!btn) return;
        btn.classList.remove('bg-[#00b894]', 'text-white', 'shadow-sm');
        btn.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200');
    });

    // Apply "Active" style to the clicked button
    const activeBtn = buttons[activeId];
    if (activeBtn) {
        activeBtn.classList.remove('bg-white', 'text-gray-600', 'border', 'border-gray-200');
        activeBtn.classList.add('bg-[#00b894]', 'text-white', 'shadow-sm');
    }
}

// Update your listeners to use this function
document.getElementById('filter-all').addEventListener('click', () => {
    setActiveFilter('all');
    renderBusList('all');
});

document.getElementById('filter-distance').addEventListener('click', () => {
    setActiveFilter('distance');
    renderBusList('distance');
});

document.getElementById('filter-stops').addEventListener('click', () => {
    setActiveFilter('stops');
    renderBusList('stops');
});

document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Parse to numbers immediately
    const focusLat = parseFloat(urlParams.get('focusLat'));
    const focusLng = parseFloat(urlParams.get('focusLng'));

    // SAFETY CHECK: Ensure coordinates are valid numbers and NOT "0" or "NaN"
    if (!isNaN(focusLat) && !isNaN(focusLng) && focusLat !== 0 && focusLng !== 0) {
        
        console.log("Flying to:", focusLat, focusLng); // Debug log

        // Use a timeout to ensure map is ready
        setTimeout(() => {
            if(window.map) {
                map.flyTo([focusLat, focusLng], 16, { animate: true, duration: 1.5 });

                L.circleMarker([focusLat, focusLng], {
                    radius: 8,
                    color: '#0A5C36',
                    fillColor: '#0A5C36',
                    fillOpacity: 0.8
                }).addTo(map).bindPopup("Destination").openPopup();
            }
        }, 800);
    } 

    // --- FEATURE: SHOW SUGGESTED STOPS IN SEARCH BAR ---
    function renderInitialSuggestions() {
        const defaultContent = document.getElementById('default-search-content');
        if(!defaultContent) return;

        // 1. Keep the "Trip Planner" button at the top
        let html = `
            <a href="/mobile/planner" class="block mb-4">
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-center gap-4 active:scale-95 transition">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-directions"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-900 text-sm">Trip Planner</h4>
                        <p class="text-xs text-blue-600">Get directions from A to B</p>
                    </div>
                    <i class="fas fa-chevron-right text-blue-300 ml-auto"></i>
                </div>
            </a>
            
            <h3 class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-3 px-1">Suggested Stops</h3>
            <div class="space-y-2 pb-10">
        `;

        // 2. Loop through allStopsData (Limit to 20 para hindi masyadong mahaba, o tanggalin ang slice kung gusto mo lahat)
        // Sort alphabetically first for better UX
        const sortedStops = allStopsData.slice().sort((a, b) => a.name.localeCompare(b.name));
        
        if (sortedStops.length === 0) {
            html += `<div class="text-center text-gray-400 text-xs py-4">Loading stops...</div>`;
        }

        sortedStops.forEach(stop => {
            // Determine Color for the Icon
            let iconColor = "text-gray-400";
            if(stop.route_name) {
                if(stop.route_name.includes('Red')) iconColor = "text-red-500";
                else if(stop.route_name.includes('Green')) iconColor = "text-green-500";
                else if(stop.route_name.includes('Blue')) iconColor = "text-blue-500";
            }

            html += `
                <div onclick="focusOnStopFromSearch(${stop.lat}, ${stop.lng})" 
                     class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3 active:bg-gray-50 transition cursor-pointer">
                    <div class="w-8 h-8 rounded-full bg-gray-50 ${iconColor} flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-map-pin text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">${stop.name}</h4>
                        <p class="text-[10px] text-gray-500">Ride: ${stop.route_name}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 ml-auto text-xs"></i>
                </div>
            `;
        });

        html += `</div>`; // Close container

        // 3. Inject into the Search Bar
        defaultContent.innerHTML = html;
    }

    // Helper Function: What happens when they click a suggestion
    window.focusOnStopFromSearch = function(lat, lng) {
        // Close Search Overlay
        document.getElementById('search-overlay').classList.add('hidden');
        document.getElementById('search-overlay').classList.remove('flex');
        
        // Fly to Location
        map.flyTo([lat, lng], 16, { animate: true, duration: 1.0 });

        // Try to open the popup for this stop
        // We find the marker in the stopsLayer
        stopsLayer.eachLayer(function(layer) {
            const lLat = layer.getLatLng().lat;
            const lLng = layer.getLatLng().lng;
            // Simple proximity check (since floats can be slightly different)
            if (Math.abs(lLat - lat) < 0.0001 && Math.abs(lLng - lng) < 0.0001) {
                layer.openPopup();
            }
        });
    };
});

// ============================================
    // 🛑 TOGGLE STOPS LOGIC (I-PASTE MO DITO SA DULO)
    // ============================================
    
    // 1. Hanapin ang button na ginawa natin
    const btnToggleStops = document.getElementById('btn-toggle-stops');
    
    // 2. Gumawa ng "Switch" (Default is TRUE/NAKA-ON)
    let areStopsVisible = true; 

    // 3. Makinig kapag pinindot ang button
    if (btnToggleStops) { // Safety check kung nag-exist ang button
        btnToggleStops.addEventListener('click', () => {
            
            if (areStopsVisible) {
                // --- KUNG NAKA-ON, PATAYIN NATIN ---
                
                // A. Tanggalin ang stops sa mapa
                map.removeLayer(stopLayer);
                areStopsVisible = false;
                
                // B. Gawing GRAY ang button (Visual Feedback)
                btnToggleStops.classList.remove('text-green-600', 'bg-white');
                btnToggleStops.classList.add('text-gray-400', 'bg-gray-100');
                
                // C. Magpakita ng message
                if(typeof showToast === 'function') showToast("Map Settings", "Bus stops hidden.");
                
            } else {
                // --- KUNG NAKA-OFF, BUKSAN NATIN ---

                // A. Ibalik ang stops sa mapa
                map.addLayer(stopLayer);
                areStopsVisible = true;

                // B. Gawing GREEN ang button (Visual Feedback)
                btnToggleStops.classList.remove('text-gray-400', 'bg-gray-100');
                btnToggleStops.classList.add('text-green-600', 'bg-white');
                
                // C. Magpakita ng message
                if(typeof showToast === 'function') showToast("Map Settings", "Bus stops visible.");
            }
        });
    }
</script>
</body>
</html> 