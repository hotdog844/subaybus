<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <style>
        body { font-family: system-ui, sans-serif; background-color: #f4f7fa; color: #333; margin: 0; padding: 1.5rem; }
        .dashboard-container { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1rem; }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .logout-btn { background: #e74c3c; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; text-decoration: none; }
        .info-card { background: #f9f9f9; border: 1px solid #eee; border-radius: 8px; padding: 1.5rem; margin-top: 1.5rem; }
        .info-card h2 { margin-top: 0; }
        .info-row { display: flex; justify-content: space-between; padding: 0.5rem 0; }
        .status-controls { display: flex; justify-content: space-between; gap: 1rem; margin-top: 1rem; }
        .status-controls button { flex: 1; padding: 0.75rem; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; color: white; }
        .btn-start { background-color: #2ecc71; }
        .btn-end { background-color: #f39c12; }
        .btn-offline { background-color: #95a5a6; }
        .message { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Driver Dashboard</h1>
            <a href="{{ route('driver.logout') }}" 
               class="logout-btn" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               Logout
            </a>
            <form id="logout-form" action="{{ route('driver.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="message error">{{ session('error') }}</div>
        @endif

        <p>Welcome, <strong>{{ $driver->name }}</strong>.</p>

        <div class="info-card">
            <h2>Your Assigned Bus</h2>
            @if ($bus)
                <div class="info-row">
                    <span>Plate Number:</span>
                    <strong>{{ $bus->plate_number }}</strong>
                </div>
                <div class="info-row">
                    <span>Route:</span>
                    <strong>{{ $bus->route_name }}</strong>
                </div>
                <div class="info-row">
                    <span>Current Status:</span>
                    <strong style="text-transform: capitalize;">{{ $bus->status }}</strong>
                </div>

                <div class="status-controls">
                    <form action="{{ route('driver.status.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="on route">
                        <button type="submit" class="btn-start">Start Trip</button>
                    </form>
                    <form action="{{ route('driver.status.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="at terminal">
                        <button type="submit" class="btn-end">End Trip</button>
                    </form>
                    <form action="{{ route('driver.status.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="offline">
                        <button type="submit" class="btn-offline">Go Offline</button>
                    </form>
                </div>
            @else
                <p>You are not currently assigned to a bus.</p>
            @endif
        </div>
    </div>
</body>
</html>