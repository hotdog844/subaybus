@extends('layouts.admin')

@section('title', 'Manage Routes')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>All Routes</h2>
        <a href="{{ route('admin.routes.create') }}" class="btn btn-primary">Add New Route</a>
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
                    <th style="padding: 0.75rem; text-align: left;">ID</th>
                    <th style="padding: 0.75rem; text-align: left;">Name</th>
                    <th style="padding: 0.75rem; text-align: left;">Start</th>
                    <th style="padding: 0.75rem; text-align: left;">End</th>
                    <th style="padding: 0.75rem; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($routes as $route)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 0.75rem;">{{ $route->id }}</td>
                        <td style="padding: 0.75rem;">{{ $route->name }}</td>
                        <td style="padding: 0.75rem;">{{ $route->start_destination }}</td>
                        <td style="padding: 0.75rem;">{{ $route->end_destination }}</td>
                        <td style="padding: 0.75rem; display: flex; align-items: center; gap: 10px;">
                            <a href="{{ route('admin.routes.stops.index', $route) }}" class="action-link" style="color: #27ae60;">Manage Stops</a>
                            <a href="{{ route('admin.routes.edit', $route) }}" class="action-link" style="color: #3498db;">Edit</a>
                            <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="action-form" onsubmit="return confirm('Are you sure you want to delete this route?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 1rem; text-align: center;">No routes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        .action-link {
            text-decoration: none;
            font-weight: 500;
        }
        .action-form {
            display: inline;
            margin: 0;
        }
        .delete-btn {
            background: none;
            border: none;
            color: #e74c3c;
            cursor: pointer;
            padding: 0;
            font-family: inherit;
            font-size: inherit;
            font-weight: 500;
        }
    </style>
@endsection