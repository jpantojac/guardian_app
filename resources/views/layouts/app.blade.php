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
            z-index: 3000;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand span {
            line-height: 1;
            display: inline-block;
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
            z-index: 3001;
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

            .navbar-brand span {
                display: none;
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

            .navbar-brand span {
                display: inline;
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

        /* Shared Incident Card Styles */
        .incident-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .incident-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .incident-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .incident-card-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
        }

        .incident-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
            color: #fff;
            /* Ensure icon is visible if it's text */
        }

        .incident-status {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .incident-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 0.75rem;
        }

        .incident-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .incident-meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .incident-category-tag {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
            background: #F3F4F6;
            color: var(--text-primary);
        }

        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar"><a href="/" class="navbar-brand"><svg xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" width="30px" height="33px" viewBox="0 0 30 33" version="1.1">
                <g id="surface1">
                    <path
                        style=" stroke:none;fill-rule:nonzero;fill:rgb(99.215686%,99.215686%,99.215686%);fill-opacity:1;"
                        d="M 0 0 C 9.898438 0 19.800781 0 30 0 C 30 10.890625 30 21.78125 30 33 C 20.101562 33 10.199219 33 0 33 C 0 22.109375 0 11.21875 0 0 Z M 0 0 " />
                    <path
                        style=" stroke:none;fill-rule:nonzero;fill:rgb(99.215686%,99.215686%,99.215686%);fill-opacity:1;"
                        d="M 0 0 C 9.898438 0 19.800781 0 30 0 C 30 10.890625 30 21.78125 30 33 C 20.101562 33 10.199219 33 0 33 C 0 22.109375 0 11.21875 0 0 Z M 11.226562 2.726562 C 11.097656 2.746094 11.097656 2.746094 10.960938 2.769531 C 8.425781 3.179688 5.953125 3.871094 3.652344 5.011719 C 3.585938 5.046875 3.515625 5.082031 3.445312 5.113281 C 3.277344 5.199219 3.113281 5.28125 2.945312 5.363281 C 2.9375 6.6875 2.929688 8.015625 2.925781 9.339844 C 2.925781 9.957031 2.921875 10.570312 2.917969 11.1875 C 2.914062 11.78125 2.910156 12.378906 2.910156 12.976562 C 2.910156 13.199219 2.90625 13.425781 2.90625 13.652344 C 2.890625 15.648438 3.074219 17.570312 3.773438 19.453125 C 3.835938 19.625 3.835938 19.625 3.898438 19.796875 C 5.015625 22.714844 6.96875 25.195312 9.480469 27.089844 C 9.53125 27.132812 9.582031 27.171875 9.636719 27.214844 C 10.933594 28.214844 12.300781 29.078125 13.746094 29.847656 C 13.828125 29.890625 13.910156 29.933594 13.992188 29.980469 C 14.109375 30.039062 14.109375 30.039062 14.226562 30.101562 C 14.292969 30.136719 14.359375 30.171875 14.429688 30.207031 C 14.945312 30.371094 15.417969 30.035156 15.867188 29.808594 C 15.964844 29.757812 16.066406 29.707031 16.167969 29.652344 C 16.269531 29.601562 16.371094 29.546875 16.476562 29.492188 C 20.699219 27.277344 24.46875 23.761719 25.949219 19.183594 C 26.054688 18.84375 26.152344 18.503906 26.25 18.164062 C 26.273438 18.085938 26.296875 18.003906 26.320312 17.921875 C 26.589844 16.917969 26.714844 15.929688 26.707031 14.890625 C 26.707031 14.734375 26.707031 14.734375 26.707031 14.574219 C 26.707031 14.234375 26.707031 13.898438 26.707031 13.558594 C 26.703125 13.320312 26.703125 13.085938 26.703125 12.847656 C 26.703125 12.289062 26.703125 11.734375 26.699219 11.175781 C 26.699219 10.542969 26.699219 9.910156 26.695312 9.273438 C 26.695312 7.96875 26.691406 6.667969 26.6875 5.363281 C 26.347656 5.203125 26.007812 5.039062 25.667969 4.878906 C 25.523438 4.8125 25.523438 4.8125 25.378906 4.742188 C 24.738281 4.4375 24.101562 4.15625 23.433594 3.925781 C 23.335938 3.894531 23.242188 3.859375 23.144531 3.828125 C 21.335938 3.214844 19.46875 2.878906 17.578125 2.636719 C 17.507812 2.625 17.4375 2.617188 17.363281 2.605469 C 16.554688 2.511719 15.734375 2.527344 14.917969 2.527344 C 14.839844 2.527344 14.757812 2.527344 14.675781 2.527344 C 13.515625 2.53125 12.371094 2.542969 11.226562 2.726562 Z M 11.226562 2.726562 " />
                    <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0.784314%,0.784314%,0.784314%);fill-opacity:1;"
                        d="M 19.1875 7.402344 C 19.359375 7.539062 19.527344 7.675781 19.691406 7.816406 C 19.761719 7.875 19.828125 7.929688 19.894531 7.988281 C 21.125 9.101562 21.953125 10.875 22.117188 12.496094 C 22.234375 17.050781 18.582031 21.582031 15.695312 24.828125 C 15.457031 25.09375 15.230469 25.363281 15 25.636719 C 14.644531 25.621094 14.539062 25.542969 14.277344 25.289062 C 14.1875 25.179688 14.09375 25.070312 14.003906 24.960938 C 13.933594 24.875 13.933594 24.875 13.859375 24.785156 C 13.714844 24.613281 13.574219 24.445312 13.4375 24.273438 C 13.339844 24.15625 13.339844 24.15625 13.238281 24.039062 C 13.054688 23.816406 12.878906 23.589844 12.699219 23.363281 C 12.660156 23.316406 12.625 23.269531 12.585938 23.21875 C 10.050781 20 7.289062 16.085938 7.636719 11.816406 C 7.71875 11.335938 7.894531 10.898438 8.097656 10.453125 C 8.125 10.398438 8.148438 10.339844 8.175781 10.28125 C 8.59375 9.378906 9.132812 8.691406 9.847656 8 C 9.917969 7.925781 9.988281 7.851562 10.0625 7.777344 C 12.4375 5.464844 16.582031 5.527344 19.1875 7.402344 Z M 19.1875 7.402344 " />
                    <path style=" stroke:none;fill-rule:nonzero;fill:rgb(1.176471%,1.176471%,1.176471%);fill-opacity:1;"
                        d="M 13.71875 0.625 C 13.832031 0.625 13.941406 0.625 14.054688 0.625 C 14.285156 0.625 14.515625 0.625 14.746094 0.625 C 15.101562 0.625 15.453125 0.625 15.808594 0.625 C 16.03125 0.625 16.257812 0.625 16.480469 0.625 C 16.585938 0.625 16.691406 0.625 16.800781 0.625 C 16.898438 0.625 16.996094 0.625 17.097656 0.625 C 17.183594 0.625 17.269531 0.625 17.359375 0.625 C 17.578125 0.636719 17.578125 0.636719 17.851562 0.726562 C 18.054688 0.753906 18.253906 0.773438 18.457031 0.796875 C 21.8125 1.1875 25.855469 2.160156 28.621094 4.183594 C 28.730469 4.402344 28.722656 4.542969 28.722656 4.789062 C 28.722656 4.878906 28.726562 4.96875 28.726562 5.058594 C 28.726562 5.160156 28.726562 5.257812 28.726562 5.359375 C 28.726562 5.515625 28.726562 5.515625 28.726562 5.675781 C 28.726562 6.019531 28.726562 6.363281 28.726562 6.710938 C 28.726562 6.949219 28.726562 7.191406 28.726562 7.429688 C 28.730469 7.9375 28.730469 8.441406 28.730469 8.949219 C 28.730469 9.527344 28.730469 10.109375 28.730469 10.691406 C 28.730469 11.191406 28.734375 11.691406 28.734375 12.191406 C 28.734375 12.488281 28.734375 12.785156 28.734375 13.085938 C 28.738281 14.371094 28.714844 15.632812 28.527344 16.910156 C 28.507812 17.046875 28.507812 17.046875 28.488281 17.191406 C 27.953125 20.820312 26.082031 24.011719 23.558594 26.636719 C 23.511719 26.683594 23.464844 26.734375 23.417969 26.785156 C 22.738281 27.488281 22.027344 28.113281 21.238281 28.695312 C 21.074219 28.816406 20.914062 28.9375 20.753906 29.0625 C 19.398438 30.078125 17.941406 30.921875 16.4375 31.703125 C 16.332031 31.761719 16.226562 31.816406 16.117188 31.871094 C 15.097656 32.394531 15.097656 32.394531 14.632812 32.363281 C 14.25 32.234375 13.898438 32.0625 13.539062 31.878906 C 13.4375 31.832031 13.335938 31.78125 13.230469 31.730469 C 11.074219 30.660156 9.066406 29.34375 7.289062 27.730469 C 7.105469 27.5625 6.914062 27.398438 6.722656 27.238281 C 5.816406 26.449219 5.039062 25.597656 4.324219 24.636719 C 4.285156 24.585938 4.25 24.535156 4.210938 24.480469 C 1.6875 21.035156 0.875 17.097656 0.878906 12.925781 C 0.878906 12.742188 0.878906 12.558594 0.878906 12.371094 C 0.878906 11.796875 0.878906 11.21875 0.878906 10.644531 C 0.878906 10.050781 0.878906 9.457031 0.875 8.867188 C 0.875 8.351562 0.875 7.839844 0.875 7.328125 C 0.875 7.023438 0.875 6.71875 0.871094 6.414062 C 0.871094 6.078125 0.871094 5.738281 0.875 5.402344 C 0.875 5.300781 0.871094 5.199219 0.871094 5.097656 C 0.871094 5.003906 0.875 4.914062 0.875 4.820312 C 0.875 4.738281 0.875 4.660156 0.875 4.578125 C 0.945312 4.242188 1.171875 4.078125 1.453125 3.890625 C 3.535156 2.773438 5.71875 1.96875 8.007812 1.363281 C 8.082031 1.34375 8.160156 1.324219 8.238281 1.300781 C 10.027344 0.832031 11.867188 0.621094 13.71875 0.625 Z M 11.226562 2.726562 C 11.097656 2.746094 11.097656 2.746094 10.960938 2.769531 C 8.425781 3.179688 5.953125 3.871094 3.652344 5.011719 C 3.585938 5.046875 3.515625 5.082031 3.445312 5.113281 C 3.277344 5.199219 3.113281 5.28125 2.945312 5.363281 C 2.9375 6.6875 2.929688 8.015625 2.925781 9.339844 C 2.925781 9.957031 2.921875 10.570312 2.917969 11.1875 C 2.914062 11.78125 2.910156 12.378906 2.910156 12.976562 C 2.910156 13.199219 2.90625 13.425781 2.90625 13.652344 C 2.890625 15.648438 3.074219 17.570312 3.773438 19.453125 C 3.835938 19.625 3.835938 19.625 3.898438 19.796875 C 5.015625 22.714844 6.96875 25.195312 9.480469 27.089844 C 9.53125 27.132812 9.582031 27.171875 9.636719 27.214844 C 10.933594 28.214844 12.300781 29.078125 13.746094 29.847656 C 13.828125 29.890625 13.910156 29.933594 13.992188 29.980469 C 14.109375 30.039062 14.109375 30.039062 14.226562 30.101562 C 14.292969 30.136719 14.359375 30.171875 14.429688 30.207031 C 14.945312 30.371094 15.417969 30.035156 15.867188 29.808594 C 15.964844 29.757812 16.066406 29.707031 16.167969 29.652344 C 16.269531 29.601562 16.371094 29.546875 16.476562 29.492188 C 20.699219 27.277344 24.46875 23.761719 25.949219 19.183594 C 26.054688 18.84375 26.152344 18.503906 26.25 18.164062 C 26.273438 18.085938 26.296875 18.003906 26.320312 17.921875 C 26.589844 16.917969 26.714844 15.929688 26.707031 14.890625 C 26.707031 14.734375 26.707031 14.734375 26.707031 14.574219 C 26.707031 14.234375 26.707031 13.898438 26.707031 13.558594 C 26.703125 13.320312 26.703125 13.085938 26.703125 12.847656 C 26.703125 12.289062 26.703125 11.734375 26.699219 11.175781 C 26.699219 10.542969 26.699219 9.910156 26.695312 9.273438 C 26.695312 7.96875 26.691406 6.667969 26.6875 5.363281 C 26.347656 5.203125 26.007812 5.039062 25.667969 4.878906 C 25.523438 4.8125 25.523438 4.8125 25.378906 4.742188 C 24.738281 4.4375 24.101562 4.15625 23.433594 3.925781 C 23.335938 3.894531 23.242188 3.859375 23.144531 3.828125 C 21.335938 3.214844 19.46875 2.878906 17.578125 2.636719 C 17.507812 2.625 17.4375 2.617188 17.363281 2.605469 C 16.554688 2.511719 15.734375 2.527344 14.917969 2.527344 C 14.839844 2.527344 14.757812 2.527344 14.675781 2.527344 C 13.515625 2.53125 12.371094 2.542969 11.226562 2.726562 Z M 11.226562 2.726562 " />
                    <path
                        style=" stroke:none;fill-rule:nonzero;fill:rgb(98.431373%,98.431373%,98.431373%);fill-opacity:1;"
                        d="M 14.683594 8.410156 C 14.773438 8.410156 14.863281 8.410156 14.953125 8.410156 C 16.398438 8.445312 17.523438 8.992188 18.527344 10.023438 C 19.410156 11.066406 19.695312 12.210938 19.601562 13.546875 C 19.433594 14.816406 18.929688 15.875 17.945312 16.726562 C 16.628906 17.632812 15.375 17.847656 13.804688 17.726562 C 12.585938 17.484375 11.550781 16.699219 10.847656 15.714844 C 10.621094 15.371094 10.449219 15.023438 10.308594 14.636719 C 10.273438 14.558594 10.242188 14.484375 10.207031 14.402344 C 9.925781 13.371094 9.953125 11.9375 10.492188 11 C 10.535156 10.914062 10.582031 10.832031 10.628906 10.746094 C 11.195312 9.785156 11.964844 9.1875 12.976562 8.726562 C 13.027344 8.703125 13.082031 8.679688 13.136719 8.652344 C 13.652344 8.4375 14.128906 8.40625 14.683594 8.410156 Z M 14.683594 8.410156 " />
                    <path style=" stroke:none;fill-rule:nonzero;fill:rgb(1.176471%,1.176471%,1.176471%);fill-opacity:1;"
                        d="M 16.019531 10.816406 C 16.601562 11.148438 17.109375 11.625 17.347656 12.261719 C 17.523438 13.035156 17.453125 13.78125 17.023438 14.453125 C 16.660156 15.007812 16.195312 15.359375 15.550781 15.546875 C 14.6875 15.652344 13.882812 15.628906 13.160156 15.089844 C 12.636719 14.640625 12.308594 14.054688 12.199219 13.378906 C 12.152344 12.570312 12.417969 11.902344 12.9375 11.277344 C 13.789062 10.480469 14.972656 10.320312 16.019531 10.816406 Z M 16.019531 10.816406 " />
                </g>
            </svg><span>GuardianApp</span></a><!-- Desktop Navigation -->
        <div class="nav-links desktop-nav"><a href="/">Mapa</a>
            @auth
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'moderator')
                <a href="{{ route('admin.dashboard') }}" style="color: var(--primary); font-weight: 600;">Panel Admin</a>
            @endif
            <span class="user-name">{{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf <button type="submit"
        class="btn btn-logout">Salir</button></form>@else <button onclick="openLoginModal()"
                    class="btn-login">Iniciar Sesión</button>@endauth
        </div><!-- Mobile Profile Avatar -->@auth <div class="profile-avatar" onclick="toggleProfileMenu()">
            <div class="avatar-circle">
                @if(auth()->user()->profile_photo_path)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Avatar"
                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">@else <svg width="20"
                        height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>@endif
            </div><!-- Dropdown Menu -->
            <div class="profile-dropdown" id="profile-dropdown">
                <div class="dropdown-header">
                    <div class="dropdown-avatar">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Avatar"
                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">@else <svg
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>@endif
                    </div>
                    <div class="dropdown-user-info">
                        <div class="dropdown-name">{{ auth()->user()->name }}</div>
                        <div class="dropdown-email">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'moderator')
                <a href="{{ route('admin.dashboard') }}" class="dropdown-item" style="text-decoration: none; color: inherit;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    Panel Admin
                </a>
                @endif
                <button onclick="openProfileModal()" class="dropdown-item"><svg
                        width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>Editar Perfil </button>
                <form action="{{ route('logout') }}" method="POST">@csrf <button type="submit"
                        class="dropdown-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>Cerrar Sesión </button></form>
            </div>
        </div>@else <button onclick="openLoginModal()" class="btn-login mobile-login">Iniciar Sesión</button>@endauth
    </nav>
    <main class="@yield('main-class', 'with-padding')">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
        </div>@endif
        @if(session('error'))
            <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; border: 2px solid #f87171;">
                {{ session('error') }}
        </div>@endif
        @yield('content')
    </main>
    
    <!-- Global Legal Footer -->
    <footer style="background-color: var(--surface); border-top: 1px solid var(--border-color); padding: 1.5rem 2rem; text-align: center; margin-top: 3rem;">
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
            <a href="{{ route('legal.terminos') }}" style="color: var(--text-secondary); text-decoration: none; margin: 0 0.5rem; font-weight: 500;">Términos de Uso</a> | 
            <a href="{{ route('legal.privacidad') }}" style="color: var(--text-secondary); text-decoration: none; margin: 0 0.5rem; font-weight: 500;">Política de Privacidad (Ley 1581)</a>
            <br>
            <span style="display: inline-block; margin-top: 0.75rem;">&copy; {{ date('Y') }} GuardianApp - WebGIS Participativo</span>
        </p>
    </footer>

    <!-- Edit Profile Modal -->
    <div id="profile-modal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 4000; align-items: center; justify-content: center; overflow-y: auto; padding: 2rem 0;">
        <div class="card"
            style="width: 100%; max-width: 500px; position: relative; animation: slideUp 0.3s ease-out; margin: auto;">
            <button onclick="closeProfileModal()"
                style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; cursor: pointer; color: var(--text-secondary); z-index: 1;"><svg
                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg></button>
            <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">Editar Perfil</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Actualiza tu información personal</p>@auth
                <div style="display: flex; gap: 1rem; border-bottom: 2px solid #F5F5F5; margin-bottom: 1.5rem;"><button
                        type="button" class="tab-btn active" onclick="switchProfileTab('profile-data')"
                        style="padding: 0.75rem 1rem; background: none; border: none; font-weight: 600; cursor: pointer; color: var(--primary); border-bottom: 2px solid var(--primary); margin-bottom: -2px;">Datos
                        Personales </button><button type="button" class="tab-btn"
                        onclick="switchProfileTab('profile-history')"
                        style="padding: 0.75rem 1rem; background: none; border: none; font-weight: 500; cursor: pointer; color: var(--text-secondary);">Historial
                        de Reportes </button></div><!-- Profile Data Tab -->
                <div id="profile-data" class="tab-content" style="display: block;">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">@csrf
                        @method('PUT') <div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 1.5rem;">
                            <div style="position: relative; width: 80px; height: 80px; flex-shrink: 0;">
                                @if(auth()->user()->profile_photo_path)
                                    <img id="profile-photo-preview"
                                        src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Profile Photo"
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">@else <div
                                            id="profile-photo-placeholder"
                                            style="width: 100%; height: 100%; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </div><img id="profile-photo-preview" src="" alt="Profile Photo"
                                    style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">@endif
                            </div>
                            <div style="flex: 1;"><label for="profile_photo"
                                    style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem;">Foto de
                                    Perfil</label><input type="file" id="profile_photo" name="profile_photo"
                                    accept="image/*" onchange="previewProfilePhoto(this)"></div>
                        </div>
                        <div style="margin-bottom: 1rem;"><label for="profile_name">Nombre</label><input type="text"
                                id="profile_name" name="name" value="{{ auth()->user()->name }}" required></div>
                        <div style="margin-bottom: 1rem;"><label for="profile_email">Correo Electrónico</label><input
                                type="email" id="profile_email" value="{{ auth()->user()->email }}" disabled
                                style="background-color: #f3f4f6; color: var(--text-secondary); cursor: not-allowed;"></div>
                        <div style="border-top: 1px solid var(--border-color); margin: 1.5rem 0; padding-top: 1.5rem;">
                            <h3 style="margin-top: 0; font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Cambiar
                                Contraseña</h3>
                            <p style="color: var(--text-secondary); font-size: 0.75rem; margin-bottom: 1rem;">Opcional </p>
                            <div style="margin-bottom: 1rem; position: relative;"><label for="profile_password">Nueva
                                    Contraseña</label>
                                <div style="position: relative;"><input type="password" id="profile_password"
                                        name="password" minlength="8" style="padding-right: 2.5rem;"
                                        oninput="validatePassword()"><button type="button"
                                        onclick="togglePasswordVisibility('profile_password')"
                                        style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-secondary); padding: 0;"><svg
                                            id="icon-profile_password" width="20" height="20" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg></button></div><small id="password-error"
                                    style="color: var(--danger); display: none; margin-top: 0.25rem;">La contraseña debe
                                    tener al menos 8 caracteres. </small>
                            </div>
                            <div style="margin-bottom: 1rem;"><label for="profile_password_confirmation">Confirmar
                                    Contraseña</label>
                                <div style="position: relative;"><input type="password" id="profile_password_confirmation"
                                        name="password_confirmation" style="padding-right: 2.5rem;"
                                        oninput="validatePassword()"><button type="button"
                                        onclick="togglePasswordVisibility('profile_password_confirmation')"
                                        style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-secondary); padding: 0;"><svg
                                            id="icon-profile_password_confirmation" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg></button></div><small id="password-match-error"
                                    style="color: var(--danger); display: none; margin-top: 0.25rem;">Las contraseñas no
                                    coinciden. </small>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: flex-end; gap: 1rem;"><button type="button"
                                onclick="closeProfileModal()" class="btn btn-secondary">Cancelar</button><button
                                type="submit" class="btn btn-primary">Guardar Cambios</button></div>
                    </form>
                </div><!-- Incidents History Tab -->
                <div id="profile-history" class="tab-content" style="display: none;">
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 140px;"><label style="font-size: 0.75rem;">Fecha
                                Inicio</label><input type="date" id="history-start-date" onchange="fetchIncidentsHistory()">
                        </div>
                        <div style="flex: 1; min-width: 140px;"><label style="font-size: 0.75rem;">Fecha Fin</label><input
                                type="date" id="history-end-date" onchange="fetchIncidentsHistory()"></div>
                        <div style="flex: 1; min-width: 140px;"><label style="font-size: 0.75rem;">Categoría</label><select
                                id="history-category" onchange="fetchIncidentsHistory()">
                                <option value="">Todas</option><!-- Populated via JS -->
                            </select></div>
                    </div>
                    <button
                        onclick="closeProfileModal(); if(typeof showUserIncidentsOnMap === 'function') showUserIncidentsOnMap();"
                        style="width: 100%; margin-bottom: 1rem; padding: 0.5rem; background: var(--surface); color: var(--primary); border: 1px dashed var(--primary); border-radius: 0.375rem; cursor: pointer; font-weight: 500;">
                        📍 Ver todos mis reportes en el mapa
                    </button>
                    <div id="history-loading"
                        style="text-align: center; padding: 2rem; display: none; color: var(--text-secondary);">Cargando
                        reportes... </div>
                    <div id="history-list"
                        style="display: flex; flex-direction: column; gap: 1rem; max-height: 400px; overflow-y: auto;">
                        <!-- Incidents will be rendered here -->
                    </div>
            </div>@endauth
        </div>
    </div><!-- Shared Helpers -->
    <script>
        const categoryConfig = {
            'Hurto a personas': { color: '#dc2626', icon: '👤' },
            'Hurto a residencias': { color: '#ea580c', icon: '🏠' },
            'Hurto a comercio': { color: '#ca8a04', icon: '🏪' },
            'Violencia intrafamiliar': { color: '#7c3aed', icon: '👨‍👩‍👧' },
            'Homicidio': { color: '#be123c', icon: '⚠️' },
            'Extorsión': { color: '#0891b2', icon: '💰' },
            'Lesiones personales': { color: '#ec4899', icon: '🩹' },
            'Otro': { color: '#6b7280', icon: '📍' }
        };

        function getTimeAgo(date) {
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 60) {
                return `Hace ${diffMins} ${diffMins === 1 ? 'minuto' : 'minutos'}`;
            } else if (diffHours < 24) {
                return `Hace ${diffHours} ${diffHours === 1 ? 'hora' : 'horas'}`;
            } else {
                return `Hace ${diffDays} ${diffDays === 1 ? 'día' : 'días'}`;
            }
        }


    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script> // Profile dropdown menu toggle

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
                    }

                        , 500);
                }

                    , 5000); // Hide after 5 seconds
            }
        });

        // Profile Modal
        function openProfileModal() {
            document.getElementById('profile-modal').style.display = 'flex';
            toggleProfileMenu(); // Close dropdown
        }

        function closeProfileModal() {
            document.getElementById('profile-modal').style.display = 'none';
        }

        function previewProfilePhoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    const preview = document.getElementById('profile-photo-preview');
                    const placeholder = document.getElementById('profile-photo-placeholder');

                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }

                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            }

            else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        function validatePassword() {
            const password = document.getElementById('profile_password').value;
            const confirm = document.getElementById('profile_password_confirmation').value;
            const submitBtn = document.querySelector('#profile-modal button[type="submit"]');

            const lengthError = document.getElementById('password-error');
            const matchError = document.getElementById('password-match-error');

            let isValid = true;

            // Validate length only if password is typed
            if (password.length > 0 && password.length < 8) {
                lengthError.style.display = 'block';
                isValid = false;
            }

            else {
                lengthError.style.display = 'none';
            }

            // Validate match only if confirm is typed
            if (confirm.length > 0 && password !== confirm) {
                matchError.style.display = 'block';
                isValid = false;
            }

            else {
                matchError.style.display = 'none';
            }

            // Also check if main password is empty but confirm is not (edge case)
            if (password.length === 0 && confirm.length > 0) {
                isValid = false;
            }

            submitBtn.disabled = !isValid;

            if (!isValid) {
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
            }

            else {
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            }
        }

        function switchProfileTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
            document.getElementById(tabId).style.display = 'block';

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.style.color = 'var(--text-secondary)';
                btn.style.borderBottom = 'none';
                btn.style.fontWeight = '500';
            });

            const activeBtn = document.querySelector(`.tab-btn[onclick="switchProfileTab('${tabId}')"]`);

            if (activeBtn) {
                activeBtn.classList.add('active');
                activeBtn.style.color = 'var(--primary)';
                activeBtn.style.borderBottom = '2px solid var(--primary)';
                activeBtn.style.fontWeight = '600';
            }

            if (tabId === 'profile-history') {
                fetchIncidentsHistory();
            }
        }

        let categoriesLoaded = false;


        function fetchIncidentsHistory() {
            const startDate = document.getElementById('history-start-date').value;
            const endDate = document.getElementById('history-end-date').value;
            const categoryId = document.getElementById('history-category').value;
            const listContainer = document.getElementById('history-list');
            const loading = document.getElementById('history-loading');

            loading.style.display = 'block';
            listContainer.innerHTML = '';

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
                category_id: categoryId
            });

            window.historyIncidents = []; // Global storage for incidents

            fetch(`/profile/incidents?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';

                    if (!categoriesLoaded && data.categories) {
                        const select = document.getElementById('history-category');
                        data.categories.forEach(cat => {
                            if (!select.querySelector(`option[value="${cat.id}"]`)) {
                                const opt = document.createElement('option');
                                opt.value = cat.id;
                                opt.textContent = cat.name;
                                select.appendChild(opt);
                            }
                        });
                        categoriesLoaded = true;
                    }

                    if (data.incidents.length === 0) {
                        listContainer.innerHTML = '<p style="text-align: center; color: var(--text-secondary);">No se encontraron reportes.</p>';
                        return;
                    }

                    data.incidents.forEach((incident, index) => {
                        window.historyIncidents.push(incident);
                        const date = new Date(incident.created_at);
                        const timeAgo = getTimeAgo(date);
                        const categoryName = incident.category ? incident.category.name : 'Otro';
                        const config = categoryConfig[categoryName] || categoryConfig['Otro'];

                        // Privacy / Reporter Info
                        const privacyIcon = incident.privacy_level === 'IDENTIFIED' ? '👤' : '🔒';
                        const reporterLabel = incident.privacy_level === 'IDENTIFIED' ? 'Identificado' : 'Anónimo';

                        const card = document.createElement('div');
                        card.className = 'incident-card';

                        // Location Description
                        const locationDesc = incident.location_description ? `
                            <div style="font-size: 0.875rem; color: var(--text-primary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.25rem;">
                                <span>📍</span> ${incident.location_description}
                            </div>` : '';

                        // Photos
                        let photosHtml = '';
                        if (incident.photos && incident.photos.length > 0) {
                            photosHtml = `
                                <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                                    ${incident.photos.slice(0, 3).map(photo => `
                                        <div style="width: 40px; height: 40px; border-radius: 4px; overflow: hidden; border: 1px solid #e5e7eb; cursor: pointer;">
                                            <img src="/storage/${photo.photo_path}" style="width: 100%; height: 100%; object-fit: cover;"
                                                onclick="event.stopPropagation(); openImageLightbox('/storage/${photo.photo_path}')">
                                        </div>
                                    `).join('')}
                                    ${incident.photos.length > 3 ? `<div style="width: 40px; height: 40px; background: #f3f4f6; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6b7280;">+${incident.photos.length - 3}</div>` : ''}
                                </div>
                            `;
                        }

                        card.innerHTML = `
                            <div class="incident-card-header">
                                <div class="incident-card-title">
                                    <div class="incident-icon" style="background-color: ${config.color};">
                                        ${config.icon}
                                    </div>
                                    <div>
                                        <h3 style="font-size: 1rem; font-weight: 600; margin: 0; color: var(--text-primary);">
                                            ${categoryName}
                                        </h3>
                                        <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.25rem;">
                                            <span class="incident-category-tag">${categoryName}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            ${locationDesc}

                            ${photosHtml}

                            <p class="incident-description">
                                ${incident.description || 'Sin descripción disponible'}
                            </p>

                            <div class="incident-meta" style="justify-content: space-between; align-items: center;">
                                <div style="display: flex; gap: 1rem;">
                                    <div class="incident-meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <span>${timeAgo}</span>
                                    </div>
                                    <div class="incident-meta-item">
                                        <span>${privacyIcon} ${reporterLabel}</span>
                                    </div>
                                </div>
                                <button onclick="closeProfileModal(); if(typeof focusIncidentOnMap === 'function') focusIncidentOnMap(${incident.latitude}, ${incident.longitude}, window.historyIncidents[${index}]);" 
                                    style="background: none; border: none; color: var(--primary); font-size: 0.75rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                    Ver en mapa <span>→</span>
                                </button>
                            </div>
                        `;
                        listContainer.appendChild(card);
                    });
                })
                .catch(err => {
                    console.error(err);
                    loading.style.display = 'none';
                    listContainer.innerHTML = '<p style="color: var(--danger); text-align: center;">Error al cargar historial.</p>';
                });
        }
    </script>
    <!-- Image Lightbox Modal -->
    <div id="image-lightbox"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 3000; align-items: center; justify-content: center; cursor: zoom-out;">
        <img id="lightbox-image" src=""
            style="max-width: 90%; max-height: 90%; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.5); object-fit: contain;">
        <button onclick="closeImageLightbox()"
            style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.2); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; color: white; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <script>
        // Image Lightbox Functions
        function openImageLightbox(url) {
            const lightbox = document.getElementById('image-lightbox');
            const img = document.getElementById('lightbox-image');
            img.src = url;
            lightbox.style.display = 'flex';
        }

        function closeImageLightbox() {
            document.getElementById('image-lightbox').style.display = 'none';
        }

        // Close on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeImageLightbox();
        });

        // Close on background click
        document.getElementById('image-lightbox').addEventListener('click', function (e) {
            if (e.target === this) closeImageLightbox();
        });
    </script>
    @stack('scripts')
</body>

</html>