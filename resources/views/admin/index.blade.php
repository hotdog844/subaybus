<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Command Center') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold">Welcome back, Admin!</h3>
                    <p class="text-gray-500">Here is the current status of the fleet.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                
                <div class="bg-blue-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-blue-200 text-sm font-bold uppercase">Total Buses</p>
                            <h4 class="text-3xl font-black mt-1">{{ \App\Models\Bus::count() }}</h4>
                        </div>
                        <div class="bg-blue-500/30 p-3 rounded-full">
                            <i class="fas fa-bus text-2xl"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.buses.index') }}" class="block mt-4 text-xs font-bold text-blue-100 hover:text-white">Manage Fleet →</a>
                </div>

                <div class="bg-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-purple-200 text-sm font-bold uppercase">Active Routes</p>
                            <h4 class="text-3xl font-black mt-1">{{ \App\Models\Route::count() }}</h4>
                        </div>
                        <div class="bg-purple-500/30 p-3 rounded-full">
                            <i class="fas fa-map-marked-alt text-2xl"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.routes.index') }}" class="block mt-4 text-xs font-bold text-purple-100 hover:text-white">View Routes →</a>
                </div>

                <div class="bg-orange-500 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-orange-200 text-sm font-bold uppercase">Active Alerts</p>
                            <h4 class="text-3xl font-black mt-1">{{ \App\Models\Alert::count() }}</h4>
                        </div>
                        <div class="bg-orange-400/30 p-3 rounded-full">
                            <i class="fas fa-bullhorn text-2xl"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.alerts.index') }}" class="block mt-4 text-xs font-bold text-orange-100 hover:text-white">Broadcast Center →</a>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>