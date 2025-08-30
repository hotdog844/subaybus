<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - SubayBus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        :root {
            --primary-color: #1EEA92; 
            --header-text-color: #0A5C36; 
            --text-dark: #222;
            --text-light: #6c757d;
            --page-bg: #f4f7fa;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); color: var(--text-dark); }
        .header {
            background-color: var(--card-bg);
            color: var(--header-text-color);
            padding: 1.2rem;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .content-padding { padding: 1.5rem; }
        
        .profile-container {
            text-align: center;
        }
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--header-text-color);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            border: 4px solid var(--card-bg);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-top: -60px; 
            position: relative; 
            z-index: 2; 
        }
        .profile-name { font-size: 1.5rem; font-weight: 700; margin: 1rem 0 0.2rem 0; }
        .profile-type { color: var(--text-light); margin-bottom: 2rem; }

        .info-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            text-align: left;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.1rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: var(--text-light); font-size: 0.9rem; }
        .info-row .value { font-weight: 500; font-size: 0.9rem; }
        
        .edit-profile-btn {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 2rem;
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(0, 208, 132, 0.4);
        }

        .bottom-nav { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--card-bg); border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; padding: 0.5rem 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 9999; }
        .nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; color: var(--text-light); font-size: 0.75rem; text-decoration: none; padding: 0.2rem 0; position: relative; transition: color 0.3s ease; }
        .nav-item:hover { color: var(--header-bg); }
        .nav-item i { font-size: 1.4rem; margin-bottom: 5px; }
        .nav-item.active { color: var(--header-bg); font-weight: 500; }
        .nav-item.active::before { content: ''; position: absolute; top: -1px; left: 50%; transform: translateX(-50%); width: 30px; height: 3px; background-color: var(--header-bg); border-radius: 2px; }
    </style>
</head>
<body>
    <header class="header">Profile</header>

    <div class="content-padding">
        <div class="profile-container">
            <div class="profile-picture">
                {{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}
            </div>
            <h2 class="profile-name">{{ Auth::user()->name }}</h2>
            <p class="profile-type">{{ Auth::user()->passenger_type }} Passenger</p>

            <div class="info-card">
                <div class="info-row">
                    <span class="label">Phone</span>
                    <span class="value">{{ Auth::user()->phone }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Email</span>
                    <span class="value">{{ Auth::user()->email }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Joined</span>
                    <span class="value">{{ Auth::user()->created_at->format('F Y') }}</span>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}" class="edit-profile-btn">Edit Profile</a>
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

    <script>
        function goToLastViewedBus() {
            const lastBusId = localStorage.getItem('lastViewedBusId');
            if (lastBusId) {
                window.location.href = `/bus/${lastBusId}`;
            } else {
                alert('Please select a bus from the list first to see its details.');
            }
        }
    </script>
</body>
</html>