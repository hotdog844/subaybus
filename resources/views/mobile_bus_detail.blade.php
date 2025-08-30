<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Details - {{ $bus->plate_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        :root {
            --primary-color: #1EEA92;
            --header-bg: #0A5C36;
            --text-dark: #222;
            --text-light: #6c757d;
            --page-bg: #f4f7fa;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
            --shadow-color: rgba(0, 0, 0, 0.08);
            --star-color: #FFD700;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); color: var(--text-dark); }
        .main-container { padding-bottom: 80px; }
        .header { background-color: var(--header-bg); color: white; padding: 1.2rem; text-align: center; position: sticky; top: 0; z-index: 1000; }
        .header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; letter-spacing: 1px; }
        .content-padding { padding: 1rem; }
        .detail-card { background: var(--card-bg); border-radius: 16px; margin-bottom: 1.2rem; box-shadow: 0 6px 20px var(--shadow-color); padding: 1.5rem; }
        .detail-card h3 { margin-top: 0; font-size: 1.1rem; font-weight: 600; border-bottom: 1px solid var(--border-color); padding-bottom: 0.8rem; margin-bottom: 1rem; }
        .detail-row { display: flex; justify-content: space-between; font-size: 0.95rem; margin-bottom: 0.8rem; }
        .detail-row .label { color: var(--text-light); }
        .detail-row .value { font-weight: 500; text-align: right; }
        .status-pill { background-color: var(--primary-color); color: white; font-size: 0.8rem; font-weight: 500; padding: 0.3rem 0.8rem; border-radius: 50px; }
        #detail-map { height: 250px; width: 100%; border-radius: 12px; margin-top: 1rem; }
        
        /* New Styles for Review Form */
        .rating-stars { display: flex; justify-content: center; gap: 1rem; font-size: 2.5rem; color: #ccc; margin-top: 1rem; }
        .rating-stars i { cursor: pointer; transition: color 0.2s, transform 0.2s; }
        .rating-stars i:hover { transform: scale(1.2); }
        .rating-stars i.selected { color: var(--star-color); }
        #review-form textarea { width: 100%; min-height: 80px; border-radius: 8px; border: 1px solid var(--border-color); padding: 0.8rem; margin-top: 1rem; font-family: 'Poppins', sans-serif; font-size: 1rem; }
        #review-form .btn-submit { display: block; width: 100%; text-align: center; margin-top: 1rem; background-color: var(--primary-color); color: white; padding: 0.8rem; border-radius: 50px; text-decoration: none; font-weight: 500; font-size: 1rem; border: none; cursor: pointer; }

        .bottom-nav { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--card-bg); border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; padding: 0.5rem 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 9999; }
        .nav-item { text-align: center; color: var(--text-light); font-size: 0.8rem; flex: 1; text-decoration: none; }
        .nav-item i { display: block; font-size: 1.3rem; margin-bottom: 4px; }
        .nav-item.active { color: var(--header-bg); }
    </style>
</head>
<body>
    <div class="main-container">
        <header class="header"><h1>SubayBus 🚌</h1></header>
        <div class="content-padding">

            <div class="detail-card">
                <h3>Bus Details</h3>
                <div class="detail-row"><span class="label">Plate</span><span class="value">{{ $bus->plate_number }}</span></div>
                <div class="detail-row"><span class="label">Driver</span><span class="value">{{ $bus->driver->name ?? 'Unassigned' }}</span></div>
                <div class="detail-row"><span class="label">Route</span><span class="value">{{ $bus->route_name }}</span></div>
                <div class="detail-row"><span class="label">Status</span><span class="value"><span class="status-pill">{{ ucfirst($bus->status) }}</span></span></div>
            </div>

            <div class="detail-card">
                <h3>Live Info</h3>
                <div class="detail-row"><span class="label">Distance</span><span class="value">{{ $bus->distance_km }} km</span></div>
                <div class="detail-row"><span class="label">Last seen</span><span class="value">{{ $bus->last_seen ? $bus->last_seen->diffForHumans() : 'N/A' }}</span></div>
                <div class="detail-row"><span class="label">ETA</span><span class="value">{{ $bus->eta_minutes }} min</span></div>
            </div>

            <div class="detail-card">
                <h3>Fare</h3>
                <div class="detail-row"><span class="label">Regular</span><span class="value">₱{{ number_format($bus->fare, 2) }}</span></div>
                <div class="detail-row"><span class="label">Discounted</span><span class="value">₱{{ number_format($bus->fare * 0.8, 2) }}</span></div>
            </div>
            
            <div class="detail-card">
                <h3>Location</h3>
                <div id="detail-map"></div>
            </div>

            {{-- Only show the rating form to logged-in users --}}
            @auth
            <div class="detail-card">
                <h3>Rate and Review this Bus</h3>
                <form id="review-form" data-bus-id="{{ $bus->id }}">
                    <div class="rating-stars">
                        <i class="far fa-star" data-value="1"></i>
                        <i class="far fa-star" data-value="2"></i>
                        <i class="far fa-star" data-value="3"></i>
                        <i class="far fa-star" data-value="4"></i>
                        <i class="far fa-star" data-value="5"></i>
                    </div>
                    <textarea name="comment" placeholder="Tell us about your experience..." required></textarea>
                    <input type="hidden" name="rating" value="0">
                    <button type="submit" class="btn-submit">Submit Review</button>
                    <p id="rating-feedback" style="text-align:center; margin-top:1rem;"></p>
                </form>
            </div>
            @else
            <div class="detail-card" style="text-align:center;">
                <p><a href="{{ route('login') }}">Log in</a> to rate and review this bus.</p>
            </div>
            @endauth

        </div>
    </div>

    <nav class="bottom-nav">
    <a href="{{ route('home') }}" class="nav-item">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="#" onclick="goToLastViewedBus(); return false;" class="nav-item">
        <i class="fas fa-bus"></i>
        <span>Bus</span>
    </a>
    <a href="{{ route('route.planner') }}" class="nav-item">
        <i class="fas fa-map-signs"></i>
        <span>Planner</span>
    </a>
    <a href="{{ route('profile.show') }}" class="nav-item">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>
    <a href="{{ route('settings') }}" class="nav-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
    </a>
</nav>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Map Initialization ---
            const busPosition = [{{ $bus->latitude ?? '11.5833' }}, {{ $bus->longitude ?? '122.75' }}];
            const detailMap = L.map('detail-map').setView(busPosition, 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(detailMap);
            const busIcon = L.icon({
                iconUrl: '{{ asset("images/bus-green.png") }}',
                iconSize: [35, 35], iconAnchor: [17, 35]
            });
            const sampleRouteLine = [
                [11.5833, 122.75], [11.5850, 122.7520], [11.5865, 122.7550],
                [11.5880, 122.7580], [11.5895, 122.7600], [11.5910, 122.7620]
            ];
            L.polyline(sampleRouteLine, {color: 'blue'}).addTo(detailMap);
            L.marker(busPosition, { icon: busIcon }).addTo(detailMap);

            function goToLastViewedBus() {
                const lastBusId = localStorage.getItem('lastViewedBusId');
                if (lastBusId) { window.location.href = `/bus/${lastBusId}`; } 
                else { alert('Please select a bus from the list first to see its details.'); }
            }

            @auth
            // --- Star Rating & Comment Logic ---
            const reviewForm = document.getElementById('review-form');
            if (reviewForm) {
                const stars = reviewForm.querySelectorAll('.rating-stars i');
                const ratingInput = reviewForm.querySelector('input[name="rating"]');
                const commentInput = reviewForm.querySelector('textarea[name="comment"]');
                const feedbackMsg = document.getElementById('rating-feedback');

                stars.forEach(star => {
                    star.addEventListener('click', () => {
                        const rating = star.dataset.value;
                        ratingInput.value = rating;
                        // Update stars UI
                        stars.forEach(s => {
                            if (s.dataset.value <= rating) {
                                s.classList.replace('far', 'fas');
                                s.classList.add('selected');
                            } else {
                                s.classList.replace('fas', 'far');
                                s.classList.remove('selected');
                            }
                        });
                    });
                });

                reviewForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    const busId = this.dataset.busId;
                    const rating = ratingInput.value;
                    const comment = commentInput.value;
                    
                    if (rating == 0) {
                        feedbackMsg.textContent = 'Please select a star rating.';
                        feedbackMsg.style.color = 'red';
                        return;
                    }
                    
                    feedbackMsg.textContent = 'Submitting...';
                    feedbackMsg.style.color = 'inherit';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    try {
                        const response = await fetch(`/bus/${busId}/review`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                rating: rating,
                                comment: comment
                            })
                        });

                        const result = await response.json();
                        
                        if(response.ok) {
                            feedbackMsg.textContent = result.message;
                            feedbackMsg.style.color = 'green';
                            reviewForm.querySelector('button').disabled = true;
                            commentInput.disabled = true;
                        } else {
                            feedbackMsg.textContent = result.message || 'An error occurred.';
                            feedbackMsg.style.color = 'red';
                        }
                    } catch (error) {
                        feedbackMsg.textContent = 'A network error occurred.';
                        feedbackMsg.style.color = 'red';
                    }
                });
            }
            @endauth
        });
    </script>
</body>
</html>