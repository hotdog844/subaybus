<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - SubayBus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        :root {
            --primary-color: #00D084;
            --header-bg: #0A5C36;
            --text-dark: #222;
            --text-light: #6c757d;
            --page-bg: #f4f7fa;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
            --danger-color: #dc3545;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); color: var(--text-dark); }
        .main-container { padding-bottom: 80px; }
        .header { background-color: var(--header-bg); color: white; padding: 1.2rem; display: flex; align-items: center; position: sticky; top: 0; z-index: 1000; }
        .header .back-arrow { font-size: 1.5rem; color: white; text-decoration: none; margin-right: 1rem; }
        .header h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
        .content-padding { padding: 1rem; }

        .settings-group { margin-bottom: 2rem; }
        .settings-title { font-size: 0.8rem; font-weight: 600; color: var(--text-light); text-transform: uppercase; padding: 0 0.5rem 0.5rem 0.5rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1rem; }
        
        .setting-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--card-bg);
            padding: 1rem;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        a.setting-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .setting-item .icon { color: var(--header-bg); margin-right: 1rem; width: 20px; text-align: center; }
        .setting-item .label { flex-grow: 1; }
        .setting-item .chevron { color: var(--text-light); }

        .toggle-switch { position: relative; display: inline-block; width: 50px; height: 28px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 28px; }
        .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--header-bg); }
        input:checked + .slider:before { transform: translateX(22px); }

        .logout-btn { width: 100%; padding: 1rem; background-color: var(--danger-color); color: white; border: none; border-radius: 50px; font-size: 1.1rem; font-weight: 500; cursor: pointer; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4); margin-top: 1rem; }

        .bottom-nav { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--card-bg); border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; padding: 0.5rem 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); z-index: 9999; }
        .nav-item { display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; color: var(--text-light); font-size: 0.75rem; text-decoration: none; }
        .nav-item i { font-size: 1.4rem; margin-bottom: 5px; }
        .nav-item.active { color: var(--header-bg); font-weight: 500; }
    </style>
</head>
<body>
    <div class="main-container">
        <header class="header">
            <a href="{{ route('home') }}" class="back-arrow">&larr;</a>
            <h1>Settings</h1>
        </header>

        <div class="content-padding">
            <div class="settings-group">
                <h3 class="settings-title">Account</h3>
                <a href="{{ route('profile.show') }}" class="setting-item">
                    <span class="label"><i class="fas fa-user-circle icon"></i>Edit Profile</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <a href="{{ route('password.edit') }}" class="setting-item">
                    <span class="label"><i class="fas fa-lock icon"></i>Change Password</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </div>

            <div class="settings-group">
                <h3 class="settings-title">Alerts & Notifications</h3>
                <div class="setting-item">
                    <span class="label"><i class="fas fa-traffic-light icon"></i>Delays & Diversions</span>
                    <label class="toggle-switch"><input type="checkbox" checked> <span class="slider"></span></label>
                </div>
                <div class="setting-item">
                    <span class="label"><i class="fas fa-bell icon"></i>Push Notification Settings</span>
                    <label class="toggle-switch"><input type="checkbox"> <span class="slider"></span></label>
                </div>
                <div class="setting-item">
                    <span class="label"><i class="fas fa-tools icon"></i>Maintenance Notices</span>
                    <label class="toggle-switch"><input type="checkbox" checked> <span class="slider"></span></label>
                </div>
            </div>

            <div class="settings-group">
                <h3 class="settings-title">Preferences</h3>
                <a href="#" class="setting-item">
                    <span class="label"><i class="fas fa-language icon"></i>Language & Localization</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <a href="#" class="setting-item">
                    <span class="label"><i class="fas fa-ruler-combined icon"></i>Units (Time, Distance)</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <a href="#" class="setting-item">
                    <span class="label"><i class="fas fa-star icon"></i>Manage Favorites</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </div>

            <div class="settings-group">
                <h3 class="settings-title">Support</h3>
                <a href="{{ route('faq') }}" class="setting-item">
                    <span class="label"><i class="fas fa-question-circle icon"></i>Help / FAQ</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <a href="{{ route('feedback.create') }}" class="setting-item">
                    <span class="label"><i class="fas fa-envelope icon"></i>Contact Support</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
            </div>

            <div class="settings-group">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </div>
    
    <nav class="bottom-nav">
        <a href="{{ route('home') }}" class="nav-item"> <i class="fas fa-home"></i> <span>Home</span> </a>
        <a href="#" onclick="goToLastViewedBus(); return false;" class="nav-item"> <i class="fas fa-bus"></i> <span>Bus</span> </a>
        <a href="{{ route('route.planner') }}" class="nav-item"> <i class="fas fa-map-signs"></i> <span>Planner</span> </a>
        <a href="{{ route('profile.show') }}" class="nav-item"> <i class="fas fa-user"></i> <span>Profile</span> </a>
        <a href="{{ route('settings') }}" class="nav-item active"> <i class="fas fa-cog"></i> <span>Settings</span> </a>
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