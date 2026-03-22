<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - GuardianApp</title>
    <!-- Use Tailwind from CDN for non-Node environments -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet & ChartJS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; color: #1f2937; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">
        <div class="h-16 flex items-center px-6 font-bold text-xl tracking-wide border-b border-gray-800">
            GuardianApp Admin
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-md hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">
                Dashboard Estratégico
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded-md hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">
                Gestión de Usuarios
            </a>
            @endif
            <a href="/" class="block px-4 py-2 rounded-md hover:bg-gray-800 mt-8 text-gray-400">
                Volver al sitio público
            </a>
        </nav>
        <div class="p-4 border-t border-gray-800">
            <p class="text-sm truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role }}</p>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="text-xs text-red-400 hover:text-red-300">Cerrar Sesión</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <!-- Mobile header -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 md:hidden">
            <span class="font-bold">GuardianApp Admin</span>
            <a href="/" class="text-sm text-blue-600">Sitio público</a>
        </header>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
    @stack('scripts')
</body>
</html>
