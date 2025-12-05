<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SubayBus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        
        :root {
            --primary-color: #00D084;
            --primary-dark: #0A5C36;
            --text-dark: #222;
            --text-light: #666;
            --bg-color: #ffffff;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .slider-container {
            flex: 1;
            display: flex;
            overflow-x: hidden;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            position: relative;
        }

        .slide {
            min-width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            box-sizing: border-box;
            scroll-snap-align: start;
            transition: transform 0.5s ease; 
        }

        .illustration {
            font-size: 8rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        .slide h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1rem;
        }

        .slide p {
            font-size: 1rem;
            color: var(--text-light);
            line-height: 1.6;
            max-width: 300px;
        }

        .controls {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .dots {
            display: flex;
            gap: 0.5rem;
        }

        .dot {
            width: 10px;
            height: 10px;
            background-color: #ddd;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .dot.active {
            background-color: var(--primary-color);
            width: 25px;
            border-radius: 5px;
        }

        .btn-start {
            width: 100%;
            max-width: 300px;
            padding: 1rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 208, 132, 0.4);
            transition: transform 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-start:hover {
            transform: scale(1.02);
            background-color: #00b874;
        }
        
        .skip-link {
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="slider-container" id="slider">
        <div class="slide">
            <div class="illustration">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <h2>Track in Real-Time</h2>
            <p>See exactly where your bus is on the map. No more guessing or endless waiting.</p>
        </div>

        <div class="slide">
            <div class="illustration">
                <i class="fas fa-map-signs"></i>
            </div>
            <h2>Find Nearby Stops</h2>
            <p>Locate the nearest bus stops based on your current location with a single tap.</p>
        </div>

        <div class="slide">
            <div class="illustration">
                <i class="fas fa-route"></i>
            </div>
            <h2>Plan Your Journey</h2>
            <p>Check routes, fares, and estimated arrival times to plan your trip efficiently.</p>
        </div>
    </div>

    <div class="controls">
        <div class="dots" id="dots">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <a href="#" class="btn-start" id="actionBtn">
            Next <i class="fas fa-arrow-right"></i>
        </a>
        
        <a href="{{ route('home') }}" class="skip-link" id="skipBtn">Skip</a>
    </div>

    <script>
        const slider = document.getElementById('slider');
        const dots = document.querySelectorAll('.dot');
        const actionBtn = document.getElementById('actionBtn');
        const skipBtn = document.getElementById('skipBtn');
        let currentSlide = 0;
        const totalSlides = 3;

        // Define the final destination URL
        const homeUrl = "{{ route('home') }}";

        // Function to update UI
        function updateSlider() {
            // Scroll to the correct slide
            slider.scrollTo({
                left: slider.clientWidth * currentSlide,
                behavior: 'smooth'
            });

            // Update dots
            dots.forEach((dot, index) => {
                if (index === currentSlide) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            // Update button text based on slide
            if (currentSlide === totalSlides - 1) {
                actionBtn.innerHTML = 'Get Started <i class="fas fa-check"></i>';
                skipBtn.style.display = 'none';
            } else {
                actionBtn.innerHTML = 'Next <i class="fas fa-arrow-right"></i>';
                skipBtn.style.display = 'block';
            }
        }

        // Handle Button Click
        actionBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Always stop the link from navigating immediately

            if (currentSlide < totalSlides - 1) {
                // If not the last slide, go to next
                currentSlide++;
                updateSlider();
            } else {
                // If it IS the last slide, go to Home/Login
                window.location.href = homeUrl;
            }
        });

        // Handle Window Resize (keeps slider aligned)
        window.addEventListener('resize', () => {
            slider.scrollTo({ left: slider.clientWidth * currentSlide });
        });

        // Initialize
        updateSlider();
    </script>
</body>
</html>