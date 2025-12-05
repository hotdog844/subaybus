<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Edit Profile - SubayBus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f4f6f8; }</style>
</head>
<body class="bg-[#f4f6f8] h-screen flex flex-col">

    <div class="bg-white px-6 py-4 flex items-center gap-4 shadow-sm sticky top-0 z-20">
        <a href="{{ route('mobile.profile') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-green-50 hover:text-green-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Edit Profile</h1>
    </div>

    <div class="flex-grow overflow-y-auto px-6 py-8 pb-32">
        
        <form action="{{ route('mobile.updateProfile') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="flex flex-col items-center mb-6">
                <div class="w-24 h-24 rounded-full bg-gray-200 border-4 border-white shadow-md flex items-center justify-center text-gray-400 text-3xl relative">
                    <i class="fas fa-user"></i>
                    <div class="absolute bottom-0 right-0 w-8 h-8 bg-green-500 rounded-full border-2 border-white flex items-center justify-center text-white text-xs">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Tap to change photo</p>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Full Name</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full text-gray-800 font-medium outline-none bg-transparent placeholder-gray-300" placeholder="Enter your name">
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Email Address</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full text-gray-800 font-medium outline-none bg-transparent placeholder-gray-300" placeholder="name@example.com">
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ auth()->user()->phone ?? '' }}" class="w-full text-gray-800 font-medium outline-none bg-transparent placeholder-gray-300" placeholder="0912 345 6789">
            </div>

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 relative">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Passenger Type</label>
                <select name="passenger_type" class="w-full text-gray-800 font-medium outline-none bg-transparent appearance-none relative z-10 bg-white">
                    <option value="regular" {{ (auth()->user()->passenger_type ?? '') == 'regular' ? 'selected' : '' }}>Regular</option>
                    <option value="student" {{ (auth()->user()->passenger_type ?? '') == 'student' ? 'selected' : '' }}>Student (20% Off)</option>
                    <option value="senior"  {{ (auth()->user()->passenger_type ?? '') == 'senior' ? 'selected' : '' }}>Senior Citizen (20% Off)</option>
                    <option value="pwd"     {{ (auth()->user()->passenger_type ?? '') == 'pwd' ? 'selected' : '' }}>PWD (20% Off)</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 bottom-5 text-gray-400 z-0"></i>
            </div>
            
            <button type="submit" class="w-full bg-[#00b894] text-white font-bold py-4 rounded-2xl shadow-lg shadow-green-500/30 active:scale-95 transition mt-4">
                Save Changes
            </button>
        </form>
    </div>

</body>
</html>