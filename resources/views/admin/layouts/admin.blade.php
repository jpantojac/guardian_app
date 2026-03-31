<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - GuardianApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; color: #1f2937; }

        /* ── Sidebar base ── */
        #admin-sidebar {
            width: 256px;
            min-width: 256px;
            transition: transform 0.3s ease, width 0.3s ease, min-width 0.3s ease;
            z-index: 40;
        }

        /* Desktop collapsed: icon-only rail (64px) */
        #admin-sidebar.collapsed {
            width: 64px;
            min-width: 64px;
        }
        #admin-sidebar.collapsed .sidebar-label,
        #admin-sidebar.collapsed .sidebar-user,
        #admin-sidebar.collapsed .sidebar-brand-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
        }
        #admin-sidebar.collapsed nav a {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }
        #admin-sidebar.collapsed nav a .nav-icon {
            margin-right: 0;
        }

        /* Mobile: hidden off-canvas by default */
        @media (max-width: 767px) {
            #admin-sidebar {
                position: fixed;
                top: 0; left: 0; bottom: 0;
                transform: translateX(-100%);
                width: 256px !important;
                min-width: 256px !important;
            }
            #admin-sidebar.mobile-open {
                transform: translateX(0);
            }
        }

        /* Overlay */
        #sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 39;
            backdrop-filter: blur(2px);
        }
        #sidebar-overlay.active { display: block; }

        /* Hamburger button animation */
        .ham-line {
            display: block;
            width: 22px; height: 2px;
            background: currentColor;
            border-radius: 2px;
            transition: transform 0.3s ease, opacity 0.2s ease;
        }
        .ham-open .ham-line:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .ham-open .ham-line:nth-child(2) { opacity: 0; }
        .ham-open .ham-line:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* Nav link transitions */
        #admin-sidebar nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: background 0.2s, padding 0.3s;
            overflow: hidden;
        }
        .sidebar-label { transition: opacity 0.2s ease, width 0.3s ease; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <!-- Mobile overlay -->
    <div id="sidebar-overlay" onclick="closeSidebar()"></div>

    <!-- ── Sidebar ── -->
    <aside id="admin-sidebar" class="bg-gray-900 text-white flex flex-col flex-shrink-0">

        <!-- Brand + hamburger -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-gray-800 flex-shrink-0">
            <span class="sidebar-brand-text font-bold text-lg tracking-wide transition-all duration-300 truncate">GuardianApp Admin</span>
            <button id="hamburger-btn" onclick="toggleSidebar()"
                class="flex flex-col gap-[5px] items-center justify-center w-9 h-9 rounded-md hover:bg-gray-800 transition-colors flex-shrink-0"
                aria-label="Toggle menú">
                <span class="ham-line"></span>
                <span class="ham-line"></span>
                <span class="ham-line"></span>
            </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-5 space-y-1 overflow-hidden">
            <a href="{{ route('admin.dashboard') }}"
               class="px-3 py-2.5 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">
                <!-- Dashboard icon -->
                <svg class="nav-icon flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                <span class="sidebar-label text-sm font-medium">Dashboard Estratégico</span>
            </a>

            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}"
               class="px-3 py-2.5 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">
                <!-- Users icon -->
                <svg class="nav-icon flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span class="sidebar-label text-sm font-medium">Gestión de Usuarios</span>
            </a>
            @endif

            <a href="/" class="px-3 py-2.5 rounded-lg hover:bg-gray-800 mt-4 text-gray-400">
                <!-- Arrow left icon -->
                <svg class="nav-icon flex-shrink-0 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6"/>
                    <line x1="9" y1="12" x2="21" y2="12"/>
                </svg>
                <span class="sidebar-label text-sm">Volver al sitio público</span>
            </a>
        </nav>

        <!-- User info -->
        <div class="p-4 border-t border-gray-800 sidebar-user overflow-hidden transition-all duration-300">
            <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role }}</p>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">Cerrar Sesión</button>
            </form>
        </div>
    </aside>

    <!-- ── Main Content ── -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto min-w-0">

        <!-- Top bar (visible siempre) -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 md:px-6 flex-shrink-0 sticky top-0 z-30">
            <!-- Mobile hamburger (md+ is inside sidebar) -->
            <button class="md:hidden flex flex-col gap-[5px] w-9 h-9 items-center justify-center rounded-md hover:bg-gray-100 transition-colors"
                    onclick="openSidebar()" aria-label="Abrir menú">
                <span class="ham-line text-gray-700"></span>
                <span class="ham-line text-gray-700"></span>
                <span class="ham-line text-gray-700"></span>
            </button>
            <span class="md:hidden font-bold text-gray-800">GuardianApp Admin</span>
            <!-- Breadcrumb / page title placeholder -->
            <div class="hidden md:block text-sm text-gray-500">
                Panel de Administración
            </div>
            <a href="/" class="hidden md:inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Sitio público
            </a>
        </header>

        <!-- Page content -->
        <div class="p-4 md:p-6 flex-1">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">✕</button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        const sidebar   = document.getElementById('admin-sidebar');
        const overlay   = document.getElementById('sidebar-overlay');
        const hamBtn    = document.getElementById('hamburger-btn');
        const isMobile  = () => window.innerWidth < 768;

        // Restore desktop state from localStorage
        if (!isMobile() && localStorage.getItem('sidebar-collapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }

        function toggleSidebar() {
            if (isMobile()) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
                hamBtn.classList.toggle('ham-open');
            } else {
                const collapsed = sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', collapsed);
            }
        }

        function openSidebar() {
            sidebar.classList.add('mobile-open');
            overlay.classList.add('active');
        }

        function closeSidebar() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            hamBtn.classList.remove('ham-open');
        }

        // Close on resize to desktop
        window.addEventListener('resize', () => {
            if (!isMobile()) closeSidebar();
        });
    </script>

    @stack('scripts')
</body>
</html>

