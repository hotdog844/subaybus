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
                <div class="w-20 h-20 rounded-full bg-[#00b894] text-white flex items-center justify-center text-2xl font-bold mb-3 border-4 border-white shadow-sm">
                    {{ substr(auth()->user()->name ?? 'Guest', 0, 2) }}
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ auth()->user()->name ?? 'Guest User' }}</h2>
                <p class="text-sm text-gray-400">{{ auth()->user()->email ?? 'guest@example.com' }}</p>
            </div>

            <div class="flex gap-4 mt-6">
                <div class="flex-1 bg-gray-50 rounded-2xl p-3">
                    <p class="text-xs text-gray-400 font-medium uppercase">Trips</p>
                    <p class="text-xl font-bold text-gray-800">127</p>
                </div>
                <div class="flex-1 bg-gray-50 rounded-2xl p-3">
                    <p class="text-xs text-gray-400 font-medium uppercase">Favorites</p>
                    <p class="text-xl font-bold text-gray-800">3</p>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-end mb-3 px-1">
            <h3 class="text-gray-800 font-bold">Favorite Routes</h3>
            <a href="#" class="text-xs text-[#00b894] font-bold hover:underline">Edit</a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-2 mb-6 space-y-1">
            
            <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-2xl transition cursor-pointer">
                <div class="w-12 h-12 rounded-xl bg-[#00b894] text-white flex items-center justify-center font-bold text-lg shadow-sm">
                    42
                </div>
                <div class="flex-grow">
                    <h4 class="font-bold text-gray-800 text-sm">Downtown Express</h4>
                    <p class="text-xs text-gray-400">Next: 3 mins</p>
                </div>
                <i class="fas fa-star text-[#00b894]"></i>
            </div>

            <div class="h-px bg-gray-100 mx-4"></div>

            <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-2xl transition cursor-pointer">
                <div class="w-12 h-12 rounded-xl bg-[#00b894] text-white flex items-center justify-center font-bold text-lg shadow-sm">
                    18
                </div>
                <div class="flex-grow">
                    <h4 class="font-bold text-gray-800 text-sm">North Loop</h4>
                    <p class="text-xs text-gray-400">Next: Delayed</p>
                </div>
                <i class="fas fa-star text-[#00b894]"></i>
            </div>

             <div class="h-px bg-gray-100 mx-4"></div>

            <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-2xl transition cursor-pointer">
                <div class="w-12 h-12 rounded-xl bg-[#00b894] text-white flex items-center justify-center font-bold text-lg shadow-sm">
                    33
                </div>
                <div class="flex-grow">
                    <h4 class="font-bold text-gray-800 text-sm">Airport Express</h4>
                    <p class="text-xs text-gray-400">Next: 15 mins</p>
                </div>
                <i class="fas fa-star text-[#00b894]"></i>
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

            <a href="#" class="flex items-center gap-4 p-4 hover:bg-gray-50 rounded-2xl transition cursor-pointer group">
                <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-100 transition">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="flex-grow">
                    <h4 class="font-bold text-gray-800 text-sm">Notifications</h4>
                </div>
                <div class="w-10 h-6 bg-green-100 rounded-full flex items-center p-1 cursor-pointer">
                    <div class="w-4 h-4 bg-green-500 rounded-full shadow-sm ml-auto"></div>
                </div>
            </a>
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

</body>
</html>