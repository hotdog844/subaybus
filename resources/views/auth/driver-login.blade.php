<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login - M-Bus Tracker</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { font-family: system-ui, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; }
        .login-container { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 420px; }
        h2 { text-align: center; color: #1c1e21; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #606770; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #dddfe2; border-radius: 6px; box-sizing: border-box; font-size: 1rem; }
        .btn { width: 100%; padding: 0.75rem; background-color: #2c3e50; color: white; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Driver Portal Login</h2>
        <form method="POST" action="{{ route('driver.login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <p style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>