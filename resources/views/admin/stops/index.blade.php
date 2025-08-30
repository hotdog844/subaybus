@extends('layouts.admin')

@section('title')
    Manage Stops for {{ $route->name }}
@endsection

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <a href="{{ route('admin.routes.index') }}" style="text-decoration: none; color: #888;">&larr; Back to Routes</a>
            <h2 style="margin-top: 0.5rem;">Stops for: {{ $route->name }}</h2>
        </div>
        <a href="{{ route('admin.routes.stops.create', $route) }}" class="btn btn-primary">Add New Stop</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; text-align: center;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: white; padding: 1.5rem; border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #ddd;">
                    <th style="padding: 0.75rem; text-align: left;">Sequence</th>
                    <th style="padding: 0.75rem; text-align: left;">Stop Name</th>
                    <th style="padding: 0.75rem; text-align: left;">Latitude</th>
                    <th style="padding: 0.75rem; text-align: left;">Longitude</th>
                    <th style="padding: 0.75rem; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stops as $stop)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 0.75rem;">{{ $stop->sequence }}</td>
                        <td style="padding: 0.75rem;">{{ $stop->name }}</td>
                        <td style="padding: 0.75rem;">{{ $stop->latitude }}</td>
                        <td style="padding: 0.75rem;">{{ $stop->longitude }}</td>
                        <td style="padding: 0.75rem;">
                            {{-- We will make these functional in the next steps --}}
                            <a href="#" style="margin-right: 10px; color: #3498db; text-decoration: none;">Edit</a>
                            <form action="#" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer; padding: 0; font-family: inherit; font-size: inherit;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 1rem; text-align: center;">No stops have been added for this route yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection