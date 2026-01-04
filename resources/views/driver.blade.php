<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login - SubayBus</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center h-screen">

    <div class="w-full max-w-sm p-6 bg-gray-800 rounded-lg shadow-xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-500">SubayBus</h1>
            <p class="text-gray-400">Driver Portal</p>
        </div>

        <form action="{{ route('driver.authenticate') }}" method="POST">
            @csrf
            
            <label class="block mb-2 text-sm font-medium text-gray-300">Select Your Bus Unit</label>
            <select name="bus_id" class="w-full p-4 mb-6 bg-gray-700 border border-gray-600 rounded-lg text-white text-lg focus:ring-blue-500 focus:border-blue-500">
                @foreach($buses as $bus)
                    <option value="{{ $bus->id }}">{{ $bus->bus_number }}</option>
                @endforeach
            </select>

            <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-800 font-medium rounded-lg text-xl px-5 py-4 text-center">
                Start Shift 🚌
            </button>
        </form>
    </div>

</body>
</html>