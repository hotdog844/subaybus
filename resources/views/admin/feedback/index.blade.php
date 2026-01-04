@extends('layouts.admin')

@section('title', 'Feedback Center')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
<div class="max-w-7xl mx-auto space-y-6">
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-1">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Passenger Voice</h2>
            <p class="text-sm text-gray-500">Monitor fleet performance.</p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fas fa-comments"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $totalFeedback }}</div>
                <div class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Reviews</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full {{ $unreadCount > 0 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }} flex items-center justify-center text-xl">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $unreadCount }}</div>
                <div class="text-xs text-gray-500 uppercase font-bold tracking-wider">New / Unread</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($avgRating, 1) }}</div>
                <div class="text-xs text-gray-500 uppercase font-bold tracking-wider">Avg Rating</div>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.feedback.index') }}" class="relative w-full max-w-md">
            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by passenger or bus plate..." 
                   value="{{ request('search') }}"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition text-sm">
        </form>
        <div class="text-sm text-gray-400 italic">
            Showing {{ $feedbackItems->firstItem() ?? 0 }}-{{ $feedbackItems->lastItem() ?? 0 }} of {{ $feedbackItems->total() }}
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl shadow-gray-100/50 overflow-hidden border border-gray-100">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/80 border-b border-gray-100">
                <tr>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Passenger</th>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Trip Details</th>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Rating & Comment</th>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($feedbackItems as $feedback)
                <tr class="hover:bg-blue-50/30 transition group {{ $feedback->status == 'new' ? 'bg-blue-50/10' : '' }}">
                    
                    <td class="p-5 align-top w-1/4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 text-white flex items-center justify-center font-bold text-sm shadow-md">
                                {{ substr($feedback->user_name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-sm">{{ $feedback->user_name }}</div>
                                <div class="text-xs text-gray-400 mb-1">{{ $feedback->user_email }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">
                                    {{ \Carbon\Carbon::parse($feedback->created_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="p-5 align-top w-1/6">
                        @if($feedback->bus_plate)
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-bus text-gray-300"></i>
                                <span class="font-bold text-gray-700 text-sm">{{ $feedback->bus_plate }}</span>
                            </div>
                        @else
                            <span class="text-gray-400 text-xs italic">Bus ID: {{ $feedback->bus_id }} (Deleted)</span>
                        @endif
                        
                        @if($feedback->status == 'new')
                            <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 px-2 py-0.5 rounded-full text-[10px] font-bold border border-red-100">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span> NEW
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full text-[10px] font-bold border border-gray-200">
                                <i class="fas fa-check text-[8px]"></i> Read
                            </span>
                        @endif
                    </td>

                    <td class="p-5 align-top">
                        <div class="flex text-yellow-400 text-xs mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $feedback->rating ? '' : 'text-gray-200' }}"></i>
                            @endfor
                            <span class="ml-2 text-gray-400 font-bold">{{ $feedback->rating }}.0</span>
                        </div>
                        <div class="text-sm text-gray-600 leading-relaxed relative pl-3 border-l-2 border-gray-200">
                            "{{ $feedback->comment ?? 'No comment provided.' }}"
                        </div>
                    </td>

                    <td class="p-5 align-top text-right">
                        <div class="flex flex-col gap-2 items-end opacity-80 group-hover:opacity-100 transition">
                            
                            @if($feedback->status == 'new')
                            <form action="{{ route('admin.feedback.read', $feedback->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                    <i class="fas fa-check-double"></i> Mark Read
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('admin.feedback.destroy', $feedback->id) }}" method="POST" onsubmit="return confirm('Delete this feedback permanently?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-gray-400 hover:text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-2xl">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">All Caught Up</h3>
                        <p class="text-gray-500 text-sm">No feedback found matching your criteria.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $feedbackItems->links() }}
    </div>
</div>
@endsection