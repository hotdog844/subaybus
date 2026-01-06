<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Hub - SubayBus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #111827; color: white; }</style>
</head>
<body class="min-h-screen flex flex-col bg-gray-900">

    <div class="bg-gray-800 p-6 pb-12 rounded-b-[40px] shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600 rounded-full mix-blend-overlay filter blur-3xl opacity-20"></div>
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <p class="text-gray-400 text-xs uppercase tracking-wider font-bold">Welcome back,</p>
                <h1 class="text-2xl font-extrabold text-white">{{ Auth::user()->name ?? 'Driver' }}</h1>
            </div>
            <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center border border-gray-600">
                <i class="fas fa-id-card text-blue-400"></i>
            </div>
        </div>
    </div>

    <div class="flex-grow px-6 -mt-8 relative z-20">
        
        <div class="bg-gray-800 border border-gray-700 rounded-3xl shadow-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-bus text-blue-500"></i> Select Bus Unit
            </h2>
            
            @if($buses->count() > 0)
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1 block">Available Buses</label>
                        <div class="relative">
                            <select id="bus-select" class="w-full bg-gray-900 border border-gray-600 text-white text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-4 appearance-none font-bold">
                                <option value="" disabled selected>-- Choose a Bus --</option>
                                @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}">
                                        {{ $bus->bus_number }} - {{ $bus->route ? $bus->route->name : 'No Route' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <button onclick="startShift()" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/30 transition-all flex justify-center items-center gap-2 active:scale-95">
                        <span>Start Shift</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 bg-gray-900 rounded-2xl border border-dashed border-gray-700">
                    <i class="fas fa-ban text-2xl mb-2"></i>
                    <p class="text-sm">No buses available.</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-700 text-center">
                <span class="block text-2xl font-black text-white">0</span>
                <span class="text-[10px] text-gray-400 uppercase font-bold">Hours Today</span>
            </div>
            <div class="bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-700 text-center">
                <span class="block text-2xl font-black text-green-400">Active</span>
                <span class="text-[10px] text-gray-400 uppercase font-bold">Account Status</span>
            </div>
        </div>
    </div>

    <div class="p-6 mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-gray-800 border border-red-900/30 text-red-400 font-bold py-4 rounded-2xl shadow-sm hover:bg-red-900/20 transition flex items-center justify-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </button>
        </form>
    </div>

    <script>
        function startShift() {
            // 1. Find the dropdown box
            const selectBox = document.getElementById('bus-select');
            
            // 2. Get the value (Bus ID)
            const busId = selectBox.value;
            
            // 3. Check if they actually picked something
            if (!busId) {
                alert("Please select a bus unit first!");
                return;
            }

            // 4. Go to the dashboard (This mimics typing the URL manually)
            // It sends them to: /driver/dashboard/{id}
            window.location.href = "/driver/dashboard/" + busId;
        }
    </script>

</body>
</html>