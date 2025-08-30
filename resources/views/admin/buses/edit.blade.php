@extends('layouts.admin')

@section('title', 'Edit Bus')

@section('content')
    <h2>Edit Bus #{{ $bus->id }}</h2>

    <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem;">
        <form action="{{ route('admin.buses.update', $bus) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1rem;">
                <label for="plate_number" style="display: block; margin-bottom: 0.5rem;">Plate Number</label>
                <input type="text" id="plate_number" name="plate_number" value="{{ old('plate_number', $bus->plate_number) }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
            </div>

            {{-- Driver Dropdown --}}
            <div style="margin-bottom: 1rem;">
                <label for="driver_id" style="display: block; margin-bottom: 0.5rem;">Assign Driver</label>
                <select name="driver_id" id="driver_id" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="">-- Unassigned --</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" @if(old('driver_id', $bus->driver_id) == $driver->id) selected @endif>
                            {{ $driver->name }} ({{ $driver->license_number }})
                        </option>
                    @endforeach
                </select>
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
                <input type="number" step="0.01" id="fare" name="fare" value="{{ old('fare', $bus->fare) }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="status" style="display: block; margin-bottom: 0.5rem;">Status</label>
                <select name="status" id="status" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="at terminal" @if(old('status', $bus->status) == 'at terminal') selected @endif>At Terminal</option>
                    <option value="offline" @if(old('status', 'offline') == $bus->status) selected @endif>Offline</option>
                    <option value="on route" @if(old('status', $bus->status) == 'on route') selected @endif>On Route</option>
                </select>
            </div>

            <button type="submit" style="background: #007bff; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Update Bus</button>
        </form>
    </div>
@endsection