<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login - SubayBus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background-color: #111827; }</style>
</head>
<body class="flex items-center justify-center min-h-screen px-6">

    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-blue-600 rounded-2xl mx-auto flex items-center justify-center shadow-lg shadow-blue-500/50 mb-4">
                <i class="fas fa-bus text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-white">Driver Portal</h1>
            <p class="text-gray-400 mt-2">Sign in to start your shift</p>
        </div>

        <div class="bg-gray-800 p-8 rounded-3xl shadow-xl border border-gray-700">
            
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-900/50 border border-red-500/50 rounded-xl text-red-200 text-sm text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('driver.authenticate') }}">
                @csrf

                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Email Address</label>
                    <input type="email" name="email" required 
                        class="w-full bg-gray-900 text-white border border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                        placeholder="driver@subaybus.com">
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Password</label>
                    <input type="password" name="password" required 
                        class="w-full bg-gray-900 text-white border border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                        placeholder="••••••••">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-600/30 transition-all active:scale-95">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-gray-500 text-sm mt-8">
            Not a driver? <a href="/login" class="text-blue-400 hover:text-blue-300">Passenger Login</a>
        </p>
    </div>

</body>
</html>