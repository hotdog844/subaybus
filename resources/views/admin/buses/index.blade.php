@extends('layouts.admin')

@section('title', 'Fleet Management')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>

    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-6 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Fleet Management</h2>
                <p class="text-gray-500 mt-1 text-sm">Monitor assignments, status, and activity of all bus units.</p>
            </div>
            
            <a href="{{ route('admin.buses.create') }}" class="bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-gray-300 flex items-center gap-2 transition-transform transform active:scale-95">
                <i class="fas fa-plus text-sm"></i> 
                <span>Add New Bus</span>
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-xl shadow-sm flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <p class="font-bold text-sm">Success</p>
                <p class="text-xs">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Bus Identity</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Assigned Driver</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Current Route</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Last Activity</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-50">
                        @forelse($buses as $bus)
                        <tr class="hover:bg-blue-50/30 transition duration-200 group">
                            
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center shadow-sm group-hover:bg-white group-hover:shadow-md transition">
                                        <i class="fas fa-bus"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">{{ $bus->plate_number }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono">ID: #{{ str_pad($bus->id, 3, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="p-5">
                                @if($bus->driver)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-bold">
                                            {{ substr($bus->driver->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">{{ $bus->driver->name }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                                        <i class="fas fa-user-slash text-[10px]"></i> Unassigned
                                    </span>
                                @endif
                            </td>

                            <td class="p-5">
                                {{-- CHANGE $bus->route TO $bus->assignedRoute --}}
@if($bus->assignedRoute)
    @php
        $routeName = $bus->assignedRoute->name;
        $badgeColor = 'bg-gray-100 text-gray-600 border-gray-200';
        
        if(str_contains($routeName, 'Green')) $badgeColor = 'bg-green-50 text-green-600 border-green-100';
        if(str_contains($routeName, 'Red'))   $badgeColor = 'bg-red-50 text-red-600 border-red-100';
        if(str_contains($routeName, 'Blue'))  $badgeColor = 'bg-blue-50 text-blue-600 border-blue-100';
        if(str_contains($routeName, 'UV'))    $badgeColor = 'bg-purple-50 text-purple-600 border-purple-100';
    @endphp

    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border {{ $badgeColor }}">
        {{ $routeName }}
    </span>
@else
    <span class="text-xs text-gray-400 italic">No Route Set</span>
@endif
                            </td>

                            <td class="p-5">
                                @if($bus->status === 'on route')
                                    <div class="flex items-center gap-2">
                                        <span class="relative flex h-2.5 w-2.5">
                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                        </span>
                                        <span class="text-xs font-bold text-green-700">On Route</span>
                                    </div>
                                @elseif($bus->status === 'at terminal')
                                    <span class="inline-flex items-center gap-1.5 bg-yellow-50 text-yellow-700 px-2.5 py-1 rounded-full text-xs font-bold border border-yellow-100">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span> At Terminal
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full text-xs font-bold">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Offline
                                    </span>
                                @endif
                            </td>

                            <td class="p-5 text-xs text-gray-500 font-medium">
                                @if($bus->last_seen)
                                    <i class="far fa-clock mr-1 text-gray-400"></i> {{ $bus->last_seen->diffForHumans() }}
                                @else
                                    <span class="text-gray-300">Never</span>
                                @endif
                            </td>

                            <td class="p-5 text-right">
                                <div class="flex justify-end gap-2 opacity-100 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.buses.edit', $bus) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition shadow-sm" title="Edit Bus">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.buses.destroy', $bus) }}" method="POST" onsubmit="return confirm('Delete this bus unit permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition shadow-sm" title="Delete Bus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-3xl">
                                    <i class="fas fa-bus"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">No Buses Found</h3>
                                <p class="text-gray-500 text-sm mb-4">Your fleet list is currently empty.</p>
                                <a href="{{ route('admin.buses.create') }}" class="text-blue-600 hover:text-blue-800 font-bold text-sm hover:underline">
                                    Add your first bus &rarr;
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($buses, 'links'))
            <div class="bg-gray-50 px-5 py-4 border-t border-gray-100">
                {{ $buses->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection