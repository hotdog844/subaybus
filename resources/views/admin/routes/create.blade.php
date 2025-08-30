@extends('layouts.admin')

@section('title', 'Add New Route')

@section('content')
    <h2>Enter New Route Details</h2>

    <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem;">
        <form action="{{ route('admin.routes.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label for="name" style="display: block; margin-bottom: 0.5rem;">Route Name (e.g., Pavia - City Proper)</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                @error('name') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="start_destination" style="display: block; margin-bottom: 0.5rem;">Start Destination</label>
                <input type="text" id="start_destination" name="start_destination" value="{{ old('start_destination') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                @error('start_destination') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="end_destination" style="display: block; margin-bottom: 0.5rem;">End Destination</label>
                <input type="text" id="end_destination" name="end_destination" value="{{ old('end_destination') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                @error('end_destination') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="description" style="display: block; margin-bottom: 0.5rem;">Description (Optional)</label>
                <textarea id="description" name="description" rows="4" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">{{ old('description') }}</textarea>
                @error('description') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
            </div>

            <button type="submit" style="background: #27ae60; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Save Route</button>
        </form>
    </div>
@endsection