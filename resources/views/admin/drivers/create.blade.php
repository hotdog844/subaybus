@extends('layouts.guest')

@section('title', 'Register New Driver')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.drivers.index') }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-600 hover:text-green-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Registration for New Driver</h1>
    </div>

    <form action="{{ route('admin.drivers.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        @csrf
        
        <div class="p-8 space-y-8">
            <div>
                <h2 class="text-sm font-bold text-green-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-user-circle"></i> Basic Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Driver's Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none transition">
                        @error('name') <p class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="contact_number" class="block text-sm font-semibold text-gray-700 mb-2">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" 
                               placeholder="e.g. 09123456789"
                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none transition">
                        @error('contact_number') <p class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-bold text-green-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-lock"></i> Account Credentials
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address (Login ID)</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none transition">
                        @error('email') <p class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none transition">
                        @error('password') <p class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-bold text-green-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-file-contract"></i> Legal Requirements
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="license_number" class="block text-sm font-semibold text-gray-700 mb-2">Professional License Number</label>
                        <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}" required 
                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none transition">
                        @error('license_number') <p class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="license_image" class="block text-sm font-semibold text-gray-700">Driver's License Photo</label>
                        <div class="relative group">
                            <input type="file" id="license_image" name="license_image" required 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-dashed border-gray-300 rounded-xl p-4">
                        </div>
                        @error('license_image') <p class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="cert_image" class="block text-sm font-semibold text-gray-700">Medical Certificate</label>
                        <div class="relative group">
                            <input type="file" id="cert_image" name="cert_image" required 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-dashed border-gray-300 rounded-xl p-4">
                        </div>
                        @error('cert_image') <p class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label for="psa_image" class="block text-sm font-semibold text-gray-700">PSA / Birth Certificate</label>
                        <div class="relative group">
                            <input type="file" id="psa_image" name="psa_image" required 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-dashed border-gray-300 rounded-xl p-4">
                        </div>
                        @error('psa_image') <p class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-8 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-green-200 transition active:scale-95">
                Save Driver & Documents
            </button>
        </div>
    </form>
</div>
@endsection