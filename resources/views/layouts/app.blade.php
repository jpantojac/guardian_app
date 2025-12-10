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
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
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
        <div class="nav-links">
            <a href="/">Mapa</a>
            @auth
                <span style="color: var(--text-secondary);">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn"
                        style="background: transparent; color: var(--text-secondary);">Salir</button>
                </form>
            @else
                <button onclick="openLoginModal()">Iniciar Sesión</button>
            @endauth
        </div>
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
    @stack('scripts')
</body>

</html>