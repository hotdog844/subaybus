@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-card .icon {
            font-size: 2.5rem;
            color: #3498db;
        }
        .stat-card .info .number {
            font-size: 2rem;
            font-weight: bold;
        }
        .stat-card .info .label {
            color: #777;
        }
    </style>

    <p>Welcome to the M-Bus Tracker administration panel. Here is a summary of your system data.</p>

    <div class="stats-grid">
        {{-- Total Users Card --}}
        <div class="stat-card">
            <div class="icon">👥</div>
            <div class="info">
                <div class="number">{{ $stats['users'] }}</div>
                <div class="label">Total Users</div>
            </div>
        </div>

        {{-- Total Drivers Card --}}
        <div class="stat-card">
            <div class="icon">🚚</div>
            <div class="info">
                <div class="number">{{ $stats['drivers'] }}</div>
                <div class="label">Total Drivers</div>
            </div>
        </div>

        {{-- Total Buses Card --}}
        <div class="stat-card">
            <div class="icon">🚌</div>
            <div class="info">
                <div class="number">{{ $stats['buses'] }}</div>
                <div class="label">Total Buses</div>
            </div>
        </div>

        {{-- Total Routes Card --}}
        <div class="stat-card">
            <div class="icon">🛣️</div>
            <div class="info">
                <div class="number">{{ $stats['routes'] }}</div>
                <div class="label">Defined Routes</div>
            </div>
        </div>

        {{-- New Feedback Card --}}
        <div class="stat-card">
            <div class="icon">💬</div>
            <div class="info">
                <div class="number">{{ $stats['feedback'] }}</div>
                <div class="label">New Feedback</div>
            </div>
        </div>
    </div>
@endsection