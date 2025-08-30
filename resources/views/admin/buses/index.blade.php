@extends('layouts.admin')

@section('title', 'Manage Buses')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>All Buses</h2>
        @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; text-align: center;">
        {{ session('success') }}
    </div>
@endif
        {{-- This link will point to the 'Add Bus' form we will create next --}}
        <a href="{{ route('admin.buses.create') }}" class="btn btn-primary">Add New Bus</a>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #ddd;">
                    <th style="padding: 0.75rem; text-align: left;">ID</th>
                    <th style="padding: 0.75rem; text-align: left;">Plate Number</th>
                    <th style="padding: 0.75rem; text-align: left;">Driver</th>
                    <th style="padding: 0.75rem; text-align: left;">Status</th>
                    <th style="padding: 0.75rem; text-align: left;">Last Seen</th>
                    <th style="padding: 0.75rem; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($buses as $bus)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 0.75rem;">{{ $bus->id }}</td>
                        <td style="padding: 0.75rem;">{{ $bus->plate_number }}</td>
                        <td style="padding: 0.75rem;">{{ $bus->driver->name ?? 'Unassigned' }}</td>
                        <td style="padding: 0.75rem;">{{ ucfirst($bus->status) }}</td>
                        <td style="padding: 0.75rem;">{{ $bus->last_seen ? $bus->last_seen->diffForHumans() : 'Never' }}</td>
                        <td style="padding: 0.75rem;">
                            {{-- We will add edit and delete buttons here --}}
                            <a href="{{ route('admin.buses.edit', $bus) }}" style="margin-right: 10px;">Edit</a>
<form action="{{ route('admin.buses.destroy', $bus) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this bus?');">
    @csrf
    @method('DELETE')
    <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer; padding: 0;">Delete</button>
</form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 1rem; text-align: center;">No buses found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection