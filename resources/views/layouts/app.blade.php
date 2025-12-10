<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GuardianApp - WebGIS Participativo</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0A0A0A;
            --secondary: #706F6C;
            --danger: #dc2626;
            --background: #E5E5E5;
            --surface: #FFFFFF;
            --text-primary: #1B1B18;
            --text-secondary: #706F6C;
            --border-color: #D4D4D4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background);
            color: var(--text-primary);
        }

        .navbar {
            background-color: var(--surface);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-primary);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-links a,
        .nav-links button {
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: color 0.2s;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .nav-links a:hover,
        .nav-links button:hover {
            color: var(--text-primary);
        }

        .user-name {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .btn-logout {
            background: transparent !important;
            color: var(--text-secondary) !important;
            padding: 0 !important;
            font-weight: 500;
        }

        .btn-login {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .btn-login:hover {
            color: var(--text-primary);
        }

        /* Profile Avatar */
        .profile-avatar {
            position: relative;
            display: none;
            /* Hidden by default, shown on mobile */
            cursor: pointer;
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.2s;
        }

        .avatar-circle:hover {
            background: #1B1B18;
            transform: scale(1.05);
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 240px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease-out;
            z-index: 1000;
        }

        .profile-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
        }

        .dropdown-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .dropdown-user-info {
            flex: 1;
            min-width: 0;
        }

        .dropdown-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-email {
            font-size: 0.75rem;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border-color);
            margin: 0;
        }

        .dropdown-item {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            text-align: left;
        }

        .dropdown-item:hover {
            background: #F5F5F5;
        }

        .dropdown-item svg {
            flex-shrink: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .desktop-nav {
                display: none !important;
            }

            .profile-avatar {
                display: block;
            }

            .mobile-login {
                display: block;
                font-size: 0.875rem;
            }

            .navbar-brand {
                font-size: 1.125rem;
            }
        }

        @media (min-width: 769px) {

            /* Show profile avatar on desktop too */
            .desktop-nav {
                display: none !important;
            }

            .profile-avatar {
                display: block !important;
            }

            .mobile-login {
                display: block !important;
            }
        }

        .btn {
            display: inline-block;
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #1B1B18;
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            border-color: var(--text-primary);
            color: var(--text-primary);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        main {
            padding: 0;
        }

        main.with-padding {
            padding: 2rem;
        }

        #map {
            height: calc(100vh - 64px);
            width: 100%;
        }

        .card {
            background: var(--surface);
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            min-width: 320px;
            max-width: 500px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideDown 0.3s ease-out;
            font-weight: 500;
        }

        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #6ee7b7;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            background-color: var(--surface);
            color: var(--text-primary);
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--text-primary);
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--text-primary);
        }

        /* Map Controls */
        .map-controls {
            position: absolute;
            top: 80px;
            right: 1rem;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .map-fab {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .map-fab:hover {
            background-color: #1B1B18;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .map-fab.large {
            width: 64px;
            height: 64px;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar">
        <a href="/" class="navbar-brand">🛡️ GuardianApp</a>

        <!-- Desktop Navigation -->
        <div class="nav-links desktop-nav">
            <a href="/">Mapa</a>
            @auth
                <span class="user-name">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-logout">Salir</button>
                </form>
            @else
                <button onclick="openLoginModal()" class="btn-login">Iniciar Sesión</button>
            @endauth
        </div>

        <!-- Mobile Profile Avatar -->
        @auth
            <div class="profile-avatar" onclick="toggleProfileMenu()">
                <div class="avatar-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>

                <!-- Dropdown Menu -->
                <div class="profile-dropdown" id="profile-dropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="dropdown-user-info">
                            <div class="dropdown-name">{{ auth()->user()->name }}</div>
                            <div class="dropdown-email">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        @else
            <button onclick="openLoginModal()" class="btn-login mobile-login">Iniciar Sesión</button>
        @endauth
    </nav>

    <main class="@yield('main-class', 'with-padding')">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Profile dropdown menu toggle
        function toggleProfileMenu() {
            const dropdown = document.getElementById('profile-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const profileAvatar = document.querySelector('.profile-avatar');
            const dropdown = document.getElementById('profile-dropdown');

            if (dropdown && profileAvatar) {
                if (!profileAvatar.contains(event.target)) {
                    dropdown.classList.remove('show');
                }
            }
        });

        // Auto-hide success notifications
        document.addEventListener('DOMContentLoaded', function () {
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(function () {
                    successAlert.style.transition = 'opacity 0.5s ease-out';
                    successAlert.style.opacity = '0';
                    setTimeout(function () {
                        successAlert.remove();
                    }, 500);
                }, 5000); // Hide after 5 seconds
            }
        });
    </script>

    @stack('scripts')
</body>

</html>