<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SubayBus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --primary-color: #0A5C36;
            --primary-hover: #0F5132;
            --page-bg: #f8f9fa;
            --form-bg: #ffffff;
            --text-dark: #212529;
            --text-light: #6c757d;
            --border-color: #dee2e6;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--page-bg);
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }

        .login-container {
            background-color: var(--form-bg);
            width: 100%;
            max-width: 420px;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px var(--shadow-color);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .login-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0 0 0.5rem 0;
        }
        .login-header p {
            color: var(--text-light);
            margin: 0;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .input-group .input-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }
        .input-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3.5rem;
            border: 1px solid var(--border-color);
            border-radius: 12px; /* Smoother rounding */
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-group input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(10, 92, 54, 0.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        .form-options .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
        }
        .form-options a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .form-options a:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.95rem;
        }
        .register-link a {
            color: var(--primary-color);
            font-weight: 700;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .error-message { color: #dc3545; font-size: 0.875rem; margin-top: -1rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>SubayBus 🚌</h1>
            <p>Welcome back! Please log in to your account.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus>
            </div>
            @error('email')<p class="error-message">{{ $message }}</p>@enderror

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input id="password" type="password" name="password" placeholder="Password" required>
            </div>
            @error('password')<p class="error-message">{{ $message }}</p>@enderror

            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="#">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary shadow-lg shadow-green-100 mb-6">Login</button>
        </form>
        
        <p class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Sign up</a>
        </p>

        <div class="mt-8 pt-8 border-t border-gray-100">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] text-center mb-4">Partner with SubayBus</p>
            
            <a href="{{ route('public.driver.register') }}" 
               class="flex items-center justify-between w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl group hover:bg-[#f0f8f4] hover:border-green-200 transition-all active:scale-[0.98]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-green-700 group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <i class="fas fa-id-card text-lg"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-[13px] font-bold text-gray-800">Apply as a Driver</p>
                        <p class="text-[10px] text-gray-400 font-medium">Earn by joining our fleet</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-300 group-hover:text-green-500 transition-colors"></i>
            </a>
        </div>
    </div>
</body>
</html>