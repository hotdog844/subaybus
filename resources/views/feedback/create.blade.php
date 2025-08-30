<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support - SubayBus</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        :root {
            --primary-color: #00D084;
            --header-bg: #0A5C36;
            --text-dark: #222;
            --page-bg: #f4f7fa;
            --border-color: #e9ecef;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); }
        .header { background-color: var(--header-bg); color: white; padding: 1.2rem; text-align: center; display: flex; align-items: center; }
        .header .back-arrow { font-size: 1.5rem; color: white; text-decoration: none; margin-right: 1rem; }
        .header h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
        .content-padding { padding: 2rem 1rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-dark); }
        input, textarea { width: 100%; padding: 0.9rem 1.2rem; border: 1px solid var(--border-color); border-radius: 50px; font-size: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        textarea { border-radius: 16px; min-height: 120px; }
        .btn-submit { width: 100%; padding: 1rem; background-color: var(--primary-color); color: white; border: none; border-radius: 50px; font-size: 1.1rem; font-weight: 500; cursor: pointer; box-shadow: 0 4px 15px rgba(0, 208, 132, 0.4); margin-top: 1rem; }
        .error { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
    </style>
</head>
<body>
    <header class="header">
        <a href="{{ route('settings') }}" class="back-arrow">&larr;</a>
        <h1>Contact & Feedback</h1>
    </header>

    <div class="content-padding">
        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('feedback.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="email">Your Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required>
                @error('subject') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                @error('message') <p class="error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-submit">Submit Feedback</button>
        </form>
    </div>
</body>
</html>