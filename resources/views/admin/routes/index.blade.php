@extends('layouts.admin')

@section('title', 'Manage Routes')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>

    <div class="max-w-7xl mx-auto">
        
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Route Management</h2>
                <p class="text-gray-500 mt-1 text-sm">Configure transport paths, start/end points, and designated stops.</p>
            </div>
            
            <a href="{{ route('admin.routes.create') }}" class="bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-gray-300 flex items-center gap-2 transition-transform transform active:scale-95">
                <i class="fas fa-plus text-sm"></i> 
                <span>Create New Route</span>
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Route Name</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Origin (Start)</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Destination (End)</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Distance</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($routes as $route)
                        <tr class="hover:bg-gray-50 transition duration-200 group">
                            
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    @php
                                        $iconColor = 'bg-gray-100 text-gray-500';
                                        if(str_contains($route->name, 'Green')) $iconColor = 'bg-green-100 text-green-600';
                                        if(str_contains($route->name, 'Red'))   $iconColor = 'bg-red-100 text-red-600';
                                        if(str_contains($route->name, 'Blue'))  $iconColor = 'bg-blue-100 text-blue-600';
                                    @endphp
                                    <div class="w-10 h-10 rounded-xl {{ $iconColor }} flex items-center justify-center shadow-sm">
                                        <i class="fas fa-route"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">{{ $route->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono">ID: #{{ $route->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="p-5">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-green-500 text-xs"></i>
                                    <span class="text-sm font-semibold text-gray-700">{{ $route->origin ?? 'Not Set' }}</span>
                                </div>
                            </td>

                            <td class="p-5">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-flag-checkered text-red-500 text-xs"></i>
                                    <span class="text-sm font-semibold text-gray-700">{{ $route->destination ?? 'Not Set' }}</span>
                                </div>
                            </td>

                            <td class="p-5">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">
                                    {{ $route->distance ?? '0' }} km
                                </span>
                            </td>

                            <td class="p-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.routes.edit', $route->id) }}" class="btn btn-info btn-sm bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1 transition">
                                        <i class="fas fa-map-marker-alt"></i> Stops
                                    </a>
                                    
                                    <a href="{{ route('admin.routes.edit', $route->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:text-blue-600 hover:bg-white transition shadow-sm">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.routes.destroy', $route->id) }}" method="POST" onsubmit="return confirm('Delete route?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:text-red-600 hover:bg-white transition shadow-sm">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection