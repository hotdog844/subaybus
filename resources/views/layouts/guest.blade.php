<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SubayBus - Driver Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-100 p-6 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-[#0A5C36]">SubayBus 🚌</h1>
            <a href="{{ route('login') }}" class="text-sm font-bold text-gray-500 hover:text-[#0A5C36]">Back to Login</a>
        </div>
    </header>

    <main class="flex-grow py-12 px-4">
        @yield('content')
    </main>
</body>
</html>