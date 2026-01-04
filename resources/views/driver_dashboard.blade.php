<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - Bus {{ $bus->bus_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white h-screen flex flex-col">

    <div class="p-4 bg-gray-800 flex justify-between items-center shadow-lg">
        <div>
            <h1 class="text-2xl font-bold text-blue-500">Bus {{ $bus->bus_number }}</h1>
            <p class="text-sm text-gray-400">Driver Mode</p>
        </div>
        <div id="status-badge" class="px-3 py-1 rounded-full text-sm font-bold bg-green-500 text-white uppercase">
            {{ $bus->status }}
        </div>
    </div>

    <div class="flex-grow flex flex-col items-center justify-center space-y-8">
        <h2 class="text-gray-400 text-lg uppercase tracking-widest">Passengers On Board</h2>
        
        <div id="count-display" class="text-9xl font-mono font-bold text-white">
            {{ $bus->passenger_count }}
        </div>

        <div class="flex space-x-6 w-full max-w-md px-6">
            <button onclick="update('minus')" class="flex-1 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-xl p-6 shadow-lg transform active:scale-95 transition">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
            </button>
            
            <button onclick="update('add')" class="flex-1 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-xl p-6 shadow-lg transform active:scale-95 transition">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>
    </div>

    <div class="p-6 bg-gray-800 grid grid-cols-2 gap-4">
        <button onclick="setStatus('active')" class="p-3 rounded-lg border border-green-500 text-green-500 hover:bg-green-900">Active</button>
        <button onclick="setStatus('full')" class="p-3 rounded-lg border border-yellow-500 text-yellow-500 hover:bg-yellow-900">Bus Full</button>
        <button onclick="setStatus('break')" class="p-3 rounded-lg border border-blue-500 text-blue-500 hover:bg-blue-900">On Break</button>
        <button onclick="setStatus('maintenance')" class="p-3 rounded-lg border border-red-500 text-red-500 hover:bg-red-900">Emergency</button>
    </div>

    <script>
        const busId = "{{ $bus->id }}";
        const updateUrl = "/driver/update/" + busId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 1. Update Passenger Count (+ or -)
        function update(action) {
            // Optimistic UI update (update number immediately before server replies)
            let display = document.getElementById('count-display');
            let current = parseInt(display.innerText);
            
            if (action === 'add') display.innerText = current + 1;
            if (action === 'minus' && current > 0) display.innerText = current - 1;

            // Send to Server
            fetch(updateUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ action: action })
            });
        }

        // 2. Update Bus Status
        function setStatus(status) {
            // Update the badge color/text immediately
            let badge = document.getElementById('status-badge');
            badge.innerText = status;
            
            // Send to Server
            fetch(updateUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ action: 'status_change', new_status: status })
            });
        }
    </script>
</body>
</html>