<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - SubayBus</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        :root {
            --header-bg: #0A5C36;
            --text-dark: #222;
            --text-light: #6c757d;
            --page-bg: #f4f7fa;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); line-height: 1.6; }
        .header { background-color: var(--header-bg); color: white; padding: 1.2rem; text-align: center; display: flex; align-items: center; }
        .header .back-arrow { font-size: 1.5rem; color: white; text-decoration: none; margin-right: 1rem; }
        .header h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
        .faq-container { padding: 2rem 1.5rem; }
        .faq-item { background: var(--card-bg); border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .faq-question { font-weight: 600; font-size: 1.1rem; margin-top: 0; margin-bottom: 0.8rem; color: var(--header-bg); }
        .faq-answer { color: var(--text-light); margin: 0; }
    </style>
</head>
<body>
    <header class="header">
        <a href="{{ route('settings') }}" class="back-arrow">&larr;</a>
        <h1>FAQs</h1>
    </header>

    <div class="faq-container">
        <div class="faq-item">
            <p class="faq-question">How accurate is the bus location tracking?</p>
            <p class="faq-answer">The bus locations are updated approximately every 15 seconds. Accuracy depends on the GPS signal, but it is generally very reliable for tracking the bus's progress along its route.</p>
        </div>
        <div class="faq-item">
            <p class="faq-question">What do the different bus statuses mean?</p>
            <p class="faq-answer">
                <strong>On Route:</strong> The bus is actively traveling.<br>
                <strong>At Terminal:</strong> The bus is at a terminal.<br>
                <strong>Offline:</strong> The bus is not currently sending location data.
            </p>
        </div>
        <div class="faq-item">
            <p class="faq-question">Why do I need to create an account?</p>
            <p class="faq-answer">Creating an account allows you to save your profile, submit ratings and detailed feedback, and access future features like trip history.</p>
        </div>
    </div>
</body>
</html>