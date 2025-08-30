<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Passenger Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f8;
      color: #333;
    }

    .mobile-navbar {
      background-color: #007bff;
      color: #fff;
      padding: 1rem;
      font-size: 1.2rem;
      font-weight: bold;
      text-align: center;
    }

    .form-container {
      max-width: 420px;
      margin: 2rem auto;
      padding: 2rem 1rem;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    label {
      display: block;
      margin-bottom: 0.4rem;
      font-weight: 500;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"],
    select {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    .btn {
      width: 100%;
      padding: 0.75rem;
      background-color: #007bff;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 1rem;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    .text-center {
      text-align: center;
      margin-top: 1rem;
    }

    .text-center a {
      color: #007bff;
      text-decoration: none;
    }

    .error {
      color: red;
      font-size: 0.9rem;
      margin-top: -1rem;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>

  <div class="mobile-navbar">📝 Register as Passenger</div>

  <div class="form-container">
    <form method="POST" action="{{ route('mobile.register.submit') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="text" name="phone" value="{{ old('phone') }}" required>
        @error('phone') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation" required>
      </div>

      <div class="form-group">
        <label for="passenger_type">Passenger Type</label>
        <select name="passenger_type" required>
          <option value="">Select...</option>
          <option value="regular">Regular</option>
          <option value="student">Student</option>
          <option value="senior">Senior Citizen</option>
          <option value="pwd">Person with Disability (PWD)</option>
        </select>
        @error('passenger_type') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label for="id_card">Upload ID for Verification</label>
        <input type="file" name="id_card" accept="image/*" required>
        @error('id_card') <div class="error">{{ $message }}</div> @enderror
      </div>

      <button type="submit" class="btn">Register</button>

      <div class="text-center">
        Already have an account? <a href="{{ route('mobile.login') }}">Login</a>
      </div>
    </form>
  </div>

</body>
</html>
