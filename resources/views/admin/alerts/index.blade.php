@extends('layouts.admin')

@section('title', 'Broadcast Center')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-1">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 sticky top-6">
            <div class="p-6 bg-gray-900 text-white">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-tower-broadcast"></i> New Alert
                </h3>
                <p class="text-gray-400 text-xs mt-1">Send a notification to all active passengers.</p>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.alerts.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Select Priority</label>
                        <div class="grid grid-cols-3 gap-2">
                            
                            <label class="cursor-pointer relative">
                                <input type="radio" name="type" value="info" class="peer sr-only" checked>
                                <div class="p-3 rounded-xl border-2 border-gray-100 bg-white text-center opacity-50 grayscale peer-checked:grayscale-0 peer-checked:opacity-100 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all hover:opacity-100">
                                    <i class="fas fa-info-circle text-xl text-blue-500 mb-1 block"></i>
                                    <span class="text-[10px] font-bold text-blue-600">INFO</span>
                                </div>
                            </label>

                            <label class="cursor-pointer relative">
                                <input type="radio" name="type" value="warning" class="peer sr-only">
                                <div class="p-3 rounded-xl border-2 border-gray-100 bg-white text-center opacity-50 grayscale peer-checked:grayscale-0 peer-checked:opacity-100 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all hover:opacity-100">
                                    <i class="fas fa-clock text-xl text-orange-400 mb-1 block"></i>
                                    <span class="text-[10px] font-bold text-orange-600">DELAY</span>
                                </div>
                            </label>

                            <label class="cursor-pointer relative">
                                <input type="radio" name="type" value="danger" class="peer sr-only">
                                <div class="p-3 rounded-xl border-2 border-gray-100 bg-white text-center opacity-50 grayscale peer-checked:grayscale-0 peer-checked:opacity-100 peer-checked:border-red-500 peer-checked:bg-red-50 transition-all hover:opacity-100">
                                    <i class="fas fa-exclamation-triangle text-xl text-red-400 mb-1 block"></i>
                                    <span class="text-[10px] font-bold text-red-600">URGENT</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Headline</label>
                        <input type="text" name="title" placeholder="e.g. Flash Flood Warning" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none transition font-bold text-gray-700" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Message Details</label>
                        <textarea name="message" rows="4" placeholder="e.g. Bus 42 will be delayed by 15 mins..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none transition text-sm text-gray-600" required></textarea>
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-black transition transform active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Broadcast Now
                    </button>

                </form>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Active Broadcasts</h3>
                <div class="flex items-center gap-2">
                    <span class="flex h-3 w-3 relative justify-center items-center">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                    </span>
                    <span class="text-xs font-bold text-red-500 uppercase tracking-wider">Live System Active</span>
                </div>
            </div>

            <div class="divide-y divide-gray-50 p-4 space-y-3">
                @if(count($alerts) > 0)
                    @foreach($alerts as $alert)
                        <div class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100 group">
                            
                            <div class="flex-shrink-0 mt-1">
                                @if($alert->type == 'info')
                                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shadow-sm"><i class="fas fa-info-circle text-lg"></i></div>
                                @elseif($alert->type == 'warning')
                                    <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shadow-sm"><i class="fas fa-clock text-lg"></i></div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center shadow-sm animate-pulse"><i class="fas fa-exclamation-triangle text-lg"></i></div>
                                @endif
                            </div>

                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-gray-800 text-lg">{{ $alert->title }}</h4>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $alert->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-600 text-sm mt-1 leading-relaxed">{{ $alert->message }}</p>
                            </div>

                            <form action="{{ route('admin.alerts.destroy', $alert->id) }}" method="POST" onsubmit="return confirm('Stop broadcasting this alert?');">
                                @csrf
                                @method('DELETE')
                                <button class="w-8 h-8 rounded-full bg-white border border-gray-200 text-gray-300 hover:text-red-500 hover:border-red-500 hover:bg-red-50 flex items-center justify-center transition shadow-sm opacity-0 group-hover:opacity-100">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                @else
                    <div class="py-12 flex flex-col items-center justify-center text-center opacity-60">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-check-circle text-4xl text-gray-300"></i>
                        </div>
                        <h3 class="text-gray-900 font-bold text-lg">All Clear</h3>
                        <p class="text-gray-500 text-sm max-w-xs mx-auto">There are no active alerts.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection