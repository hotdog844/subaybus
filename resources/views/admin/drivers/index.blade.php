@extends('layouts.admin')

@section('title', 'Manage Drivers')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>All Drivers</h2>
        <a href="{{ route('admin.drivers.create') }}" style="background: #27ae60; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px;">Add New Driver</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; text-align: center;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; text-align: center;">
        {{ session('error') }}
    </div>
@endif

    <div style="background: white; padding: 1.5rem; border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #ddd;">
                    <th style="padding: 0.75rem; text-align: left;">ID</th>
                    <th style="padding: 0.75rem; text-align: left;">Name</th>
                    <th style="padding: 0.75rem; text-align: left;">License Number</th>
                    <th style="padding: 0.75rem; text-align: left;">Contact #</th>
                    <th style="padding: 0.75rem; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($drivers as $driver)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 0.75rem;">{{ $driver->id }}</td>
                        <td style="padding: 0.75rem;">{{ $driver->name }}</td>
                        <td style="padding: 0.75rem;">{{ $driver->license_number }}</td>
                        <td style="padding: 0.75rem;">{{ $driver->contact_number }}</td>
                        <td style="padding: 0.75rem;">
                            <a href="{{ route('admin.drivers.edit', $driver) }}" style="margin-right: 10px;">Edit</a>
                            <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer; padding: 0;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 1rem; text-align: center;">No drivers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection