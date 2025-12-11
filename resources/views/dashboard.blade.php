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
                <div style="padding-top: 1rem; border-top: 1px solid var(--border-color); cursor: pointer;"
                    onclick="openIncidentsModal()">
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

            <!-- Warning Message -->
            <div style="background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1.5rem; display: flex; gap: 0.75rem;">
                <span style="font-size: 1.25rem;">⚠️</span>
                <div>
                    <strong style="display: block; font-size: 0.875rem;">Importante</strong>
                    <p style="font-size: 0.8125rem; margin: 0; line-height: 1.4;">
                        El reporte voluntario de incidente debe ser real ya que es información vital para la comunidad. Por favor, reporta con responsabilidad.
                    </p>
                </div>
            </div>

            <form id="report-form" method="POST" action="{{ route('report.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label for="report-category">Categoría</label>
                    <select id="report-category" name="category_id" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
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
                    <textarea id="report-description" name="description" rows="3" placeholder="Describe el incidente..."
                        style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem;"></textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="location-description">Ubicación (Descripción)</label>
                    <input type="text" id="location-description" name="location_description"
                        placeholder="Ej: Cerca de Transmilenio Calle 72"
                        style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label>Ubicación (Mapa)</label>
                    <div id="map-picker"
                        style="height: 250px; width: 100%; border: 1px solid var(--border-color); border-radius: 0.375rem; overflow: hidden;">
                    </div>
                    <input type="hidden" id="report-latitude" name="latitude" required>
                    <input type="hidden" id="report-longitude" name="longitude" required>
                    <small style="color: var(--text-secondary); font-size: 0.75rem; display: block; margin-top: 0.5rem;">Haz
                        clic en el mapa o arrastra el marcador para ajustar la ubicación.</small>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label>Fotos de Evidencia</label>
                    <div
                        style="border: 1px dashed var(--border-color); padding: 1rem; border-radius: 0.375rem; text-align: center;">
                        <input type="file" id="evidence-photos" name="evidence_photos[]" multiple
                            accept="image/*" style="display: none;" onchange="handlePhotoSelect(this)">
                        <label for="evidence-photos" style="cursor: pointer; color: var(--primary); font-weight: 500;">
                            Seleccionar imágenes
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Máximo 5 fotos
                            (JPG, PNG)</p>
                    </div>
                    <div id="photo-preview-grid"
                        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 0.5rem; margin-top: 1rem;">
                    </div>
                    <p id="photo-count"
                        style="font-size: 0.75rem; color: var(--text-primary); margin-top: 0.5rem; text-align: right;">0/5
                        fotos</p>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label>Privacidad</label>
                    <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="privacy_level" value="ANONYMOUS" checked>
                            <span>🔒 Anónimo</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="privacy_level" value="IDENTIFIED">
                            <span>👤 Identificado ({{ auth()->user()->name ?? 'Yo' }})</span>
                        </label>
                    </div>
                </div>

                <!-- CAPTCHA -->
                <div style="margin-bottom: 1.5rem; background: #f9fafb; padding: 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color);">
                    <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Verificación de Seguridad</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="font-size: 1.125rem; font-family: monospace; letter-spacing: 2px;">¿Cuánto es {{ $num1 }} + {{ $num2 }}?</span>
                        <input type="number" name="captcha" required placeholder="?" 
                            style="width: 80px; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem; text-align: center;">
                    </div>
                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">Resuelve la operación para demostrar que eres humano.</p>
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

    <!-- Incidents List Modal -->
    <div id="incidents-modal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; overflow-y: auto; padding: 2rem 0;">
        <div class="card"
            style="width: 100%; max-width: 700px; position: relative; animation: slideUp 0.3s ease-out; margin: auto; max-height: 90vh; display: flex; flex-direction: column;">
            <button onclick="closeIncidentsModal()"
                style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; cursor: pointer; color: var(--text-secondary); z-index: 1;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <div style="margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">Incidentes Filtrados</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">
                    <span id="modal-incidents-count">0</span> incidentes en el área seleccionada
                </p>
            </div>

            <div id="incidents-list-container" style="overflow-y: auto; flex: 1;">
                <!-- Incidents will be loaded dynamically -->
            </div>
        </div>
    </div>
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

        const statusConfig = {
            'reported': { label: 'Reportado', color: '#6b7280', bg: '#f3f4f6' },
            'validated': { label: 'Validado', color: '#059669', bg: '#d1fae5' },
            'rejected': { label: 'Rechazado', color: '#dc2626', bg: '#fee2e2' },
            'in_progress': { label: 'En Progreso', color: '#2563eb', bg: '#dbeafe' },
            'resolved': { label: 'Resuelto', color: '#059669', bg: '#d1fae5' }
        };

        function getIncidentPopupHtml(props) {
            const category = props.category || 'Otro';
            const config = categoryConfig[category] || categoryConfig['Otro'];
            const status = props.status || 'reported';
            const statusInfo = statusConfig[status] || statusConfig['reported'];

            // Time ago
            const timeAgo = getTimeAgo(new Date(props.created_at));

            // Photos (limit to 3 for popup)
            let photosHtml = '';
            if (props.photos && props.photos.length > 0) {
                photosHtml = `
                        <div style="display: flex; gap: 4px; margin-top: 8px; overflow: hidden;">
                            ${props.photos.slice(0, 3).map(url => `
                                <div style="width: 40px; height: 40px; border-radius: 4px; overflow: hidden; cursor: pointer;" onclick="openImageLightbox('${url}')">
                                    <img src="${url}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            `).join('')}
                            ${props.photos.length > 3 ? `<div style="width: 40px; height: 40px; background: #f3f4f6; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6b7280;">+${props.photos.length - 3}</div>` : ''}
                        </div>
                    `;
            }

            return `
                    <div style="font-family: 'Inter', sans-serif; width: 260px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 24px; height: 24px; border-radius: 50%; background: ${config.color}; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                    ${config.icon}
                                </div>
                                <strong style="font-size: 14px; color: #111827;">${category}</strong>
                            </div>
                        <!-- Status hidden as requested -->
                        </div>

                        ${props.location_description ? `
                            <div style="font-size: 11px; color: #4b5563; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;">
                                <span>📍</span> ${props.location_description}
                            </div>
                        ` : ''}

                        <p style="margin: 0 0 8px 0; font-size: 12px; color: #374151; line-height: 1.4;">
                            ${props.description || 'Sin descripción'}
                        </p>

                        ${photosHtml}

                        <div style="margin-top: 8px; font-size: 10px; color: #9ca3af; display: flex; justify-content: space-between;">
                            <span>${timeAgo}</span>
                            <span>${props.privacy_level === 'IDENTIFIED' ? '👤 Usuario' : '🔒 Anónimo'}</span>
                        </div>
                    </div>
                `;
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

                marker.bindPopup(getIncidentPopupHtml(props));

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

        document.getElementById('incidents-modal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeIncidentsModal();
            }
        });

        // Incidents Modal Functions
        function openIncidentsModal() {
            const filteredIncidents = getFilteredIncidents();

            if (filteredIncidents.length === 0) {
                return; // Don't open if no incidents
            }

            document.getElementById('modal-incidents-count').textContent = filteredIncidents.length;
            renderIncidentCards(filteredIncidents);
            document.getElementById('incidents-modal').style.display = 'flex';
        }

        function closeIncidentsModal() {
            document.getElementById('incidents-modal').style.display = 'none';
        }

        function getFilteredIncidents() {
            const now = new Date();
            const hoursAgo = new Date(now - currentFilters.hours * 60 * 60 * 1000);

            return allIncidents.filter(feature => {
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
        }

        function renderIncidentCards(incidents) {
            const container = document.getElementById('incidents-list-container');
            container.innerHTML = '';

            incidents.forEach(feature => {
                const props = feature.properties;
                const coords = feature.geometry.coordinates;
                const category = props.category || 'Otro';
                const config = categoryConfig[category] || categoryConfig['Otro'];

                // Calculate time ago
                const createdAt = new Date(props.created_at);
                const timeAgo = getTimeAgo(createdAt);

                // Status badge
                const status = props.status || 'reported';
                const statusInfo = statusConfig[status] || statusConfig['reported'];

                // Privacy / Reporter Info
                const reporterName = props.privacy_level === 'IDENTIFIED' ? (props.reporter_name || 'Usuario') : 'Anónimo';
                const privacyIcon = props.privacy_level === 'IDENTIFIED' ? '👤' : '🔒';

                // Location Description
                const locationDesc = props.location_description ? `
                                    <div style="font-size: 0.875rem; color: var(--text-primary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.25rem;">
                                        <span>📍</span> ${props.location_description}
                                    </div>` : '';

                // Localidad
                const localidadBadge = props.localidad ? `
                                    <span style="background: #EEF2FF; color: #4F46E5; padding: 0.125rem 0.5rem; border-radius: 999px; font-size: 0.75rem;">
                                        ${props.localidad}
                                    </span>` : '';

                // Photos Carousel / Preview
                let photosHtml = '';
                if (props.photos && props.photos.length > 0) {
                    photosHtml = `
                                        <div style="margin-bottom: 0.75rem; overflow-x: auto; display: flex; gap: 0.5rem; padding-bottom: 0.5rem;">
                                        ${props.photos.map(url => `
                                            <img src="${url}" 
                                                onclick="event.stopPropagation(); openImageLightbox('${url}')"
                                                style="height: 60px; width: 60px; object-fit: cover; border-radius: 0.375rem; border: 1px solid var(--border-color); flex-shrink: 0; cursor: zoom-in; transition: transform 0.2s;"
                                                onmouseover="this.style.transform='scale(1.1)'"
                                                onmouseout="this.style.transform='scale(1)'"
                                            >
                                        `).join('')}
                                    </div>
                                `;
                }

                const card = document.createElement('div');
                card.className = 'incident-card';
                card.onclick = () => focusIncidentOnMap(coords[1], coords[0]);

                card.innerHTML = `
                                    <div class="incident-card-header">
                                        <div class="incident-card-title">
                                            <div class="incident-icon" style="background-color: ${config.color};">
                                                ${config.icon}
                                            </div>
                                            <div>
                                                <h3 style="font-size: 1rem; font-weight: 600; margin: 0; color: var(--text-primary);">
                                                    ${category}
                                                </h3>
                                                <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.25rem;">
                                                    <span class="incident-category-tag">${category}</span>
                                                    ${localidadBadge}
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Status hidden as requested -->
                                    </div>

                                    ${locationDesc}

                                    <p class="incident-description">
                                        ${props.description || 'Sin descripción disponible'}
                                    </p>

                                    ${photosHtml}

                                    <div class="incident-meta" style="justify-content: space-between;">
                                        <div style="display: flex; gap: 1rem;">
                                            <div class="incident-meta-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                <span>${timeAgo}</span>
                                            </div>
                                            <div class="incident-meta-item">
                                                <span>${privacyIcon} ${reporterName}</span>
                                            </div>
                                        </div>
                                    </div>
                                `;

                container.appendChild(card);
            });
        }

        // Photo Preview Logic
        let selectedFiles = [];

        function handlePhotoSelect(input) {
            const files = Array.from(input.files);
            const previewGrid = document.getElementById('photo-preview-grid');
            const countLabel = document.getElementById('photo-count');

            if (selectedFiles.length + files.length > 5) {
                alert('Máximo 5 fotos permitidas');
                return;
            }

            // Merge new files
            selectedFiles = [...selectedFiles, ...files];

            // Re-render preview
            renderPhotoPreviews();

            // Update input files
            updateInputFiles(input);
        }

        function renderPhotoPreviews() {
            const previewGrid = document.getElementById('photo-preview-grid');
            const countLabel = document.getElementById('photo-count');

            previewGrid.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.style.position = 'relative';
                    div.innerHTML = `
                                        <img src="${e.target.result}" style="width: 100%; height: 60px; object-fit: cover; border-radius: 4px;">
                                        <button type="button" onclick="removePhoto(${index})" 
                                            style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            ×
                                        </button>
                                    `;
                    previewGrid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });

            countLabel.textContent = `${selectedFiles.length}/5 fotos`;
        }

        function removePhoto(index) {
            selectedFiles.splice(index, 1);
            renderPhotoPreviews();
            updateInputFiles(document.getElementById('evidence-photos'));
        }

        function updateInputFiles(input) {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        }

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

        function focusIncidentOnMap(lat, lng) {
            closeIncidentsModal();
            map.setView([lat, lng], 16, {
                animate: true,
                duration: 1
            });
        }

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
@endpush