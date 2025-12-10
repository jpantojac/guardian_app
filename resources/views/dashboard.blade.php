@extends('layouts.app')

@section('main-class', '')

@section('content')
    <div id="map"></div>

    <!-- Floating Report Button -->
    <div style="position: absolute; bottom: 2rem; right: 1rem; z-index: 999;">
        @auth
            <button onclick="openReportModal()" class="map-fab large" title="Reportar Incidente">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </button>
        @else
            <button onclick="openLoginModal()" class="map-fab large" title="Iniciar sesión para reportar">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </button>
        @endauth
    </div>

    <!-- Filter Panel -->
    <div style="position: absolute; top: 80px; left: 1rem; z-index: 999; max-width: 320px;">
        <div class="card" style="margin: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Filtros</h3>
                <button id="toggle-filters"
                    style="background: none; border: none; cursor: pointer; color: var(--text-secondary); padding: 0.25rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>

            <div id="filter-content">
                <!-- Time Filter -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; display: block;">Período de
                        tiempo</label>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <button class="filter-btn time-filter active" data-hours="1">1 hora</button>
                        <button class="filter-btn time-filter" data-hours="6">6 horas</button>
                        <button class="filter-btn time-filter" data-hours="24">24 horas</button>
                    </div>
                </div>

                <!-- Distance Filter -->
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="font-size: 0.875rem; font-weight: 600; display: block; margin: 0;">Radio de
                            búsqueda</label>
                        <span id="distance-value" style="font-size: 0.875rem; font-weight: 600; color: var(--primary);">2
                            km</span>
                    </div>
                    <input type="range" id="distance-slider" min="1" max="40" value="2" class="distance-slider">
                    <div style="display: flex; justify-content: space-between; margin-top: 0.25rem;">
                        <span style="font-size: 0.625rem; color: var(--text-secondary);">1 km</span>
                        <span style="font-size: 0.625rem; color: var(--text-secondary);">40 km</span>
                    </div>
                    <div id="location-status"
                        style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem; display: none;">
                        <span id="location-message">📍 Detectando ubicación...</span>
                    </div>
                </div>

                <!-- Category Filter -->
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="font-size: 0.875rem; font-weight: 600; display: block; margin: 0;">Categorías</label>
                        <label
                            style="font-size: 0.75rem; color: var(--primary); cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                            <input type="checkbox" id="select-all-categories" checked style="width: 12px; height: 12px;">
                            Todos
                        </label>
                    </div>
                    <div id="category-filters" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <!-- Categories will be loaded dynamically -->
                    </div>
                </div>

                <!-- Stats -->
                <div style="padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <div style="font-size: 0.875rem; color: var(--text-secondary);">
                        <div style="margin-bottom: 0.25rem;">
                            <strong style="color: var(--text-primary); font-size: 1.25rem;" id="stats-total">0</strong>
                            <span style="margin-left: 0.25rem;">incidentes</span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">
                            en el área seleccionada
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login/Register Modal -->
    <div id="auth-modal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
        <div class="card" style="width: 100%; max-width: 400px; position: relative; animation: slideUp 0.3s ease-out;">
            <button onclick="closeLoginModal()"
                style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; cursor: pointer; color: var(--text-secondary);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <div style="text-align: center; margin-bottom: 2rem;">
                <div
                    style="width: 48px; height: 48px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Seguridad Ciudadana</h2>
                <p style="color: var(--text-secondary);">Accede para reportar incidentes</p>
            </div>

            <!-- Tabs -->
            <div style="display: flex; border-bottom: 1px solid var(--border-color); margin-bottom: 1.5rem;">
                <button onclick="switchAuthTab('login')" id="tab-login"
                    style="flex: 1; padding: 0.75rem; background: none; border: none; border-bottom: 2px solid var(--primary); color: var(--primary); font-weight: 600; cursor: pointer;">Iniciar
                    Sesión</button>
                <button onclick="switchAuthTab('register')" id="tab-register"
                    style="flex: 1; padding: 0.75rem; background: none; border: none; border-bottom: 2px solid transparent; color: var(--text-secondary); font-weight: 600; cursor: pointer;">Registrarse</button>
            </div>

            <!-- Login Form -->
            <form id="form-login" method="POST" action="{{ route('login') }}">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Correo
                        Electrónico</label>
                    <input type="email" name="email" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 0.875rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label
                        style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Contraseña</label>
                    <input type="password" name="password" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 0.875rem;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Ingresar</button>
            </form>

            <!-- Register Form -->
            <form id="form-register" method="POST" action="{{ route('register') }}" style="display: none;">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label
                        style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Nombre</label>
                    <input type="text" name="name" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 0.875rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Correo
                        Electrónico</label>
                    <input type="email" name="email" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 0.875rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label
                        style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Contraseña</label>
                    <input type="password" name="password" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 0.875rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Confirmar
                        Contraseña</label>
                    <input type="password" name="password_confirmation" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 0.875rem;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Registrarse</button>
            </form>
        </div>
    </div>

    <!-- Report Incident Modal -->
    <div id="report-modal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; overflow-y: auto; padding: 2rem 0;">
        <div class="card"
            style="width: 100%; max-width: 700px; position: relative; animation: slideUp 0.3s ease-out; margin: auto;">
            <button onclick="closeReportModal()"
                style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; cursor: pointer; color: var(--text-secondary); z-index: 1;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">Reportar Incidente</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Selecciona la ubicación en el mapa y describe el
                incidente.</p>

            <div id="report-errors"
                style="display: none; background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                <ul id="report-errors-list" style="margin: 0; padding-left: 1.5rem; font-size: 0.875rem;"></ul>
            </div>

            <form id="report-form" method="POST" action="{{ route('report.store') }}">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label for="report-category">Categoría</label>
                    <select id="report-category" name="category_id" required>
                        <option value="">Selecciona una categoría</option>
                        @php
                            $categories = App\Models\Category::all();
                        @endphp
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="report-description">Descripción</label>
                    <textarea id="report-description" name="description" rows="4"
                        placeholder="Describe el incidente..."></textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label>Ubicación</label>
                    <div id="map-picker"
                        style="height: 350px; width: 100%; border: 1px solid var(--border-color); border-radius: 0.375rem; overflow: hidden;">
                    </div>
                    <input type="hidden" id="report-latitude" name="latitude" required>
                    <input type="hidden" id="report-longitude" name="longitude" required>
                    <small style="color: var(--text-secondary); font-size: 0.75rem; display: block; margin-top: 0.5rem;">Haz
                        clic en el mapa o arrastra el marcador para ajustar la ubicación.</small>
                </div>

                <div style="margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <label style="display: flex; align-items: start; gap: 0.5rem; font-weight: 400;">
                        <input type="checkbox" required style="width: auto; margin-top: 0.25rem;">
                        <span>Acepto la política de privacidad y el tratamiento de mis datos.</span>
                    </label>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="closeReportModal()" class="btn btn-secondary"
                        style="flex: 1;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="flex: 2;">Enviar Reporte</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .time-filter {
            flex: 1;
            min-width: 80px;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            background: white;
            color: var(--text-primary);
            border-radius: 0.375rem;
            font-size: 0.75rem;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            opacity: 0.9;
        }

        .time-filter.active {
            background: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
        }

        .distance-slider {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
            cursor: pointer;
            background: linear-gradient(to right, var(--primary) 0%, var(--primary) 2.56%, #E5E7EB 2.56%, #E5E7EB 100%);
        }

        .distance-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--primary);
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .distance-slider::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--primary);
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .category-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .category-checkbox:hover {
            background: #F5F5F5;
        }

        .category-checkbox input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .category-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .category-label {
            font-size: 0.875rem;
            flex: 1;
        }

        #filter-content.collapsed {
            display: none;
        }

        #toggle-filters svg {
            transition: transform 0.2s;
        }

        #toggle-filters.collapsed svg {
            transform: rotate(-90deg);
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let map;
        let markersLayer;
        let userLocationMarker;
        let radiusCircle;
        let allIncidents = [];
        let userLocation = null;
        let currentFilters = {
            hours: 1,
            categories: [],
            distanceKm: 2
        };

        // Report modal map
        let reportMap;
        let reportMarker;

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

        document.addEventListener('DOMContentLoaded', function () {
            map = L.map('map', {
                zoomControl: false
            }).setView([4.6097, -74.0817], 12);

            L.control.zoom({
                position: 'topright'
            }).addTo(map);

            const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'OpenStreetMap'
            });

            const cartoLight = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: 'CartoDB'
            });

            const cartoDark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: 'CartoDB'
            });

            cartoLight.addTo(map);

            const baseMaps = {
                "Claro (CartoDB)": cartoLight,
                "Oscuro (CartoDB)": cartoDark,
                "OpenStreetMap": osm
            };

            L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

            markersLayer = L.layerGroup().addTo(map);

            requestUserLocation();

            fetch('/api/geojson')
                .then(response => response.json())
                .then(data => {
                    allIncidents = data.features;
                    const categories = [...new Set(allIncidents.map(f => f.properties.category))].sort();
                    currentFilters.categories = categories;
                    renderCategoryFilters(categories);
                    applyFilters();
                })
                .catch(error => console.error('Error loading map data:', error));

            document.querySelectorAll('.time-filter').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.time-filter').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilters.hours = parseInt(this.dataset.hours);
                    applyFilters();
                });
            });

            const distanceSlider = document.getElementById('distance-slider');
            const distanceValue = document.getElementById('distance-value');

            distanceSlider.addEventListener('input', function () {
                const value = this.value;
                currentFilters.distanceKm = parseInt(value);
                distanceValue.textContent = value + ' km';
                const percentage = ((value - 1) / 39) * 100;
                this.style.background = `linear-gradient(to right, var(--primary) 0%, var(--primary) ${percentage}%, #E5E7EB ${percentage}%, #E5E7EB 100%)`;
                updateRadiusCircle();
                applyFilters();
            });

            document.getElementById('toggle-filters').addEventListener('click', function () {
                const content = document.getElementById('filter-content');
                content.classList.toggle('collapsed');
                this.classList.toggle('collapsed');
            });

            document.getElementById('select-all-categories').addEventListener('change', function () {
                const isChecked = this.checked;
                const checkboxes = document.querySelectorAll('#category-filters input[type="checkbox"]');
                checkboxes.forEach(cb => cb.checked = isChecked);
                if (isChecked) {
                    currentFilters.categories = [...new Set(allIncidents.map(f => f.properties.category))];
                } else {
                    currentFilters.categories = [];
                }
                applyFilters();
            });
        });

        function requestUserLocation() {
            const locationStatus = document.getElementById('location-status');
            const locationMessage = document.getElementById('location-message');

            locationStatus.style.display = 'block';
            locationMessage.textContent = '📍 Detectando ubicación...';

            map.locate({ setView: false, maxZoom: 16 });

            map.on('locationfound', function (e) {
                userLocation = e.latlng;

                // Center map on user location with smooth animation
                map.setView(e.latlng, 14, {
                    animate: true,
                    duration: 1.5
                });

                if (userLocationMarker) {
                    map.removeLayer(userLocationMarker);
                }

                userLocationMarker = L.marker(e.latlng, {
                    icon: L.divIcon({
                        html: `
                                                        <div style="
                                                            width: 20px;
                                                            height: 20px;
                                                            background-color: #3b82f6;
                                                            border: 4px solid white;
                                                            border-radius: 50%;
                                                            box-shadow: 0 0 0 2px #3b82f6, 0 2px 8px rgba(0,0,0,0.3);
                                                        "></div>
                                                    `,
                        className: '',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(map);

                userLocationMarker.bindPopup('📍 Tu ubicación');
                updateRadiusCircle();

                locationMessage.innerHTML = '✅ Ubicación detectada';
                setTimeout(() => {
                    locationStatus.style.display = 'none';
                }, 3000);

                applyFilters();
            });

            map.on('locationerror', function (e) {
                locationMessage.innerHTML = '❌ No se pudo detectar la ubicación';
                setTimeout(() => {
                    locationStatus.style.display = 'none';
                }, 5000);
            });
        }

        function updateRadiusCircle() {
            if (!userLocation) return;

            if (radiusCircle) {
                map.removeLayer(radiusCircle);
            }

            radiusCircle = L.circle(userLocation, {
                radius: currentFilters.distanceKm * 1000,
                color: '#3b82f6',
                fillColor: '#3b82f6',
                fillOpacity: 0.1,
                weight: 2,
                opacity: 0.5
            }).addTo(map);
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function renderCategoryFilters(categories) {
            const container = document.getElementById('category-filters');
            container.innerHTML = '';

            categories.forEach(category => {
                const config = categoryConfig[category] || categoryConfig['Otro'];
                const div = document.createElement('label');
                div.className = 'category-checkbox';
                div.innerHTML = `
                                                <input type="checkbox" value="${category}" checked>
                                                <span class="category-color" style="background-color: ${config.color};"></span>
                                                <span class="category-label">${category}</span>
                                            `;

                const checkbox = div.querySelector('input');
                checkbox.addEventListener('change', function () {
                    if (this.checked) {
                        currentFilters.categories.push(category);
                    } else {
                        currentFilters.categories = currentFilters.categories.filter(c => c !== category);
                    }
                    const allCheckboxes = document.querySelectorAll('#category-filters input[type="checkbox"]');
                    const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
                    document.getElementById('select-all-categories').checked = allChecked;
                    applyFilters();
                });

                container.appendChild(div);
            });
        }

        function createCustomIcon(category) {
            const config = categoryConfig[category] || categoryConfig['Otro'];
            return L.divIcon({
                html: `
                                                <div style="
                                                    width: 32px;
                                                    height: 32px;
                                                    background-color: ${config.color};
                                                    border: 3px solid white;
                                                    border-radius: 50%;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    font-size: 16px;
                                                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                                                ">
                                                    ${config.icon}
                                                </div>
                                            `,
                className: '',
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -16]
            });
        }

        function applyFilters() {
            markersLayer.clearLayers();
            const now = new Date();
            const hoursAgo = new Date(now - currentFilters.hours * 60 * 60 * 1000);

            const filteredIncidents = allIncidents.filter(feature => {
                const incidentDate = new Date(feature.properties.created_at);
                const category = feature.properties.category;
                const timeMatch = incidentDate >= hoursAgo;
                const categoryMatch = currentFilters.categories.includes(category);

                let distanceMatch = true;
                if (userLocation) {
                    const coords = feature.geometry.coordinates;
                    const distance = calculateDistance(
                        userLocation.lat,
                        userLocation.lng,
                        coords[1],
                        coords[0]
                    );
                    distanceMatch = distance <= currentFilters.distanceKm;
                }

                return timeMatch && categoryMatch && distanceMatch;
            });

            filteredIncidents.forEach(feature => {
                const coords = feature.geometry.coordinates;
                const props = feature.properties;
                const category = props.category || 'Otro';

                const marker = L.marker([coords[1], coords[0]], {
                    icon: createCustomIcon(category)
                });

                marker.bindPopup(`
                                                <div style="font-family: 'Inter', sans-serif;">
                                                    <strong style="font-size: 0.875rem; color: #0A0A0A;">${props.category}</strong><br>
                                                    <p style="margin: 0.5rem 0; font-size: 0.75rem; color: #706F6C;">${props.description || 'Sin descripción'}</p>
                                                    <small style="font-size: 0.625rem; color: #9CA3AF;">${new Date(props.created_at).toLocaleString('es-CO')}</small>
                                                </div>
                                            `);

                markersLayer.addLayer(marker);
            });

            document.getElementById('stats-total').innerText = filteredIncidents.length;
        }

        // Modal Functions
        function openLoginModal() {
            document.getElementById('auth-modal').style.display = 'flex';
        }

        function closeLoginModal() {
            document.getElementById('auth-modal').style.display = 'none';
        }

        function switchAuthTab(tab) {
            const loginForm = document.getElementById('form-login');
            const registerForm = document.getElementById('form-register');
            const loginTab = document.getElementById('tab-login');
            const registerTab = document.getElementById('tab-register');

            if (tab === 'login') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                loginTab.style.borderBottomColor = 'var(--primary)';
                loginTab.style.color = 'var(--primary)';
                registerTab.style.borderBottomColor = 'transparent';
                registerTab.style.color = 'var(--text-secondary)';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                registerTab.style.borderBottomColor = 'var(--primary)';
                registerTab.style.color = 'var(--primary)';
                loginTab.style.borderBottomColor = 'transparent';
                loginTab.style.color = 'var(--text-secondary)';
            }
        }

        // Report Modal Functions
        function openReportModal() {
            document.getElementById('report-modal').style.display = 'flex';

            // Initialize map after modal is visible
            setTimeout(() => {
                if (!reportMap) {
                    reportMap = L.map('map-picker').setView([4.6097, -74.0817], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap'
                    }).addTo(reportMap);

                    // Try to get user location
                    if (userLocation) {
                        updateReportMarker(userLocation.lat, userLocation.lng);
                        reportMap.setView([userLocation.lat, userLocation.lng], 15);
                    } else if ("geolocation" in navigator) {
                        navigator.geolocation.getCurrentPosition(position => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            updateReportMarker(lat, lng);
                            reportMap.setView([lat, lng], 15);
                        });
                    }

                    reportMap.on('click', function (e) {
                        updateReportMarker(e.latlng.lat, e.latlng.lng);
                    });
                } else {
                    reportMap.invalidateSize();
                }
            }, 100);
        }

        function closeReportModal() {
            document.getElementById('report-modal').style.display = 'none';
            document.getElementById('report-errors').style.display = 'none';
        }

        function updateReportMarker(lat, lng) {
            if (reportMarker) {
                reportMarker.setLatLng([lat, lng]);
            } else {
                reportMarker = L.marker([lat, lng], { draggable: true }).addTo(reportMap);
                reportMarker.on('dragend', function (event) {
                    const position = reportMarker.getLatLng();
                    document.getElementById('report-latitude').value = position.lat;
                    document.getElementById('report-longitude').value = position.lng;
                });
            }
            document.getElementById('report-latitude').value = lat;
            document.getElementById('report-longitude').value = lng;
        }

        // Close modals when clicking outside
        document.getElementById('auth-modal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });

        document.getElementById('report-modal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeReportModal();
            }
        });
    </script>
@endpush