@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
    <div style="margin-bottom: 2rem;">
        <h2>Passenger Verification</h2>
        <p style="color: #666;">Review uploaded IDs and verify passenger accounts.</p>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #f0f0f0;">
                    <th style="padding: 1rem; text-align: left;">Name</th>
                    <th style="padding: 1rem; text-align: left;">Email</th>
                    <th style="padding: 1rem; text-align: left;">Passenger Type</th>
                    <th style="padding: 1rem; text-align: left;">Valid ID</th>
                    <th style="padding: 1rem; text-align: left;">Status</th>
                    <th style="padding: 1rem; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        {{-- Name --}}
                        <td style="padding: 1rem;">
                            <strong>{{ $user->name }}</strong>
                        </td>

                        {{-- Email --}}
                        <td style="padding: 1rem;">{{ $user->email }}</td>

                        {{-- Passenger Type --}}
                        <td style="padding: 1rem;">
                            @if($user->passenger_type)
                                {{ $user->passenger_type }}
                            @else
                                <span style="color: #999; font-style: italic;">Not set</span>
                            @endif
                        </td>

                        {{-- Valid ID Link --}}
                        <td style="padding: 1rem;">
                            @if ($user->id_image_path)
                                <a href="{{ asset('storage/' . $user->id_image_path) }}" target="_blank" style="text-decoration: none; color: #007bff; font-weight: bold;">
                                    View ID Photo ↗
                                </a>
                            @else
                                <span style="color: #ccc;">No ID Uploaded</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td style="padding: 1rem;">
                            @if($user->is_verified)
                                <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">Verified</span>
                            @else
                                <span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">Pending</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td style="padding: 1rem;">
                            @if(!$user->is_verified)
                                {{-- FIXED: Added 'admin.' prefix to route name --}}
                                <form action="{{ route('admin.users.verify', $user) }}" method="POST" style="display: inline-block; margin-right: 5px;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="background: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Verify</button>
                                </form>
                            @endif

                            {{-- FIXED: Added 'admin.' prefix to route name --}}
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: #888;">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection