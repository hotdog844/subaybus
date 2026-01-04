<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - SubayBus</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    @yield('styles')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --primary-color: #0A5C36;
            --primary-light: #f0f8f4;
            --text-dark: #2c3e50;
            --text-light: #ecf0f1;
            --bg-color: #f4f7fa;
            --sidebar-bg: #2c3e50;
            --header-bg: #ffffff;
            --border-color: #e9ecef;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
        }

        .admin-wrapper { display: flex; }

        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--text-light);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            transition: transform 0.3s ease-in-out;
            z-index: 101;
        }
        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            border-bottom: 1px solid #34495e;
        }
        .sidebar-nav { padding-top: 1rem; }
        
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-light);
            text-decoration: none;
            padding: 1rem 1.5rem;
            font-size: 0.95rem;
            transition: background-color 0.2s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: #34495e;
            border-left: 4px solid var(--primary-color); /* Visual indicator */
        }
        .sidebar-nav a .icon {
            width: 20px;
            text-align: center;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 100;
        }

        .main-content {
            flex-grow: 1;
            padding-left: 260px;
            transition: padding-left 0.3s ease-in-out;
            width: 100%; /* Ensure full width */
        }
        
        .header {
            background-color: var(--header-bg);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 5px var(--shadow-color);
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .header h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
        }
        
        .burger-menu {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
            margin-right: 1.5rem;
            background: none;
            border: none;
            color: var(--text-dark);
        }

        .page-content { padding: 2rem; }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.is-open { transform: translateX(0); }
            .sidebar.is-open + .sidebar-overlay { display: block; }
            .main-content { padding-left: 0; }
            .burger-menu { display: block; }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            SubayBus Admin
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt icon"></i> Dashboard
            </a>
            <a href="{{ route('admin.buses.index') }}" class="{{ request()->routeIs('admin.buses.*') ? 'active' : '' }}">
                <i class="fas fa-bus icon"></i> Manage Buses
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users icon"></i> Manage Users
            </a>
            <a href="{{ route('admin.routes.index') }}" class="{{ request()->routeIs('admin.routes.*') ? 'active' : '' }}">
                <i class="fas fa-road icon"></i> Manage Routes
            </a>
            <a href="{{ route('admin.drivers.index') }}" class="{{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
                <i class="fas fa-id-card icon"></i> Manage Drivers
            </a>
            <a href="{{ route('admin.feedback.index') }}" class="{{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                <i class="fas fa-comment-dots icon"></i> View Feedback
            </a>
            
            <a href="{{ route('admin.fares.index') }}" class="{{ request()->routeIs('admin.fares.*') ? 'active' : '' }}">
                <i class="fas fa-calculator icon"></i> Fare Matrix
            </a>
            <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line icon"></i> Reports
            </a>
            <a href="{{ route('admin.alerts.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-xl transition">
    <i class="fas fa-bullhorn w-5"></i>
    <span class="font-medium">Broadcasts</span>
</a>
        </nav>
    </aside>

    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="main-content" id="main-content">
        <header class="header">
            <button class="burger-menu" id="burger-menu">
                <i class="fas fa-bars" id="burger-icon"></i>
            </button>
            <h1>@yield('title')</h1>
        </header>

        <main class="page-content">
            @yield('content')
        </main>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const burgerMenu = document.getElementById('burger-menu');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const burgerIcon = document.getElementById('burger-icon');

            function closeSidebar() {
                sidebar.classList.remove('is-open');
                burgerIcon.classList.remove('fa-times');
                burgerIcon.classList.add('fa-bars');
            }

            burgerMenu.addEventListener('click', function() {
                const isOpen = sidebar.classList.toggle('is-open');
                if (isOpen) {
                    burgerIcon.classList.remove('fa-bars');
                    burgerIcon.classList.add('fa-times');
                } else {
                    closeSidebar();
                }
            });

            overlay.addEventListener('click', function() {
                closeSidebar();
            });
        });
    </script>
    
    @yield('scripts')

</body>
</html>