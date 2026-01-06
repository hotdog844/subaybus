@extends('layouts.admin')

@section('title', 'Manage Drivers')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Manage Drivers</h1>
        <p class="text-sm text-gray-500">View, verify, and manage your fleet operators.</p>
    </div>
    <a href="{{ route('admin.drivers.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-green-200 transition active:scale-95 flex items-center gap-2">
        <i class="fas fa-user-plus"></i> Add New Driver
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <p class="font-medium">{{ session('error') }}</p>
    </div>
@endif

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest border-b border-gray-100">
                <th class="px-6 py-4">ID</th>
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">License #</th>
                <th class="px-6 py-4 text-center">Requirements</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($drivers as $driver)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 text-sm font-bold text-gray-400">#{{ $driver->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $driver->name }}</td>
                    <td class="px-6 py-4 text-gray-600 font-medium">{{ $driver->license_number }}</td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="openVerifyModal({{ $driver->id }}, '{{ asset('storage/'.$driver->license_image) }}', '{{ asset('storage/'.$driver->cert_image) }}', '{{ asset('storage/'.$driver->psa_image) }}')" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 text-xs font-bold rounded-xl hover:bg-green-100 transition">
                            <i class="fas fa-eye"></i> Review Docs
                        </button>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $driver->status == 'verified' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $driver->status ?? 'Pending' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-3 text-sm">
                            <a href="{{ route('admin.drivers.edit', $driver) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-green-700 transition">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                        <i class="fas fa-user-slash block text-4xl mb-4 opacity-20"></i>
                        No drivers found in the database.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="verifyModal" class="fixed inset-0 z-[100] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-5xl rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-black text-gray-800 text-xl italic uppercase tracking-tight">Driver Verification</h3>
            <button onclick="closeVerifyModal()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-red-50 text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-8 overflow-y-auto grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Driver's License</p>
                <div class="h-64 bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden cursor-zoom-in group relative">
                    <img id="licensePreview" class="w-full h-full object-cover transition group-hover:scale-105" onclick="window.open(this.src)">
                </div>
            </div>
            <div class="space-y-3 text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Medical Certificate</p>
                <div class="h-64 bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden cursor-zoom-in group relative">
                    <img id="certPreview" class="w-full h-full object-cover transition group-hover:scale-105" onclick="window.open(this.src)">
                </div>
            </div>
            <div class="space-y-3 text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">PSA/Birth Certificate</p>
                <div class="h-64 bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden cursor-zoom-in group relative">
                    <img id="psaPreview" class="w-full h-full object-cover transition group-hover:scale-105" onclick="window.open(this.src)">
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-100 flex gap-4">
            <button onclick="closeVerifyModal()" class="flex-1 py-4 font-bold text-gray-500 hover:bg-gray-200 bg-gray-100 rounded-2xl transition">Dismiss</button>
            <form id="verifyForm" method="POST" class="flex-1">
                @csrf 
                @method('PATCH')
                <button type="submit" class="w-full py-4 bg-green-600 text-white font-black rounded-2xl shadow-lg shadow-green-200 hover:bg-green-700 transition active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-user-check"></i> Approve & Verify Driver
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openVerifyModal(id, license, cert, psa) {
        document.getElementById('licensePreview').src = license;
        document.getElementById('certPreview').src = cert;
        document.getElementById('psaPreview').src = psa;
        
        // Update form action to the verify route
        document.getElementById('verifyForm').action = `/admin/drivers/${id}/verify`;
        
        document.getElementById('verifyModal').classList.remove('hidden');
    }

    function closeVerifyModal() {
        document.getElementById('verifyModal').classList.add('hidden');
    }
</script>
@endsection