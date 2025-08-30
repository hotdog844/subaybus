<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Route Planner - SubayBus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); color: var(--text-dark); }
        .header { background-color: var(--header-bg); color: white; padding: 1.2rem; display: flex; align-items: center; position: sticky; top: 0; z-index: 1000; }
        .header .back-arrow { font-size: 1.5rem; color: white; text-decoration: none; margin-right: 1rem; }
        .header h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
        .content-padding { padding: 1.5rem; }

        .planner-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 6px 20px var(--shadow-color);
        }
        .input-group {
            position: relative;
            margin-bottom: 1rem;
        }
        .input-group i {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }
        .input-group input {
            width: 100%;
            padding: 0.9rem 1.2rem 0.9rem 3.5rem;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            font-size: 1rem;
        }
        .btn-find-route {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 208, 132, 0.4);
            margin-top: 1rem;
        }

        /* Styles for the results */
        #results-container {
            display: none; /* Hidden by default */
            margin-top: 2rem;
        }
        .results-header {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        #results-map {
            height: 250px;
            width: 100%;
            border-radius: 16px;
            margin-bottom: 1.5rem;
        }
        .timeline {
            border-left: 3px solid var(--primary-color);
            padding-left: 1.5rem;
            position: relative;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -27px; /* Position on the timeline */
            top: 5px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: var(--card-bg);
            border: 3px solid var(--primary-color);
        }
        .timeline-item h3 { margin: 0 0 0.3rem 0; font-size: 1.1rem; }
        .timeline-item p { margin: 0; color: var(--text-light); font-size: 0.9rem; }
        
        .btn-save {
            width: 100%;
            padding: 1rem;
            background-color: var(--header-bg);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            margin-top: 1.5rem;
        }
        .bottom-nav { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--card-bg); border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; padding: 0.5rem 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 9999; }
        .nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; color: var(--text-light); font-size: 0.75rem; text-decoration: none; }
        .nav-item i { font-size: 1.4rem; margin-bottom: 5px; }
        .nav-item.active { color: var(--header-bg); font-weight: 500; }
    </style>
</head>
<body>

    <header class="header">
        <a href="{{ route('home') }}" class="back-arrow">&larr;</a>
        <h1>Route Planner</h1>
    </header>

    <div class="content-padding">
        <div class="planner-card">
            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" id="fromInput" placeholder="From: Current Location">
            </div>
            <div class="input-group">
                <i class="fas fa-flag-checkered"></i>
                <input type="text" id="toInput" placeholder="To: e.g., Robinsons Place Roxas">
            </div>
            <button class="btn-find-route" id="findRouteBtn">Find Route</button>
        </div>

        <div id="results-container">
            <h2 class="results-header">Suggested Route</h2>
            <div id="results-map"></div>
            <div class="planner-card">
                <div class="timeline">
                    <div class="timeline-item">
                        <h3>Your Location</h3>
                        <p>Walk 2 min (150m) to the nearest bus stop: Roxas City Plaza</p>
                    </div>
                    <div class="timeline-item">
                        <h3>Board Bus RXS-001 (11:15 AM)</h3>
                        <p>Route: To Lawaan (ETA: 5 min)</p>
                    </div>
                    <div class="timeline-item">
                        <h3>Passes 4 stops</h3>
                        <p>~ 12 minutes total travel time</p>
                    </div>
                    <div class="timeline-item">
                        <h3>Alight at Robinsons Place Roxas (11:27 AM)</h3>
                        <p>You have arrived at your destination.</p>
                    </div>
                </div>
                <button class="btn-save" onclick="alert('Trip Saved! (Prototype)')">Save & Get Directions</button>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="{{ route('home') }}" class="nav-item"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="#" onclick="goToLastViewedBus(); return false;" class="nav-item"><i class="fas fa-bus"></i><span>Bus</span></a>
        <a href="{{ route('route.planner') }}" class="nav-item active"><i class="fas fa-map-signs"></i><span>Planner</span></a>
        <a href="{{ route('profile.show') }}" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="{{ route('settings') }}" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
    </nav>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function goToLastViewedBus() {
            const lastBusId = localStorage.getItem('lastViewedBusId');
            if (lastBusId) { window.location.href = `/bus/${lastBusId}`; } 
            else { alert('Please tap on a bus from the list first to see its details.'); }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const findRouteBtn = document.getElementById('findRouteBtn');
            const resultsContainer = document.getElementById('results-container');
            let resultsMap = null;

            findRouteBtn.addEventListener('click', function() {
                resultsContainer.style.display = 'block';

                if (!resultsMap) {
                    resultsMap = L.map('results-map').setView([11.57, 122.752], 14);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(resultsMap);
                    
                    const startPoint = [11.5833, 122.7525]; // Roxas Plaza
                    const endPoint = [11.5585, 122.7533];   // Robinsons
                    const routePath = [startPoint, [11.5770, 122.7530], endPoint];
                    
                    L.marker(startPoint).addTo(resultsMap).bindPopup('Start');
                    L.marker(endPoint).addTo(resultsMap).bindPopup('Destination');
                    L.polyline(routePath, { color: '#0A5C36', weight: 6 }).addTo(resultsMap);
                }
            });
        });
    </script>
</body>
</html>