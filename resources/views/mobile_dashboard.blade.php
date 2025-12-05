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

    <div id="search-results" class="flex-grow overflow-y-auto p-4">
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
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-1">Suggested Places</h3>

            <div class="flex flex-wrap gap-2 mb-6">
                <button class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-600 shadow-sm hover:border-green-500 hover:text-green-600 transition">🛍️ Malls</button>
                <button class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-600 shadow-sm hover:border-green-500 hover:text-green-600 transition">🎓 Schools</button>
                <button class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-600 shadow-sm hover:border-green-500 hover:text-green-600 transition">🚌 Terminals</button>
            </div>

            <div class="text-center mt-12 opacity-50">
                <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 text-sm">Type above to find your bus.</p>
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

<div class="absolute top-28 right-4 z-40">
    <button id="btn-notifications" class="w-11 h-11 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:text-green-600 active:scale-95 transition relative">
        <i class="fas fa-bell"></i>
        <div class="absolute top-2 right-3 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></div>
    </button>
</div>
    <div class="absolute right-4 top-40 z-40 flex flex-col gap-3 transition-all">
        
        <a href="{{ route('mobile.nearby') }}" class="glass-panel w-11 h-11 rounded-full shadow-md flex items-center justify-center text-green-600 hover:text-green-700 active:scale-95 transition bg-white mb-2">
            <i class="fas fa-map-marker-alt text-lg"></i>
        </a>

        <button id="zoom-in" class="glass-panel w-11 h-11 rounded-full shadow-md flex items-center justify-center text-gray-600 hover:text-green-600 active:scale-95 transition bg-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>
        
        <button id="zoom-out" class="glass-panel w-11 h-11 rounded-full shadow-md flex items-center justify-center text-gray-600 hover:text-green-600 active:scale-95 transition bg-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
            </svg>
        </button>
    </div>

    <div id="bus-list-panel" class="fixed inset-x-0 bottom-0 z-[80] bg-gray-50 rounded-t-[30px] shadow-[0_-10px_40px_rgba(0,0,0,0.2)] transform translate-y-full transition-transform duration-300 ease-out flex flex-col h-[80vh]">
    
    <div class="w-full flex justify-center pt-3 pb-1 bg-white rounded-t-[30px]" id="close-bus-list">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
    </div>

    <div class="px-6 py-4 bg-white border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">All Routes</h2>

        <div class="relative mb-4">
            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" placeholder="Search routes" class="w-full bg-gray-100 text-gray-800 rounded-xl py-3 pl-10 pr-4 outline-none focus:ring-2 focus:ring-green-500 transition">
        </div>

        <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
            <button class="px-4 py-2 bg-[#00b894] text-white text-sm font-medium rounded-lg shadow-sm whitespace-nowrap">
                <i class="fas fa-filter mr-1"></i> All Routes
            </button>
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg whitespace-nowrap hover:border-green-500 transition">
                By Distance
            </button>
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg whitespace-nowrap hover:border-green-500 transition">
                By Stop
            </button>
        </div>
    </div>

    <div id="bus-list-container" class="flex-grow overflow-y-auto p-4 space-y-3">
        <div class="text-center py-10 text-gray-400">Loading routes...</div>
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

    <div class="flex-grow overflow-y-auto p-4 space-y-6 pb-24">
        
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

    <div class="fixed inset-x-0 bottom-0 z-[60] pointer-events-none flex flex-col justify-end h-full mobile-frame">
        
        <div class="sidebar-container">
            <div class="desktop-only p-6 pb-2 bg-white">
                <h2 class="text-2xl font-bold text-gray-800">Capiz Operations</h2>
                <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-green-500">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                    </svg>
                    <p>Last Updated: <span id="desktop-clock" class="font-medium text-gray-700">10:24:30 AM PHST</span></p>
                </div>
            </div>

            <div class="pointer-events-auto p-4 md:p-6 md:pt-4 md:flex-grow">
                <div class="glass-panel bg-white/95 rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] overflow-hidden transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.16)]">
                    
                    <div id="trip-card-header" class="bg-gradient-to-r from-[#00b06b] to-[#009e60] p-5 text-white cursor-pointer relative overflow-hidden transition-colors duration-300">
    <div class="relative flex justify-between items-start">
        <div class="flex gap-4">
            <div class="bg-white/20 p-2.5 rounded-2xl backdrop-blur-md shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.129a48.97 48.97 0 00-7.53 0c-.565.08-.987.56-.987 1.13v.958m3.75 0v-1.5a1.5 1.5 0 00-3 0v1.5m0 0h7.5" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span id="card-route-name" class="text-[11px] font-bold bg-green-800/30 px-2 py-0.5 rounded-full uppercase tracking-wider">Express Route</span>
                    <div class="flex items-center gap-0.5 text-yellow-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.651l-4.752-.381-1.831-4.401z" clip-rule="evenodd" /></svg>
                        <span class="text-sm font-bold ml-0.5 text-white">4.8</span>
                    </div>
                </div>
                <h3 id="card-bus-number" class="text-xl font-bold leading-tight">Bus 42 - Downtown</h3>
            </div>
        </div>
    </div>
</div>

<div class="p-6">
    <div class="flex items-start gap-4 mb-6">
        <div class="flex-shrink-0 relative">
            <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
            </div>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Next Stop</p>
            <h4 id="card-next-stop" class="text-gray-900 font-bold text-lg leading-tight">Roxas City Plaza</h4>
            <p class="text-gray-500 text-sm">Arnaldo Blvd</p>
        </div>
    </div>

    <div class="flex items-start gap-4 mb-2">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">Estimated Arrival</p>
            <div class="flex items-baseline gap-1.5">
                <span id="card-eta-time" class="text-3xl font-black text-gray-900">3</span>
                <span class="text-gray-600 font-medium">min</span>
            </div>
        </div>
    </div>
</div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                         <div class="flex justify-between text-[11px] text-gray-500 mb-2 font-semibold uppercase tracking-wider">
                            <span>Route Progress</span>
                            <span>65%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-[#00b06b] h-full rounded-full relative" style="width: 65%"></div>
                        </div>
                        <div class="flex justify-between text-[11px] font-medium text-gray-400 mt-2">
                             <span>Pueblo de Panay</span>
                             <span>City Proper</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pointer-events-auto bg-white md:bg-transparent border-t border-gray-100 md:border-none px-8 py-4 md:py-6 md:pb-8 flex justify-between items-center rounded-t-[30px] md:rounded-none shadow-[0_-10px_30px_rgba(0,0,0,0.03)] md:shadow-none">
                <a href="#" class="flex flex-col items-center gap-1.5 text-green-600 group">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 transition group-hover:scale-110"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" /><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" /></svg>
                    <span class="text-[11px] font-bold">Home</span>
                </a>
                
                <a href="#" id="nav-buses" class="flex flex-col items-center gap-1.5 text-gray-400 hover:text-green-600 transition group">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 transition group-hover:scale-110">
        <path d="M4.5 6.375a4.125 4.125 0 118.25 0 1.125 1.125 0 11-2.25 0 3 3 0 00-6 0l.991 2.786a1.49 1.49 0 01.304.568l2.667 9.335a.75.75 0 11-1.442.412l-2.567-8.988a5.25 5.25 0 01-1.173.427l-1.37 4.792a.75.75 0 01-1.442-.412l1.957-6.849c.156-.547.422-1.053.778-1.486L4.5 6.375z" />
        <path d="M14.894 14.418l-2.205-3.15a.75.75 0 111.229-.858l2.613 3.733a.75.75 0 11-1.23.861l-.407-.586zM13.669 17.866l-2.18-2.18a.75.75 0 111.06-1.06l2.18 2.18a.75.75 0 11-1.06 1.06z" />
        <path d="M21.75 4.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM22.5 12a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM21.75 19.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
    </svg>
    <span class="text-[11px] font-medium">Buses</span>
</a>

                <a href="{{ route('mobile.profile') }}" class="flex flex-col items-center gap-1.5 text-gray-400 hover:text-green-600 transition group">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 transition group-hover:scale-110">
        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
    </svg>
    <span class="text-[11px] font-medium">Profile</span>
</a>
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
  // --- 1. SETUP THE MAP ---
  var map = L.map('map', {
    zoomControl: false, 
    attributionControl: false 
  }).setView([11.5853, 122.7511], 14); // Zoomed out slightly to see all 3 buses

  // NEW: Store the ID of the bus we are following
  let trackedBusId = null;

  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    maxZoom: 20
  }).addTo(map);

  // --- 2. LIVE TRACKING LOGIC ---
  var busMarkers = {}; // Object to store our markers: { bus_id: marker_object }

  // --- 2. LIVE TRACKING LOGIC (Updated to make the Card Dynamic) ---
  var busMarkers = {}; 

  // --- DRAW ROUTE LINES ---
  function drawRouteLines() {
      fetch('/api/routes/shapes')
          .then(res => res.json())
          .then(routes => {
              routes.forEach(route => {
                  if (route.path_data) {
                      // Parse the JSON string into an array
                      const coordinates = JSON.parse(route.path_data);
                      
                      // Draw the line
                      const polyline = L.polyline(coordinates, {
                          color: route.color,   // Use the color from DB
                          weight: 5,            // Thickness
                          opacity: 0.7,         // Slight transparency looks modern
                          lineCap: 'round'      // Smooth ends
                      }).addTo(map);

                      // Optional: Click to see name
                      polyline.bindPopup(`<b>${route.name}</b>`);
                  }
              });
          })
          .catch(err => console.error("Error drawing routes:", err));
  }

  // Call it ONCE when map loads
  drawRouteLines();

  // --- 2. LIVE TRACKING LOGIC (Updated for Auto-Follow) ---
  var busMarkers = {}; 

  function fetchBusPositions() {
      fetch('/api/live-locations')
          .then(response => response.json())
          .then(data => {
              data.forEach(bus => {
                  var lat = parseFloat(bus.lat);
                  var lng = parseFloat(bus.lng);
                  var isReal = bus.bus_number.includes("REAL");
                  var iconColor = isReal ? '#00b894' : '#636e72'; 

                  // 1. UPDATE EXISTING MARKER
                  if (busMarkers[bus.bus_id]) {
                      var existingMarker = busMarkers[bus.bus_id];
                      existingMarker.setLatLng([lat, lng]);

                      // --- AUTO-FOLLOW LOGIC ---
                      // If this is the bus we are tracking, move the camera!
                      if (trackedBusId === bus.bus_id) {
                          map.panTo([lat, lng], { animate: true, duration: 1.0 });
                      }
                  } 
                  // 2. CREATE NEW MARKER
                  else {
                      var busIcon = L.divIcon({
                          className: 'custom-bus-marker',
                          html: `<div style="background-color: ${iconColor}; width: 35px; height: 35px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; transition: all 0.5s ease;"><i class="fas fa-bus" style="font-size: 16px;"></i></div>`,
                          iconSize: [35, 35],
                          iconAnchor: [17, 17]
                      });

                      var newMarker = L.marker([lat, lng], {icon: busIcon}).addTo(map);

                      // Click to select bus
                      newMarker.on('click', function() {
                          updateTripCard(bus); 
                          // NOTE: We don't auto-track on simple click, only when they hit "Track Live"
                      });
                      
                      busMarkers[bus.bus_id] = newMarker;
                  }
              });
          })
          .catch(error => console.error('Error fetching bus data:', error));
  }

  // --- 3. FUNCTION TO UPDATE THE CARD ---
  function updateTripCard(bus) {

      // 1. Update Texts
      document.getElementById('card-bus-number').innerText = bus.bus_number;
      document.getElementById('card-route-name').innerText = bus.status === 'active' ? 'Active Route' : 'Offline';
      
      // 2. Simulate "Next Stop" data (since we don't have a route algorithm yet)
      // For now, we just switch the text based on the Bus ID to make it feel real
      if(bus.bus_number.includes("REAL")) {
          document.getElementById('card-next-stop').innerText = "Roxas City Plaza";
          document.getElementById('card-eta-time').innerText = "2";
          document.getElementById('trip-card-header').classList.remove('from-gray-500', 'to-gray-600');
          document.getElementById('trip-card-header').classList.add('from-[#00b06b]', 'to-[#009e60]'); // Green
      } else {
          document.getElementById('card-next-stop').innerText = "Transport Terminal";
          document.getElementById('card-eta-time').innerText = "15";
          // Make header gray for Ghost buses to show difference
          document.getElementById('trip-card-header').classList.remove('from-[#00b06b]', 'to-[#009e60]');
          document.getElementById('trip-card-header').classList.add('from-gray-500', 'to-gray-600'); 
      }

      let load = bus.current_load || 0;
      let max = bus.max_capacity || 40;
      let percent = Math.round((load / max) * 100);

      document.getElementById('card-bus-number').innerHTML = `
          ${bus.bus_number} 
          <span class="text-xs font-normal ml-2 opacity-80 block md:inline">
            <i class="fas fa-users"></i> ${load}/${max} passengers
          </span>
      `;
  }

  // Run immediately, then every 2 seconds
  fetchBusPositions();
  setInterval(fetchBusPositions, 2000);


  // --- 3. UI CONTROLS (Search, Zoom, Clock) ---
  
  // Connect Zoom Buttons
  document.getElementById('zoom-in').addEventListener('click', function() { map.zoomIn(); });
  document.getElementById('zoom-out').addEventListener('click', function() { map.zoomOut(); });

  // Connect Search Bar
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

  // Live Clock
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

  // Open Panel
  navBuses.addEventListener('click', (e) => {
      e.preventDefault(); // Prevent jump to top
      busListPanel.classList.remove('translate-y-full'); // Slide Up
      renderBusList(); // Refresh list data
  });

  // Close Panel (Clicking the gray handle or the map)
  closeBusList.addEventListener('click', () => {
      busListPanel.classList.add('translate-y-full'); // Slide Down
  });
  
  // Close when clicking the map (optional UX improvement)
  map.on('click', () => {
       busListPanel.classList.add('translate-y-full');
  });

  // --- 5. BUS DETAIL PANEL LOGIC ---
  const detailPanel = document.getElementById('bus-detail-panel');
  const closeDetailBtn = document.getElementById('close-detail-panel');
  const trackLiveBtn = document.getElementById('btn-track-live');
  const stopsContainer = document.getElementById('stops-timeline');
  let selectedBusData = null; // Store data to use later

  // Close Detail Panel (Back Button)
  closeDetailBtn.addEventListener('click', () => {
      detailPanel.classList.add('translate-x-full'); // Slide away
  });

  // "Track Bus Live" Button Action (Updated)
  trackLiveBtn.addEventListener('click', () => {
      detailPanel.classList.add('translate-x-full'); // Hide detail
      busListPanel.classList.add('translate-y-full'); // Hide list
      
      if(selectedBusData) {
          // 1. Set the variable to lock the camera
          trackedBusId = selectedBusData.bus_id; 
          
          // 2. Immediate zoom to target
          map.flyTo([selectedBusData.lat, selectedBusData.lng], 18, { duration: 1.5 });
          updateTripCard(selectedBusData);
          
          // 3. Show a temporary message (Optional but nice)
          alert("Tracking " + selectedBusData.bus_number); 
      }
  });

  // Stop following if user drags the map manually
  map.on('dragstart', function() {
      if (trackedBusId) {
          trackedBusId = null; // Clear the variable
          console.log("Auto-follow stopped by user");
      }
  });

  // FUNCTION: Generate Dummy Stops for the Timeline
  function generateStops(busNumber) {
      // Fake data just for the visuals
      const stops = [
          { name: "Main Terminal", status: "Departed", color: "bg-gray-300" },
          { name: "City Hall", status: "Passed", color: "bg-gray-300" },
          { name: "Roxas City Plaza", status: "Current Stop", color: "bg-green-500", active: true },
          { name: "Gaisano Mall", status: "5 min", color: "bg-gray-200" },
          { name: "St. Anthony Hospital", status: "12 min", color: "bg-gray-200" },
          { name: "Airport Terminal", status: "20 min", color: "bg-gray-200" }
      ];

      let html = '';
      stops.forEach((stop, index) => {
          const isLast = index === stops.length - 1;
          const lineClass = isLast ? '' : 'h-full w-0.5 bg-gray-200 absolute left-[7px] top-4';
          
          html += `
            <div class="relative flex gap-4 pb-8 last:pb-0">
                <div class="${lineClass}"></div>
                
                <div class="relative z-10 w-4 h-4 rounded-full ${stop.active ? 'bg-green-500 ring-4 ring-green-100' : 'bg-gray-300'} flex-shrink-0 mt-1"></div>
                
                <div class="flex-grow flex justify-between items-start -mt-0.5">
                    <div>
                        <h4 class="${stop.active ? 'text-green-700 font-bold' : 'text-gray-600 font-medium'} text-sm">${stop.name}</h4>
                        <p class="text-xs text-gray-400">Stop #${2800 + index}</p>
                        ${stop.active ? '<span class="inline-block mt-1 bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full">Current Stop</span>' : ''}
                    </div>
                    <span class="text-xs ${stop.active ? 'text-green-600 font-bold' : 'text-gray-400'}">${stop.status}</span>
                </div>
            </div>
          `;
      });
      stopsContainer.innerHTML = html;
  }

  // Function to Render the List (Updated for "Card" Design)
  function renderBusList() {
      fetch('/api/live-locations')
          .then(res => res.json())
          .then(data => {
              busListContainer.innerHTML = ''; // Clear loading text

              data.forEach(bus => {
                  const isReal = bus.bus_number.includes("REAL");
                  
                  // Extract just the number for the green box (e.g. "42" from "Bus 42")
                  // If no number found, default to "?"
                  let routeNum = bus.bus_number.match(/\d+/) ? bus.bus_number.match(/\d+/)[0] : '?';
                  
                  // Clean up the name for display
                  let displayName = bus.bus_number.replace('(REAL)', '').replace('(GHOST)', '').trim();
                  // Add specific names to make it look like the image
                  if(routeNum === '42') displayName = "Downtown Express";
                  if(routeNum === '10') displayName = "North Loop";
                  if(routeNum === '01') displayName = "Airport Express";

                  // Status Logic
                  let statusText = "On Time";
                  let statusColor = "bg-green-100 text-green-700";
                  let statusIcon = "fa-bus";

                  // CAPACITY LOGIC
                  let load = bus.current_load || 0;
                  let max = bus.max_capacity || 40;
                  let percent = Math.round((load / max) * 100);
                  
                  let capacityColor = "bg-green-100 text-green-700";
                  let capacityText = "Seats Available";
                  
                  if(percent > 50) { capacityColor = "bg-yellow-100 text-yellow-700"; capacityText = "Standing Room"; }
                  if(percent >= 90) { capacityColor = "bg-red-100 text-red-700"; capacityText = "Full"; }

                  if(!isReal) {
                      // Randomize statuses for ghosts to look interesting
                      if(routeNum === '10') { statusText = "Delayed"; statusColor = "bg-orange-100 text-orange-700"; }
                  }

                  const item = document.createElement('div');
                  item.className = "bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between cursor-pointer hover:shadow-md transition";
                  
                  item.innerHTML = `
                      <div class="flex items-center gap-4">
                          <div class="w-12 h-12 rounded-xl bg-[#00b894] text-white flex items-center justify-center font-bold text-lg shadow-sm">
                              ${routeNum}
                          </div>
                          <div>
                              <h4 class="font-bold text-gray-800 text-[15px]">${displayName}</h4>
                              <div class="flex items-center gap-2 mt-1">
                                  <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide border border-gray-100 ${statusColor}">
                                      ${statusText}
                                  </span>
                                  <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide border border-gray-100 ${capacityColor}">
                                      <i class="fas fa-users mr-1"></i> ${percent}%
                                  </span>
                              </div>
                          </div>
                      </div>
                      <div class="text-gray-300">
                          <i class="fas fa-chevron-right"></i>
                      </div>
                  `;

                  // Click Event: Go to Bus
                  item.addEventListener('click', () => {
      selectedBusData = bus; // Save for later
      document.getElementById('detail-title').innerText = displayName + " Details"; // Set Title
      generateStops(bus.bus_number); // Build the timeline
      
      detailPanel.classList.remove('translate-x-full'); // Show the panel

      updateHeartButton(bus.bus_id);
  });

                  busListContainer.appendChild(item);
              });
          });
  }

  // --- 6. NOTIFICATION SYSTEM ---
  const btnNotifications = document.getElementById('btn-notifications');
  const alertsPanel = document.getElementById('alerts-panel');
  const alertsCard = document.getElementById('alerts-card');
  const closeAlerts = document.getElementById('close-alerts');

  // Open Alerts
  btnNotifications.addEventListener('click', () => {
      alertsPanel.classList.remove('hidden');
      // Small timeout to allow the transition to happen
      setTimeout(() => {
          alertsCard.classList.remove('scale-95', 'opacity-0');
          alertsCard.classList.add('scale-100', 'opacity-100');
      }, 10);
  });

  // Close Alerts
  function hideAlerts() {
      alertsCard.classList.remove('scale-100', 'opacity-100');
      alertsCard.classList.add('scale-95', 'opacity-0');
      
      setTimeout(() => {
          alertsPanel.classList.add('hidden');
      }, 300);
  }

  closeAlerts.addEventListener('click', hideAlerts);
  
  // Close when clicking outside the card
  alertsPanel.addEventListener('click', (e) => {
      if (e.target === alertsPanel) hideAlerts();
  });

  // --- 7. FAVORITES LOGIC ---
  const btnFavorite = document.getElementById('btn-favorite');
  let currentFavorites = []; // Store IDs here

  // 1. Load Favorites on Start
  function loadFavorites() {
      fetch('/api/favorites/ids')
          .then(res => res.json())
          .then(ids => {
              currentFavorites = ids;
          });
  }
  loadFavorites(); // Call immediately

  // 2. Check State when opening details
  function updateHeartButton(busId) {
      if (currentFavorites.includes(busId)) {
          btnFavorite.classList.remove('text-gray-400', 'bg-white');
          btnFavorite.classList.add('text-red-500', 'bg-red-50', 'border-red-200');
          btnFavorite.innerHTML = '<i class="fas fa-heart"></i>'; // Solid/Red
      } else {
          btnFavorite.classList.add('text-gray-400', 'bg-white');
          btnFavorite.classList.remove('text-red-500', 'bg-red-50', 'border-red-200');
          btnFavorite.innerHTML = '<i class="far fa-heart"></i>'; // Outline/Gray
      }
  }

  // 3. Toggle Click
  btnFavorite.addEventListener('click', () => {
      if (!selectedBusData) return;

      const busId = selectedBusData.bus_id;

      // Optimistic UI Update (Change color immediately)
      const isFav = currentFavorites.includes(busId);
      if (isFav) {
          currentFavorites = currentFavorites.filter(id => id !== busId); // Remove locally
      } else {
          currentFavorites.push(busId); // Add locally
      }
      updateHeartButton(busId); // Update color

      // Send to API
      fetch('/api/favorites/toggle', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}' // Important for Laravel!
          },
          body: JSON.stringify({ bus_id: busId })
      });
  });

  // --- UPDATE RENDER LIST TO CHECK HEART ---
  // You need to call updateHeartButton() when you open the panel.
  // Go back to your 'item.addEventListener' inside renderBusList()
  // Add this line: updateHeartButton(bus.bus_id);

</script>
</body>
</html>