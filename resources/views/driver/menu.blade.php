<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Hub - SubayBus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
</head>
<body class="min-h-screen flex flex-col">

    <div class="bg-gray-900 text-white p-6 pb-12 rounded-b-[40px] shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500 rounded-full mix-blend-overlay filter blur-3xl opacity-20"></div>
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <p class="text-gray-400 text-xs uppercase tracking-wider font-bold">Welcome back,</p>
                <h1 class="text-2xl font-extrabold">{{ Auth::user()->name ?? 'Driver' }}</h1>
            </div>
            <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center border border-gray-700">
                <i class="fas fa-id-card text-blue-400"></i>
            </div>
        </div>
    </div>

    <div class="flex-grow px-6 -mt-8 relative z-20">
        
        <div class="bg-white rounded-3xl shadow-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-bus text-blue-600"></i> Select Unit
            </h2>
            
            @if($buses->count() > 0)
                <div class="space-y-3">
                    @foreach($buses as $bus)
                    <a href="{{ route('driver.dashboard', $bus->id) }}" class="block group">
                        <div class="border border-gray-100 bg-gray-50 hover:bg-blue-50 hover:border-blue-200 rounded-2xl p-4 transition-all flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm text-gray-400 group-hover:text-blue-500 transition">
                                    <i class="fas fa-bus"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 group-hover:text-blue-700">{{ $bus->bus_number }}</h4>
                                    <p class="text-xs text-gray-500">{{ $bus->route ? $bus->route->name : 'No Route' }}</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm text-gray-300 group-hover:text-blue-500 group-hover:bg-blue-100 transition">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-400 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <i class="fas fa-ban text-2xl mb-2"></i>
                    <p class="text-sm">No buses available.</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center">
                <span class="block text-2xl font-black text-gray-800">0</span>
                <span class="text-[10px] text-gray-400 uppercase font-bold">Hours Today</span>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center">
                <span class="block text-2xl font-black text-green-500">Active</span>
                <span class="text-[10px] text-gray-400 uppercase font-bold">Account Status</span>
            </div>
        </div>
    </div>

    <div class="p-6 mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-white border border-red-100 text-red-500 font-bold py-4 rounded-2xl shadow-sm hover:bg-red-50 transition flex items-center justify-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </button>
        </form>
    </div>

</body>
</html>