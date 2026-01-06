<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Route Planner - SubayBus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f4f6f8; }</style>
</head>
<body class="bg-gray-50 h-screen flex flex-col">

    <div class="bg-white px-6 py-6 shadow-sm pb-8 rounded-b-[30px] z-20">
        
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('mobile.dashboard') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-green-50 hover:text-green-600 transition">
                <i class="fas fa-times"></i>
            </a>
            <h1 class="text-lg font-bold text-gray-800">Plan a Trip</h1>
            <div class="w-10"></div> </div>

        <div class="flex gap-3">
            <div class="flex flex-col items-center pt-3 gap-1">
                <div class="w-3 h-3 rounded-full border-2 border-gray-400"></div> <div class="w-0.5 h-10 bg-gray-200 border-l border-dashed border-gray-300"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div> </div>

            <div class="flex-grow space-y-3">
                <div class="relative">
                    <input type="text" id="input-from" value="Current Location" class="w-full bg-gray-50 text-gray-800 font-medium rounded-xl py-3 px-4 outline-none border border-transparent focus:border-green-500 focus:bg-white transition text-sm">
                </div>
                <div class="relative">
                    <input type="text" id="input-to" placeholder="Where to?" class="w-full bg-gray-50 text-gray-800 font-medium rounded-xl py-3 px-4 outline-none border border-transparent focus:border-green-500 focus:bg-white transition text-sm autofocus">
                </div>
            </div>

            <button class="flex flex-col justify-center text-gray-400 hover:text-green-600">
                <i class="fas fa-exchange-alt rotate-90"></i>
            </button>
        </div>
    </div>

    <div class="flex-grow overflow-y-auto p-6 space-y-4" id="results-container">
        
        <div class="text-center py-10 opacity-50 hidden" id="empty-state">
            <i class="fas fa-route text-4xl text-gray-300 mb-2"></i>
            <p class="text-gray-500 text-sm">Enter a destination to see routes.</p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-green-500 relative overflow-hidden cursor-pointer active:scale-95 transition group">
            <div class="absolute top-0 right-0 bg-green-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl">BEST OPTION</div>
            
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-[#00b894] text-white flex items-center justify-center font-bold text-lg shadow-sm">
                        42
                    </div>
                    <div id="results-container" class="mt-4">
    <p class="text-center text-gray-400">Click "Find Routes" to see available buses.</p>
</div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-black text-gray-800">15 <span class="text-xs font-medium text-gray-400">min</span></p>
                    <p class="text-xs text-gray-400">₱ 25.00</p>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-gray-400 mt-2">
                <span class="font-medium text-gray-600">10:30 AM</span>
                <div class="h-0.5 flex-grow bg-gray-100 mx-3 relative">
                    <div class="absolute left-0 w-1/3 h-full bg-green-200"></div> <div class="absolute left-1/3 w-2/3 h-full bg-green-500"></div> </div>
                <span class="font-medium text-gray-600">10:45 AM</span>
            </div>
            
            <div class="mt-3 flex gap-2 text-[10px] text-gray-500">
                <span class="flex items-center"><i class="fas fa-walking mr-1"></i> 2 min</span>
                <span class="flex items-center"><i class="fas fa-bus mr-1"></i> 13 min</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 cursor-pointer active:scale-95 transition">
            <div class="flex justify-between items-start mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-500 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                        18
                    </div>
                    <div id="results-container" class="mt-4">
    <p class="text-center text-gray-400">Click "Find Routes" to see available buses.</p>
</div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-gray-800">35 <span class="text-xs font-medium text-gray-400">min</span></p>
                    <p class="text-xs text-gray-400">₱ 20.00</p>
                </div>
            </div>
        </div>

    </div>

    <div class="p-6 bg-white border-t border-gray-100">
        <button id="btn-find-routes" class="w-full bg-gray-800 text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-black transition flex items-center justify-center gap-2">
            <i class="fas fa-search-location"></i> Find Routes
        </button>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    
    const findBtn = document.getElementById('btn-find-routes');
    // NOTE: In your HTML, you used id="results-container" multiple times. 
    // This script grabs the FIRST one (the main wrapper), which is what we want.
    const resultsDiv = document.getElementById('results-container');

    findBtn.addEventListener('click', function() {
        // 1. Show loading state
        findBtn.innerText = "Searching...";
        findBtn.disabled = true;
        resultsDiv.innerHTML = '<p class="text-center text-gray-500 py-10">Looking for buses...</p>';

        // 2. DEFENSE COORDINATES (Hardcoded for the Demo)
        const startLat = 11.55985; 
        const startLng = 122.75145;
        const destLat = 11.57760; 
        const destLng = 122.75660;

        // 3. Call your API
        fetch(`/api/plan-trip?start_lat=${startLat}&start_lng=${startLng}&dest_lat=${destLat}&dest_lng=${destLng}`)
            .then(response => response.json())
            .then(data => {
                findBtn.innerText = "Find Routes";
                findBtn.disabled = false;

                if (data.success) {
                    let html = '';
                    
                    data.data.forEach(route => {
                        // --- FIX: I ADDED THE CONTENT BACK INSIDE THE CARD ---
                        html += `
                        <div onclick="window.location.href='{{ route('mobile.dashboard') }}?show_route=${route.route_id}'"
                             class="bg-white p-5 rounded-2xl shadow-sm mb-4 border-l-4 hover:bg-gray-50 transition cursor-pointer relative overflow-hidden group" 
                             style="border-left-color: ${route.color};">
                             
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl text-white flex items-center justify-center font-bold text-lg shadow-sm" style="background-color: ${route.color}">
                                        ${route.route_id}
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-lg leading-tight">${route.name}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">From: ${route.start_stop}</p>
                                        <p class="text-xs text-gray-500">To: ${route.end_stop}</p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-xl font-black text-gray-800">
                                        <span class="text-xs font-medium text-gray-400 align-top">₱</span>${route.price}
                                    </div>
                                    <div class="text-sm text-green-600 font-bold">${route.eta}</div>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Recommended</span>
                                <span class="text-xs text-blue-500 font-bold flex items-center group-hover:underline">
                                    View on Map <i class="fas fa-chevron-right ml-1 text-[10px]"></i>
                                </span>
                            </div>
                        </div>
                        `;
                    });

                    resultsDiv.innerHTML = html;
                } else {
                    resultsDiv.innerHTML = `<p class="text-center text-red-500 py-10">${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                findBtn.innerText = "Find Routes";
                findBtn.disabled = false;
                resultsDiv.innerHTML = '<p class="text-center text-red-500 py-10">System Error. Check console.</p>';
            });
    });
});
</script>
</body>
</html>

