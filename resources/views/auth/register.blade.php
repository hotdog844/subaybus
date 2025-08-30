<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Subaybus</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        :root {
            --primary-color: #0A5C36;
            --primary-hover: #0F5132;
            --background-color: #f0f2f5;
            --form-background: #ffffff;
            --text-color: #1D2E28;
            --label-color: #14452F;
            --border-color: #dddfe2;
            --error-color: #dc3545;
            --input-bg: #f9f9f9;
        }

        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--background-color);
            margin: 0;
            padding: 1rem;
        }

        .register-container {
            background: var(--form-background);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 450px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-color);
            margin: 0;
        }

        .register-header p {
            color: var(--label-color);
            margin-top: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--label-color);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.15);
        }

        .btn-register {
            width: 100%;
            padding: 0.9rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 1rem;
        }

        .btn-register:hover {
            background-color: var(--primary-hover);
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.95rem;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2>Register</h2>
            <p>Join Subaybus to start tracking.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required>
                @error('first_name') <p class="error-message">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required>
                @error('last_name') <p class="error-message">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email') <p class="error-message">{{ $message }}</p> @enderror
            </div>
            
            <div class="form-group">
                <label for="passenger_type">Passenger Type</label>
                <select name="passenger_type" id="passenger_type" required>
                    <option value="Regular" @if(old('passenger_type') == 'Regular') selected @endif>Regular</option>
                    <option value="Student" @if(old('passenger_type') == 'Student') selected @endif>Student</option>
                    <option value="Senior Citizen" @if(old('passenger_type') == 'Senior Citizen') selected @endif>Senior Citizen</option>
                </select>
                @error('passenger_type') <p class="error-message">{{ $message }}</p> @enderror
            </div>
            
            <div class="form-group">
                <label for="id_image">Upload Valid ID</label>
                <input id="id_image" type="file" name="id_image" accept="image/*" required>
                <span id="fileName" style="font-size: 0.9rem; color: #555; margin-left: 10px;"></span>
                @error('id_image') <p class="error-message">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password') <p class="error-message">{{ $message }}</p @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn-register">Create Account</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="{{ route('login') }}">Log In</a></p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('id_image');
            const fileNameDisplay = document.getElementById('fileName');

            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    fileNameDisplay.textContent = fileInput.files[0].name;
                } else {
                    fileNameDisplay.textContent = '';
                }
            });
        });
    </script>
</body>
</html>