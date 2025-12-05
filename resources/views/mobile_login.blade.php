<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - SubayBus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --primary-green: #00b894; /* Vibrant Green from image */
      --dark-green: #008b70;
      --text-dark: #2d3436;
      --text-light: #636e72;
      --bg-light: #f4f6f8;
      --white: #ffffff;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: var(--bg-light);
      height: 100vh;
      overflow: hidden; /* Prevent scrolling, map handles it */
      display: flex;
      flex-direction: column;
    }

    /* --- MAP LAYER (Background) --- */
    .map-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      background-color: #eef2f3;
      /* CSS Grid Pattern to simulate the map in your image */
      background-image: 
        linear-gradient(#e0e0e0 1px, transparent 1px),
        linear-gradient(90deg, #e0e0e0 1px, transparent 1px);
      background-size: 40px 40px;
    }

    /* --- TOP SEARCH BAR --- */
    .top-search-bar {
      position: absolute;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      width: 90%;
      max-width: 400px;
      z-index: 10;
    }

    .search-input {
      width: 100%;
      padding: 15px 20px 15px 45px;
      border: none;
      border-radius: 30px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      font-size: 1rem;
      outline: none;
      box-sizing: border-box; /* Ensures padding doesn't break width */
    }

    .search-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
    }

    /* --- FLOATING CONTROLS (Zoom) --- */
    .map-controls {
      position: absolute;
      right: 20px;
      top: 100px;
      z-index: 10;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .control-btn {
      width: 45px;
      height: 45px;
      background: var(--white);
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      color: var(--text-dark);
      font-size: 1.2rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* --- TRIP PROGRESS CARD (The Green Card) --- */
    .trip-card {
      position: absolute;
      bottom: 90px; /* Above nav bar */
      left: 50%;
      transform: translateX(-50%);
      width: 90%;
      max-width: 380px;
      background: var(--white);
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
      z-index: 10;
      overflow: hidden;
      animation: slideUp 0.5s ease-out;
    }

    .trip-header {
      background: var(--primary-green);
      color: var(--white);
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .bus-badge {
      background: rgba(255,255,255,0.2);
      width: 45px;
      height: 45px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      margin-right: 15px;
    }

    .trip-info h3 { margin: 0; font-size: 1.1rem; }
    .trip-info span { font-size: 0.9rem; opacity: 0.9; }

    .refresh-time { font-size: 0.75rem; display: flex; align-items: center; gap: 5px; }

    .trip-body { padding: 20px; }

    .stop-item {
      display: flex;
      align-items: flex-start;
      margin-bottom: 20px;
      position: relative;
    }

    .icon-box {
      width: 40px;
      height: 40px;
      background: #e0f7fa;
      color: var(--primary-green);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      flex-shrink: 0;
    }

    .stop-details h4 { margin: 0 0 5px 0; color: var(--text-dark); font-size: 1rem; }
    .stop-details p { margin: 0; color: var(--text-light); font-size: 0.85rem; }

    .progress-section { margin-top: 10px; }
    .progress-label {
      display: flex;
      justify-content: space-between;
      font-size: 0.85rem;
      color: var(--text-light);
      margin-bottom: 8px;
    }
    .progress-bar {
      width: 100%;
      height: 8px;
      background: #eee;
      border-radius: 4px;
      overflow: hidden;
    }
    .progress-fill {
      height: 100%;
      background: var(--primary-green);
      width: 25%; /* 3 of 12 stops is 25% */
    }

    /* --- BOTTOM NAV BAR --- */
    .bottom-nav {
      position: absolute;
      bottom: 0;
      width: 100%;
      background: var(--white);
      padding: 15px 0;
      display: flex;
      justify-content: space-around;
      border-top-left-radius: 25px;
      border-top-right-radius: 25px;
      box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
      z-index: 20;
    }

    .nav-item {
      text-align: center;
      color: #b2bec3;
      text-decoration: none;
      font-size: 0.8rem;
    }
    .nav-item.active { color: var(--primary-green); }
    .nav-item i { font-size: 1.4rem; display: block; margin-bottom: 5px; }

    @keyframes slideUp {
      from { transform: translate(-50%, 100%); }
      to { transform: translate(-50%, 0); }
    }
  </style>
</head>
<body>

  <div class="map-container">
    </div>

  <div class="top-search-bar">
    <i class="fas fa-search search-icon"></i>
    <input type="text" class="search-input" placeholder="Search routes, stops, locations...">
  </div>

  <div class="map-controls">
    <button class="control-btn"><i class="fas fa-plus"></i></button>
    <button class="control-btn"><i class="fas fa-minus"></i></button>
  </div>

  <div class="trip-card">
    <div class="trip-header">
      <div style="display:flex; align-items:center;">
        <div class="bus-badge"><i class="fas fa-bus"></i></div>
        <div class="trip-info">
          <h3>Bus 42 — Downtown</h3>
          <span>Express Route</span>
        </div>
      </div>
      <div class="refresh-time">
        <i class="fas fa-sync-alt"></i> 2 min ago
      </div>
    </div>

    <div class="trip-body">
      <div class="stop-item">
        <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
        <div class="stop-details">
          <h4>Next Stop: Central Station</h4>
          <p>Stop #2847</p>
        </div>
      </div>

      <div class="stop-item">
        <div class="icon-box"><i class="fas fa-clock"></i></div>
        <div class="stop-details">
          <h4>Estimated Arrival</h4>
          <p>3:45 PM (in 5 min)</p>
        </div>
      </div>

      <div class="progress-section">
        <div class="progress-label">
          <span>Progress</span>
          <span>3 of 12 stops</span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill"></div>
        </div>
      </div>
    </div>
  </div>

  <nav class="bottom-nav">
    <a href="#" class="nav-item active">
      <i class="fas fa-home"></i> Home
    </a>
    <a href="#" class="nav-item">
      <i class="fas fa-bus"></i> Bus
    </a>
    <a href="#" class="nav-item">
      <i class="fas fa-user"></i> Profile
    </a>
    <a href="#" class="nav-item">
      <i class="fas fa-cog"></i> Settings
    </a>
  </nav>

</body>
</html>