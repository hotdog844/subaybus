<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Subaybus</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
        body { font-family: 'Roboto', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; text-align: center; color: #333; }
        .verify-container { background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); max-width: 500px; }
        h2 { color: #0A5C36; }
        .message { margin-bottom: 1.5rem; }
        .resend-btn { background-color: #0A5C36; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 25px; cursor: pointer; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="verify-container">
        <h2>Verify Your Email Address</h2>
        <p class="message">
            Thanks for signing up! Before you can access the dashboard, you need to verify your email address by clicking on the link we just emailed to you.
        </p>
        <p class="message">
            If you didn't receive the email, we will gladly send you another.
        </p>

        @if (session('message'))
            <div style="color: green; margin-bottom: 1rem;">
                {{ session('message') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="resend-btn">Resend Verification Email</button>
        </form>
    </div>
</body>
</html>