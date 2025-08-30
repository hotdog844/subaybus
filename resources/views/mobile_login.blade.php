<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Roxas City M-Bus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f8;
    }
    .container {
      max-width: 400px;
      margin: 3rem auto;
      background: #fff;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    h2 {
      text-align: center;
      color: #007bff;
      margin-bottom: 1.5rem;
    }
    input {
      width: 100%;
      padding: 0.8rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      width: 100%;
      padding: 0.9rem;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      background: #007bff;
      color: white;
      font-size: 1rem;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
    p {
      text-align: center;
      margin-top: 1rem;
    }
    a {
      color: #007bff;
      text-decoration: none;
      font-weight: bold;
    }
    .logo {
      font-size: 2.5rem;
      text-align: center;
      margin-bottom: 1rem;
    }
    .error {
      color: red;
      font-size: 0.9rem;
      margin-bottom: 1rem;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">🚌</div>
    <h2>Login to SubayBus Roxas City</h2>

    @if ($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('mobile.login.submit') }}">
      @csrf
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <p>Don’t have an account? <a href="{{ route('mobile.register') }}">Register here</a></p>
  </div>
</body>
</html>
