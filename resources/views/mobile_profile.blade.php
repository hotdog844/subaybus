<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Profile - SubayBus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f4f6f8; }</style>
</head>
<body class="bg-[#f4f6f8] h-screen flex flex-col relative">

    <div class="bg-[#00b894] h-40 w-full rounded-b-[40px] absolute top-0 z-0"></div>

    <div class="relative z-10 flex flex-col flex-grow px-6 pt-10 pb-24 overflow-y-auto no-scrollbar">
        
        <div class="bg-white rounded-3xl shadow-lg p-6 mb-6 text-center">
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-[#00b894] text-white flex items-center justify-center text-2xl font-bold mb-3 border-4 border-white shadow-sm uppercase">
                    {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                </div>
                <h2 class="text-xl font-bold text-gray-800">
                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                </h2>
                <p class="text-sm text-gray-400 mb-2">{{ Auth::user()->email }}</p>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-600 uppercase tracking-widest border border-green-100">
                    {{ Auth::user()->passenger_type ?? 'Regular' }}
                </span>
            </div>

            <div class="flex gap-4 mt-6">
                <div class="flex-1 bg-gray-50 rounded-2xl p-3">
                    <p class="text-xs text-gray-400 font-medium uppercase">Trips</p>
                    <p class="text-xl font-bold text-gray-800">0</p>
                </div>
                <div class="flex-1 bg-gray-50 rounded-2xl p-3">
                    <p class="text-xs text-gray-400 font-medium uppercase">Favorites</p>
                    <p id="fav-count" class="text-xl font-bold text-gray-800">0</p>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-end mb-3 px-1">
            <h3 class="text-gray-800 font-bold">Favorite Routes</h3>
            <a href="{{ route('mobile.dashboard') }}" class="text-xs text-[#00b894] font-bold hover:underline">Manage in Map</a>
        </div>

        <div id="favorites-container" class="bg-white rounded-3xl shadow-sm p-2 mb-6 space-y-1 min-h-[100px]">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-gray-300 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-2 mb-6">
            
            <a href="{{ route('mobile.edit_profile') }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 rounded-2xl transition cursor-pointer group">
                <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center group-hover:bg-green-100 transition">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="flex-grow">
                    <h4 class="font-bold text-gray-800 text-sm">Edit Profile</h4>
                    <p class="text-xs text-gray-400">Change name, email, details</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300"></i>
            </a>

            <div class="h-px bg-gray-100 mx-4"></div>

            <div onclick="toggleNotifications()" class="flex items-center gap-4 p-4 hover:bg-gray-50 rounded-2xl transition cursor-pointer group">
                <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-100 transition">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="flex-grow">
                    <h4 class="font-bold text-gray-800 text-sm">Notifications</h4>
                    <p id="notif-status" class="text-xs text-gray-400">On</p>
                </div>
                
                <div id="notif-switch" class="w-10 h-6 bg-green-100 rounded-full flex items-center p-1 cursor-pointer transition-colors">
                    <div id="notif-dot" class="w-4 h-4 bg-green-500 rounded-full shadow-sm ml-auto transition-all"></div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-white border border-red-100 text-red-500 font-bold py-4 rounded-2xl hover:bg-red-50 transition flex items-center justify-center gap-2 shadow-sm">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </button>
        </form>

    </div>

    <div class="fixed bottom-0 w-full bg-white border-t border-gray-100 px-8 py-4 flex justify-between items-center z-50 rounded-t-[30px] shadow-[0_-5px_20px_rgba(0,0,0,0.03)]">
        <a href="{{ route('mobile.dashboard') }}" class="flex flex-col items-center gap-1.5 text-gray-400 hover:text-green-600 transition group">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 transition group-hover:scale-110"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" /><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" /></svg>
            <span class="text-[10px] font-medium">Home</span>
        </a>
        <a href="{{ route('mobile.dashboard') }}" class="flex flex-col items-center gap-1.5 text-gray-400 hover:text-green-600 transition group">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 transition group-hover:scale-110"><path d="M4.5 6.375a4.125 4.125 0 118.25 0 1.125 1.125 0 11-2.25 0 3 3 0 00-6 0l.991 2.786a1.49 1.49 0 01.304.568l2.667 9.335a.75.75 0 11-1.442.412l-2.567-8.988a5.25 5.25 0 01-1.173.427l-1.37 4.792a.75.75 0 01-1.442-.412l1.957-6.849c.156-.547.422-1.053.778-1.486L4.5 6.375z" /><path d="M14.894 14.418l-2.205-3.15a.75.75 0 111.229-.858l2.613 3.733a.75.75 0 11-1.23.861l-.407-.586zM13.669 17.866l-2.18-2.18a.75.75 0 111.06-1.06l2.18 2.18a.75.75 0 11-1.06 1.06z" /><path d="M21.75 4.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM22.5 12a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM21.75 19.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
            <span class="text-[10px] font-medium">Buses</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-1.5 text-green-600 transition group">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" /></svg>
            <span class="text-[10px] font-bold">Profile</span>
        </a>
    </div>

    <script>
        // 1. ROUTE DATA (Client-side Mapping to match your database IDs)
        // This ensures the profile loads instantly without complex backend queries
        const knownRoutes = {
            1:  { name: "PdP Green Route", number: "01", color: "#00b894" },
            2:  { name: "PdP Red Route",   number: "42", color: "#e74c3c" },
            14: { name: "PdP Red Route",   number: "42", color: "#e74c3c" }, // Handle alternate ID
            3:  { name: "PdP Blue Route",  number: "10", color: "#0984e3" },
            4:  { name: "UV Express",      number: "UV", color: "#6c5ce7" }
        };

        // 2. FETCH FAVORITES
        document.addEventListener('DOMContentLoaded', function() {
            loadFavorites();
            loadNotificationState();
        });

        function loadFavorites() {
            const container = document.getElementById('favorites-container');
            const countEl = document.getElementById('fav-count');

            // Use the same API your Dashboard uses
            fetch('/api/favorites/ids')
                .then(res => res.json())
                .then(ids => {
                    container.innerHTML = ''; // Clear spinner
                    countEl.innerText = ids.length;

                    if (ids.length === 0) {
                        container.innerHTML = `
                            <div class="text-center py-6">
                                <i class="far fa-star text-gray-300 text-3xl mb-2"></i>
                                <p class="text-sm text-gray-400">No favorite routes yet.</p>
                                <a href="{{ route('mobile.dashboard') }}" class="text-xs text-green-600 font-bold mt-2 inline-block">Go to Map to add one</a>
                            </div>`;
                        return;
                    }

                    // Loop through IDs and generate HTML
                    ids.forEach((id, index) => {
                        const route = knownRoutes[id];
                        // If we know this route ID, render it. If not, skip or show generic.
                        const name = route ? route.name : "Route #" + id;
                        const number = route ? route.number : id;
                        const color = route ? route.color : "#95a5a6";

                        // Add Divider if not first item
                        if (index > 0) {
                            const divider = document.createElement('div');
                            divider.className = "h-px bg-gray-100 mx-4";
                            container.appendChild(divider);
                        }

                        const card = document.createElement('div');
                        // Clicking the card takes you to the Map and Zooms to that route
                        card.setAttribute('onclick', `window.location.href='{{ route('mobile.dashboard') }}?show_route=${id}'`);
                        card.className = "flex items-center gap-4 p-3 hover:bg-gray-50 rounded-2xl transition cursor-pointer";
                        
                        card.innerHTML = `
                            <div class="w-12 h-12 rounded-xl text-white flex items-center justify-center font-bold text-lg shadow-sm" style="background-color: ${color}">
                                ${number}
                            </div>
                            <div class="flex-grow">
                                <h4 class="font-bold text-gray-800 text-sm">${name}</h4>
                                <p class="text-xs text-gray-400">Tap to view on map</p>
                            </div>
                            <i class="fas fa-star text-[#00b894]"></i>
                        `;
                        
                        container.appendChild(card);
                    });
                })
                .catch(err => {
                    console.error(err);
                    container.innerHTML = '<p class="text-center text-red-400 py-4 text-xs">Could not load favorites</p>';
                });
        }

        // 3. NOTIFICATION TOGGLE LOGIC
        function loadNotificationState() {
            const isOff = localStorage.getItem('notifications_off') === 'true';
            updateToggleUI(!isOff);
        }

        function toggleNotifications() {
            const current = localStorage.getItem('notifications_off') === 'true';
            const newState = !current; // If it was off (true), new state is on (false) -> wait, logic check
            
            // Logic: 
            // If currently OFF (true), we want to turn ON (false in storage)
            // If currently ON (false/null), we want to turn OFF (true in storage)
            
            if (current) {
                // Was Off, turning On
                localStorage.removeItem('notifications_off');
                updateToggleUI(true);
            } else {
                // Was On, turning Off
                localStorage.setItem('notifications_off', 'true');
                updateToggleUI(false);
            }
        }

        function updateToggleUI(isOn) {
            const dot = document.getElementById('notif-dot');
            const bg = document.getElementById('notif-switch');
            const text = document.getElementById('notif-status');

            if (isOn) {
                dot.classList.remove('mr-auto');
                dot.classList.add('ml-auto');
                dot.classList.add('bg-green-500');
                dot.classList.remove('bg-gray-400');
                
                bg.classList.add('bg-green-100');
                bg.classList.remove('bg-gray-200');
                
                text.innerText = "On";
            } else {
                dot.classList.remove('ml-auto');
                dot.classList.add('mr-auto'); // Move to left
                dot.classList.remove('bg-green-500');
                dot.classList.add('bg-gray-400');
                
                bg.classList.remove('bg-green-100');
                bg.classList.add('bg-gray-200');
                
                text.innerText = "Off";
            }
        }
    </script>

</body>
</html>