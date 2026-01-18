@extends('layouts.admin')

@section('title', 'Beep Card Management')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>

    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-6 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Beep Cards</h2>
                <p class="text-gray-500 mt-1 text-sm">Manage passenger balances and RFID assignments.</p>
            </div>
            
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
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">User Identity</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Wallet Balance</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">RFID UID</th>
                            <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($users as $user)
                        <tr class="hover:bg-blue-50/30 transition duration-200 group">
                            
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center shadow-sm">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">{{ $user->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="p-5">
                                <span class="inline-block px-3 py-1 rounded-lg text-sm font-bold bg-green-50 text-green-700 border border-green-100">
                                    ₱{{ number_format($user->wallet_balance, 2) }}
                                </span>
                            </td>

                            <td class="p-5">
                                @if($user->card)
                                    <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">
                                        {{ $user->card->card_uid }}
                                    </span>
                                @else
                                    <span class="text-xs text-red-400 italic flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i> No Card
                                    </span>
                                @endif
                            </td>

                            <td class="p-5">
                                <div class="flex justify-end gap-3">
                                    
                                    <form action="{{ route('admin.cards.topup', $user->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <div class="relative">
                                            <span class="absolute left-2 top-1.5 text-gray-400 text-xs">₱</span>
                                            <input type="number" name="amount" placeholder="00" class="pl-5 pr-2 py-1 w-20 text-xs border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition" required>
                                        </div>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs font-bold shadow-md transition-transform active:scale-95">
                                            Load
                                        </button>
                                    </form>

                                    @if(!$user->card)
                                    <div class="h-6 w-px bg-gray-200"></div> <form action="{{ route('admin.cards.assign', $user->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input type="text" name="card_uid" placeholder="RFID UID" class="px-2 py-1 w-24 text-xs font-mono border border-gray-200 rounded-lg focus:ring-gray-500 focus:border-gray-500 transition" required>
                                        <button type="submit" class="bg-gray-800 hover:bg-black text-white px-3 py-1 rounded-lg text-xs font-bold shadow-md transition-transform active:scale-95">
                                            Assign
                                        </button>
                                    </form>
                                    @endif

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