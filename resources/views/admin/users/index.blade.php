@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
    <h2>All Registered Users</h2>

    <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-top: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #ddd;">
                    <th style="padding: 0.75rem; text-align: left;">Name</th>
                    <th style="padding: 0.75rem; text-align: left;">Email</th>
                    <th style="padding: 0.75rem; text-align: left;">Passenger Type</th>
                    <th style="padding: 0.75rem; text-align: left;">Uploaded ID</th>
                    <th style="padding: 0.75rem; text-align: left;">Status</th>
                    <th style="padding: 0.75rem; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 0.75rem;">{{ $user->name }}</td>
                        <td style="padding: 0.75rem;">{{ $user->email }}</td>
                        <td style="padding: 0.75rem;">{{ $user->passenger_type }}</td>
                        <td style="padding: 0.75rem;">
                            @if ($user->id_image_path)
                                <a href="{{ asset('storage/' . $user->id_image_path) }}" target="_blank">View ID</a>
                            @else
                                <span>N/A</span>
                            @endif
                        </td>
                        <td style="padding: 0.75rem;">
                            @if($user->is_verified)
                                <span style="color: green;">Verified</span>
                            @else
                                <span style="color: orange;">Not Verified</span>
                            @endif
                        </td>
                        <td style="padding: 0.75rem;">
    @if(!$user->is_verified)
        <form action="{{ route('admin.users.verify', $user) }}" method="POST" style="display: inline; margin-right: 10px;">
            @csrf
            @method('PATCH')
            <button type="submit" style="background: none; border: none; color: #007bff; cursor: pointer; padding: 0;">Verify</button>
        </form>
    @endif

    {{-- Add the delete form --}}
    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
        @csrf
        @method('DELETE')
        <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer; padding: 0;">Delete</button>
    </form>
</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 1rem; text-align: center;">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection