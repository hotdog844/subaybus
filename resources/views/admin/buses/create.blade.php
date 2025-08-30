@extends('layouts.admin')

@section('title', 'Add New Bus')

@section('content')
    <h2>Enter New Bus Details</h2>

    <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem;">
        <form action="{{ route('admin.buses.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label for="plate_number" style="display: block; margin-bottom: 0.5rem;">Plate Number</label>
                <input type="text" id="plate_number" name="plate_number" value="{{ old('plate_number') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                @error('plate_number') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Driver Dropdown --}}
            <div style="margin-bottom: 1rem;">
                <label for="driver_id" style="display: block; margin-bottom: 0.5rem;">Assign Driver</label>
                <select name="driver_id" id="driver_id" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="">-- Unassigned --</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->license_number }})</option>
                    @endforeach
                </select>
                @error('driver_id') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Route Dropdown --}}
<div style="margin-bottom: 1rem;">
    <label for="route_id" style="display: block; margin-bottom: 0.5rem;">Assign Route</label>
    <select name="route_id" id="route_id" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        <option value="">-- Unassigned --</option>
        @foreach($routes as $route)
            {{-- For the edit form, this line will pre-select the correct route --}}
            <option value="{{ $route->id }}" @if(isset($bus) && $bus->route_id == $route->id) selected @endif>
                {{ $route->name }}
            </option>
        @endforeach
    </select>
    @error('route_id') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
</div>

            <div style="margin-bottom: 1rem;">
                <label for="fare" style="display: block; margin-bottom: 0.5rem;">Fare</label>
                <input type="number" step="0.01" id="fare" name="fare" value="{{ old('fare') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                @error('fare') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="status" style="display: block; margin-bottom: 0.5rem;">Initial Status</label>
                <select name="status" id="status" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="at terminal">At Terminal</option>
                    <option value="offline" selected>Offline</option>
                    <option value="on route">On Route</option>
                </select>
            </div>

            <button type="submit" style="background: #27ae60; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Save Bus</button>
        </form>
    </div>
@endsection