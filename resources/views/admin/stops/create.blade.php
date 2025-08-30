@extends('layouts.admin')

@section('title', 'Add New Stop to ' . $route->name)

@section('content')
    <div>
        <a href="{{ route('admin.routes.stops.index', $route) }}" style="text-decoration: none; color: #888;">&larr; Back to Stops List</a>
        <h2 style="margin-top: 0.5rem;">Enter New Stop Details</h2>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem;">
        <form action="{{ route('admin.routes.stops.store', $route) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label for="name" style="display: block; margin-bottom: 0.5rem;">Stop Name (e.g., Robinsons Place Roxas)</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                @error('name') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="sequence" style="display: block; margin-bottom: 0.5rem;">Sequence (Order on the route, e.g., 1, 2, 3)</label>
                <input type="number" id="sequence" name="sequence" value="{{ old('sequence') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                @error('sequence') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label for="latitude" style="display: block; margin-bottom: 0.5rem;">Latitude</label>
                <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                @error('latitude') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="longitude" style="display: block; margin-bottom: 0.5rem;">Longitude</label>
                <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                @error('longitude') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Save Stop</button>
        </form>
    </div>
@endsection