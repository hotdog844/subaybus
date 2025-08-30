@extends('layouts.mobile_base')

@section('content')
<style>
    .bus-card {
        background-color: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        padding: 16px;
        margin-bottom: 20px;
        transition: all 0.3s ease-in-out;
    }
    .bus-card:hover {
        transform: translateY(-2px);
    }
    .bus-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .bus-info {
        font-size: 14px;
        color: #555;
    }
    .bus-tag {
        display: inline-block;
        background-color: #eef4ff;
        color: #2c5eff;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 12px;
        margin-top: 6px;
        margin-right: 6px;
    }
    .btn-track {
        background-color: #2c5eff;
        color: white;
        font-weight: 500;
        font-size: 14px;
        padding: 6px 14px;
        border-radius: 8px;
        margin-top: 12px;
        border: none;
        width: 100%;
    }
    .btn-track:hover {
        background-color: #1d48cc;
    }
    .section-header {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #333;
    }
    .leaflet-container {
        border-radius: 12px;
        overflow: hidden;
        margin-top: 12px;
    }
    .filter-bar {
        margin-bottom: 20px;
    }
</style>

<div class="container py-3">
    <div class="filter-bar">
        <form action="{{ route('mobile') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search route..." value="{{ request('search') }}">
            <select name="filter" class="form-select" style="max-width: 120px;">
                <option value="">All</option>
                <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="regular" {{ request('filter') == 'regular' ? 'selected' : '' }}>Regular</option>
            </select>
            <button class="btn btn-primary">Go</button>
        </form>
    </div>

    <h5 class="section-header">Available Buses</h5>

    @forelse ($buses as $bus)
        <div class="bus-card">
            <div class="bus-title">{{ $bus->route }}</div>

            <div class="bus-info"><strong>Last seen:</strong> 
                {{ $bus->locations->last()?->created_at ? $bus->locations->last()->created_at->diffForHumans() : 'N/A' }}
            </div>

            <div class="bus-info"><strong>Distance:</strong> 
                {{ number_format($bus->distance ?? 0, 2) }} km
            </div>

            <div class="bus-info"><strong>ETA:</strong> 
                {{ $bus->eta ? $bus->eta . ' min' : 'N/A' }}
            </div>

            <div class="bus-info"><strong>Fare:</strong> ₱{{ number_format($bus->fare, 2) }}</div>
            <div class="bus-info"><strong>Discounted:</strong> ₱{{ number_format($bus->discounted_fare, 2) }}</div>

            @if ($bus->status)
                <span class="bus-tag">{{ ucfirst($bus->status) }}</span>
            @endif

            <form action="{{ route('mobile.bus.show', $bus->id) }}" method="GET">
                <button class="btn-track">View Bus Details</button>
            </form>

            @if ($bus->locations->last())
                <div class="mt-3 leaflet-container" style="height: 200px;" id="map-{{ $bus->id }}"></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const map{{ $bus->id }} = L.map('map-{{ $bus->id }}').setView([{{ $bus->locations->last()->latitude }}, {{ $bus->locations->last()->longitude }}], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 18,
                        }).addTo(map{{ $bus->id }});
                        L.marker([{{ $bus->locations->last()->latitude }}, {{ $bus->locations->last()->longitude }}])
                            .addTo(map{{ $bus->id }})
                            .bindPopup("{{ $bus->route }}").openPopup();
                    });
                </script>
            @endif
        </div>
    @empty
        <p class="text-muted">No buses found at the moment.</p>
    @endforelse
</div>
@endsection
