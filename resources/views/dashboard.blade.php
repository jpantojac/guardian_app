@extends('layouts.app')

@section('main-class', '')

@section('content')
    <div id="map"></div>

    <!-- Floating Report Button -->
    <div style="position: absolute; bottom: calc(var(--footer-h, 44px) + 1rem); right: 1rem; z-index: 999;">
        @auth
            <button onclick="openReportModal()" class="map-fab large" title="Reportar Incidente" aria-label="Abrir modal para reportar incidente">
                <svg aria-hidden="true" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </button>
        @else
            <button onclick="openLoginModal()" class="map-fab large" title="Iniciar sesión para reportar" aria-label="Iniciar sesión para reportar incidente">
                <svg aria-hidden="true" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </button>
        @endauth
    </div>

    <!-- Filter Panel -->
    <div style="position: absolute; top: 80px; left: 1rem; z-index: 999; max-width: 320px;">
        <div id="filter-card" class="card" style="margin: 0; transition: all 0.2s;">
            <div class="margin-bottom-1rem"
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Filtros</h3>
                <button id="toggle-filters" aria-label="Contraer o expandir filtros"
                    style="background: none; border: none; cursor: pointer; color: var(--text-secondary); padding: 0.25rem;">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                        <button class="filter-btn time-filter" data-hours="1">1 hora</button>
                        <button class="filter-btn time-filter" data-hours="24">24 horas</button>
                        <button class="filter-btn time-filter active" data-hours="168">7 días</button>
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
                    <input type="range" id="distance-slider" min="1" max="40" value="2" class="distance-slider" aria-label="Ajustar radio de búsqueda en kilómetros">
                    <div style="display: flex; justify-content: space-between; margin-top: 0.25rem;">
                        <span style="font-size: 0.625rem; color: var(--text-secondary);">1 km</span>
                        <span style="font-size: 0.625rem; color: var(--text-secondary);">40 km</span>
                    </div>
                    <div id="location-status"
                        style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem; display: none;">
                        <span id="location-message">📍 Detectando ubicación...</span>
                    </div>
                </div>

                <!-- Heatmap Toggle (Citizen Prevention) -->
                <div style="margin-bottom: 1rem;">
                    <label style="display: flex; justify-content: space-between; align-items: center; cursor: pointer; background: #fff5f5; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #fca5a5;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: #dc2626; display: flex; align-items: center; gap: 0.25rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.5 14.5A2.5 2.5 0 0011 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 11-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 002.5 2.5z"></path></svg>
                            Mapa de Calor
                        </span>
                        <input type="checkbox" id="heatmap-toggle" checked style="width: 16px; height: 16px; cursor: pointer; accent-color: #dc2626;">
                    </label>
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
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 4000; align-items: center; justify-content: center;">
        <div class="card" style="width: 100%; max-width: 400px; position: relative; animation: slideUp 0.3s ease-out;">
            <button onclick="closeLoginModal()" aria-label="Cerrar modal de autenticación"
                style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; cursor: pointer; color: var(--text-secondary);">
                <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <div style="text-align: center; margin-bottom: 2rem;">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30px"
                    height="33px" viewBox="0 0 30 33" version="1.1">
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
                </svg>
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
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 4000; align-items: center; justify-content: center; overflow-y: auto; padding: 2rem 0;">
        <div class="card"
            style="width: 100%; max-width: 700px; position: relative; animation: slideUp 0.3s ease-out; margin: auto;">
            <button onclick="closeReportModal()" aria-label="Cerrar formulario de reporte"
                style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; cursor: pointer; color: var(--text-secondary); z-index: 1;">
                <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            <div
                style="background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1.5rem; display: flex; gap: 0.75rem;">
                <span style="font-size: 1.25rem;">⚠️</span>
                <div>
                    <strong style="display: block; font-size: 0.875rem;">Importante</strong>
                    <p style="font-size: 0.8125rem; margin: 0; line-height: 1.4;">
                        El reporte voluntario de incidente debe ser real ya que es información vital para la comunidad. Por
                        favor, reporta con responsabilidad.
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
                        <input type="file" id="evidence-photos" name="evidence_photos[]" multiple accept="image/*"
                            style="display: none;" onchange="handlePhotoSelect(this)">
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
                <div
                    style="margin-bottom: 1.5rem; background: #f9fafb; padding: 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color);">
                    <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Verificación de
                        Seguridad</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="font-size: 1.125rem; font-family: monospace; letter-spacing: 2px;">¿Cuánto es
                            {{ $num1 }} + {{ $num2 }}?</span>
                        <input type="number" name="captcha" required placeholder="?"
                            style="width: 80px; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem; text-align: center;">
                    </div>
                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">Resuelve la operación
                        para demostrar que eres humano.</p>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="allow_comments" checked style="width: auto;">
                        <span style="font-size: 0.875rem;">Permitir comentarios en este reporte</span>
                    </label>
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
            <button onclick="closeIncidentsModal()" aria-label="Cerrar listado de incidentes"
                style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; cursor: pointer; color: var(--text-secondary); z-index: 1;">
                <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

    <!-- Incident Details Modal -->
    <div id="incident-details-modal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 4000; align-items: center; justify-content: center; overflow-y: auto; padding: 2rem 0;">
        <div class="card"
            style="width: 100%; max-width: 700px; position: relative; animation: slideUp 0.3s ease-out; margin: auto; max-height: 90vh; display: flex; flex-direction: column;">
            <button onclick="closeIncidentDetailsModal()"
                style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; cursor: pointer; color: var(--text-secondary); z-index: 1;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <div id="incident-modal-scroll" style="overflow-y: auto; flex: 1; min-height: 0; padding: 1rem;">
                <div id="incident-details-content">
                    <!-- Content loaded via JS -->
                    <div style="text-align: center; padding: 2rem;">
                        <div class="spinner"></div>
                        <p>Cargando detalles...</p>
                    </div>
                </div>

                <!-- Comments Section -->
                <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Comentarios</h3>

                    <div id="social-stats-container"
                        style="display: none; justify-content: space-between; align-items: center; padding: 0.5rem 0; color: #6B7280; font-size: 0.875rem; border-bottom: 1px solid #E5E7EB; margin-bottom: 1rem;">
                        <!-- Stats loaded via JS -->
                    </div>

                    <div id="comments-list" style="margin-bottom: 1.5rem;">
                        <!-- Comments loaded via JS -->
                    </div>

                </div>
            </div>

            <!-- Sticky Comment Form -->
            <div style="padding: 1rem; border-top: 1px solid var(--border-color); background: white; z-index: 10;">
                @auth
                    <form id="comment-form" method="POST" onsubmit="submitComment(event)">
                        @csrf
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" name="content" required placeholder="Escribe un comentario..."
                                style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                            <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Enviar</button>
                        </div>
                    </form>
                @else
                    <div style="text-align: center; padding: 0.5rem; background: #f9fafb; border-radius: 0.5rem;">
                        <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 0.875rem;">Inicia sesión para
                            comentar</p>
                        <button onclick="openLoginModal()" class="btn btn-secondary btn-sm">Iniciar Sesión</button>
                    </div>
                @endauth
            </div>
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

        #filter-card.collapsed {
            padding: 0.5rem 1rem !important;
            width: auto !important;
            min-width: 0 !important;
            border-radius: 2rem !important;
        }

        #filter-card.collapsed h3 {
            font-size: 0.875rem !important;
        }

        #filter-card.collapsed .margin-bottom-1rem {
            margin-bottom: 0 !important;
        }

        /* Comment Styles */
        .comment-thread {
            margin-bottom: 0.75rem;
        }

        .comment-row {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 0.25rem;
        }

        .comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #0A0A0A;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            flex-shrink: 0;
            object-fit: cover;
        }

        .comment-bubble {
            background: #F3F4F6;
            padding: 0.5rem 0.75rem;
            border-radius: 1rem;
            border-top-left-radius: 0.25rem;
            position: relative;
        }

        .comment-author {
            font-weight: 600;
            font-size: 0.8125rem;
            color: #111827;
        }

        .comment-text {
            font-size: 0.875rem;
            color: #374151;
            margin: 0.125rem 0;
            line-height: 1.4;
        }

        .comment-actions {
            display: flex;
            gap: 1rem;
            margin-left: 3.5rem;
            font-size: 0.75rem;
            color: #6B7280;
            font-weight: 500;
        }

        .comment-action-btn {
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            color: inherit;
            font-weight: inherit;
            transition: color 0.2s;
            position: relative;
        }

        .comment-action-btn:hover {
            text-decoration: underline;
        }

        .replies-container {
            margin-left: 3.5rem;
            border-left: 2px solid #F3F4F6;
            padding-left: 0.75rem;
            margin-top: 0.5rem;
        }

        /* Reaction Picker */
        .reaction-picker-container {
            position: relative;
            display: inline-block;
        }

        .reaction-picker {
            position: absolute;
            bottom: 100%;
            left: 0;
            background: white;
            border-radius: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 0.25rem 0.5rem;
            display: none;
            gap: 0.25rem;
            z-index: 10;
            animation: slideUp 0.2s ease-out;
            border: 1px solid #E5E7EB;
        }

        .reaction-picker-container:hover .reaction-picker,
        .reaction-picker:hover {
            display: flex;
        }

        .view-replies-btn {
            background: none;
            border: none;
            padding: 0;
            color: #6B7280;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            margin-left: 3.5rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .view-replies-btn:hover {
            text-decoration: underline;
        }

        .reaction-stack-icons {
            display: flex;
            align-items: center;
        }

        .reaction-icon-stack {
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 50%;
            font-size: 10px;
            border: 1px solid white;
            margin-left: -4px;
        }

        .reaction-icon-stack:first-child {
            margin-left: 0;
        }

        .reaction-summary {
            position: absolute;
            bottom: -0.75rem;
            right: 0;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            border-radius: 1rem;
            padding: 2px 4px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            border: 1px solid #fff;
            z-index: 1;
            cursor: pointer;
        }

        .reaction-option {
            font-size: 1.25rem;
            cursor: pointer;
            transition: transform 0.2s;
            padding: 0 2px;
        }

        .reaction-option:hover {
            transform: scale(1.3);
        }

        /* Reaction Counts */
        .reaction-summary {
            position: absolute;
            bottom: -0.75rem;
            right: 0;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            padding: 0.125rem 0.375rem;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 2px;
            border: 1px solid #F3F4F6;
            z-index: 1;
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
    <!-- Leaflet.heat plugin -->
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js" defer></script>
    <script>
        let map;
        let markersLayer;
        let userLocationMarker;
        let radiusCircle;
        let heatLayer = null;
        let allIncidents = [];
        let userLocation = null;
        let currentFilters = {
            hours: 168,
            categories: [],
            distanceKm: 2
        };

        // Pagination vars
        let currentIncidentComments = [];
        let commentsShown = 5;
        // Report modal map
        let reportMap = null;
        let reportMarker = null;

        document.addEventListener('DOMContentLoaded', function () {
            map = L.map('map', {
                zoomControl: false
            }).setView([4.6097, -74.0817], 12);

            const zoomControl = L.control.zoom({
                position: 'topright'
            }).addTo(map);
            
            // Fix Leaflet zoom buttons accessibility
            setTimeout(() => {
                const zoomIn = document.querySelector('.leaflet-control-zoom-in');
                const zoomOut = document.querySelector('.leaflet-control-zoom-out');
                if (zoomIn) { zoomIn.setAttribute('aria-label', 'Aumentar zoom'); zoomIn.title = 'Aumentar zoom'; }
                if (zoomOut) { zoomOut.setAttribute('aria-label', 'Disminuir zoom'); zoomOut.title = 'Disminuir zoom'; }
            }, 100);

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

            const layerControl = L.control.layers(baseMaps, null, {
                position: 'topright'
            }).addTo(map);

            // Fix Leaflet layers button accessibility
            setTimeout(() => {
                const layerBtn = document.querySelector('.leaflet-control-layers-toggle');
                if (layerBtn) { layerBtn.setAttribute('aria-label', 'Cambiar capas del mapa'); layerBtn.title = 'Capas del mapa'; }
            }, 100);

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

            if (distanceSlider) {
                distanceSlider.addEventListener('input', function () {
                    const value = this.value;
                    currentFilters.distanceKm = parseInt(value);
                    distanceValue.textContent = value + ' km';
                    const percentage = ((value - 1) / 39) * 100;
                    this.style.background = `linear-gradient(to right, var(--primary) 0%, var(--primary) ${percentage}%, #E5E7EB ${percentage}%, #E5E7EB 100%)`;
                    updateRadiusCircle();
                    applyFilters();
                });
            }

            const toggleFiltersBtn = document.getElementById('toggle-filters');
            if (toggleFiltersBtn) {
                toggleFiltersBtn.addEventListener('click', function () {
                    const content = document.getElementById('filter-content');
                    const card = document.getElementById('filter-card');
                    if (content) content.classList.toggle('collapsed');
                    if (card) card.classList.toggle('collapsed');
                    this.classList.toggle('collapsed');
                });
            }

            // Auto-collapse on mobile
            if (window.innerWidth <= 768) {
                const content = document.getElementById('filter-content');
                const card = document.getElementById('filter-card');
                const toggleBtn = document.getElementById('toggle-filters');

                if (content) content.classList.add('collapsed');
                if (card) card.classList.add('collapsed');
                if (toggleBtn) toggleBtn.classList.add('collapsed');
            }

            const selectAll = document.getElementById('select-all-categories');
            if (selectAll) {
                selectAll.addEventListener('change', function () {
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
            }

            const heatmapToggle = document.getElementById('heatmap-toggle');
            if (heatmapToggle) {
                heatmapToggle.addEventListener('change', function() {
                    applyFilters();
                });
            }
        });

        function requestUserLocation() {
            const locationStatus = document.getElementById('location-status');
            const locationMessage = document.getElementById('location-message');

            if (locationStatus) locationStatus.style.display = 'block';
            if (locationMessage) locationMessage.textContent = '📍 Detectando ubicación...';

            if (!map) return;

            map.locate({
                setView: false,
                maxZoom: 16
            });

            map.on('locationfound', function (e) {
                userLocation = e.latlng;
                map.setView(e.latlng, 14, {
                    animate: true,
                    duration: 1.5
                });
                if (userLocationMarker) map.removeLayer(userLocationMarker);
                userLocationMarker = L.marker(e.latlng, {
                    icon: L.divIcon({
                        html: `<div style="width: 20px; height: 20px; background-color: #3b82f6; border: 4px solid white; border-radius: 50%; box-shadow: 0 0 0 2px #3b82f6, 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                        className: '',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(map);
                userLocationMarker.bindPopup('📍 Tu ubicación');
                updateRadiusCircle();
                if (locationMessage) locationMessage.innerHTML = '✅ Ubicación detectada';
                setTimeout(() => {
                    if (locationStatus) locationStatus.style.display = 'none';
                }, 3000);
                applyFilters();
            });

            map.on('locationerror', function (e) {
                if (locationMessage) locationMessage.innerHTML = '❌ No se pudo detectar la ubicación';
                setTimeout(() => {
                    if (locationStatus) locationStatus.style.display = 'none';
                }, 5000);
            });
        }

        function updateRadiusCircle() {
            if (!userLocation || !map) return;
            if (radiusCircle) map.removeLayer(radiusCircle);
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
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function renderCategoryFilters(categories) {
            const container = document.getElementById('category-filters');
            if (!container) return;
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
                    <div role="img" aria-label="Incidente de ${category}" style="
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
                                                                                                                                                                                                                                            <img src="${url}" alt="Evidencia de ${category}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
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
                                                                                                                                                                                                                                <button onclick="openIncidentDetails(${props.id})" 
                                                                                                                                                                                                                                    style="margin-top: 8px; width: 100%; background: var(--primary); color: white; border: none; padding: 6px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: 500;">
                                                                                                                                                                                                                                    Ver Detalles y Comentarios
                                                                                                                                                                                                                                </button>
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

                // Si no hay ubicacion del usuario aun, no filtrar por distancia
                let distanceMatch = !userLocation; // true cuando no hay GPS
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

            if (heatLayer) {
                map.removeLayer(heatLayer);
                heatLayer = null;
            }

            const heatmapToggle = document.getElementById('heatmap-toggle');
            const showHeatmap = heatmapToggle && heatmapToggle.checked;

            if (showHeatmap) {
                const heatPoints = filteredIncidents.map(f => [f.geometry.coordinates[1], f.geometry.coordinates[0], 0.6]);
                heatLayer = L.heatLayer(heatPoints, {
                    radius: 20,
                    blur: 15,
                    maxZoom: 15,
                    max: 1.0,
                    gradient: {0.4: 'blue', 0.6: 'cyan', 0.7: 'lime', 0.8: 'yellow', 1.0: 'red'}
                }).addTo(map);
            } else {
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
            }

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
                                                                                                                                                                                                                                                        alt="Foto de evidencia - ${category}"
                                                                                                                                                                                                                                                        loading="lazy"
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



        function focusIncidentOnMap(lat, lng, incidentData = null) {
            closeIncidentsModal();
            closeProfileModal(); // Ensure profile modal is closed too

            map.setView([lat, lng], 16, {
                animate: true,
                duration: 1
            });

            // If we have data, ensure there's a marker
            if (incidentData) {
                // Check if marker exists near this location in current layer
                let found = false;
                markersLayer.eachLayer(layer => {
                    const layerLatLng = layer.getLatLng();
                    if (Math.abs(layerLatLng.lat - lat) < 0.0001 && Math.abs(layerLatLng.lng - lng) < 0.0001) {
                        const popup = layer.getPopup();
                        if (popup) {
                            // Temporarily disable autoPan to avoid conflict with setView
                            const oldAutoPan = popup.options.autoPan;
                            popup.options.autoPan = false;
                            layer.openPopup();
                            popup.options.autoPan = oldAutoPan; // Restore
                        } else {
                            layer.openPopup();
                        }
                        found = true;
                    }
                });

                if (!found) {
                    // Create a temporary or permanent marker for this incident
                    const category = incidentData.category ? incidentData.category.name : (incidentData.category_name || 'Otro');
                    const config = categoryConfig[category] || categoryConfig['Otro'];

                    const marker = L.marker([lat, lng], {
                        icon: createCustomIcon(config.color, config.icon)
                    }).addTo(map);

                    // Bind popup with basic info
                    const timeAgo = getTimeAgo(new Date(incidentData.created_at));
                    const popupContent = `
                                                                                                                                                                                                                    <div style="font-family: 'Inter', sans-serif;">
                                                                                                                                                                                                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                                                                                                                                                                                                            <div style="width: 24px; height: 24px; border-radius: 50%; background: ${config.color}; display: flex; align-items: center; justify-content: center; font-size: 12px; color: white;">
                                                                                                                                                                                                                                ${config.icon}
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                            <h3 style="margin: 0; font-size: 14px; font-weight: 600;">${category}</h3>
                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                        <p style="margin: 0 0 4px 0; font-size: 12px; color: #374151;">${incidentData.description || 'Sin descripción'}</p>
                                                                                                                                                                                                                        <div style="font-size: 10px; color: #6b7280;">${timeAgo}</div>
                                                                                                                                                                                                                        <button onclick="openIncidentDetails(${incidentData.id})" 
                                                                                                                                                                                                                            style="margin-top: 8px; width: 100%; background: var(--primary); color: white; border: none; padding: 6px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: 500;">
                                                                                                                                                                                                                            Ver Detalles y Comentarios
                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                `;

                    marker.bindPopup(popupContent, { autoPan: false }).openPopup();

                    // Cleanup marker when popup closes? Optional, maybe keep it.
                }
                showExitMapModeButton('📍 Viendo reporte seleccionado');
            }
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

        // Global function to show user incidents
        window.showUserIncidentsOnMap = function () {
            // Show loading state if needed

            fetch('/profile/incidents')
                .then(res => res.json())
                .then(data => {
                    // Convert Eloquent models to GeoJSON features
                    const userFeatures = data.incidents.map(incident => ({
                        type: "Feature",
                        geometry: {
                            type: "Point",
                            coordinates: [incident.longitude, incident.latitude]
                        },
                        properties: {
                            ...incident,
                            category: incident.category ? incident.category.name : 'Otro'
                        }
                    }));

                    // Update global data source
                    allIncidents = userFeatures;

                    // Reset filters to show all - Maximize range and time
                    currentFilters.categories = [...new Set(allIncidents.map(f => f.properties.category))];
                    currentFilters.hours = 24 * 365 * 10; // 10 years
                    currentFilters.distanceKm = 10000; // Global range (10000 km)

                    // Update UI inputs to reflect this "View All" state if possible, or just force internal state
                    const distanceSlider = document.getElementById('distance-range');
                    if (distanceSlider) distanceSlider.value = 40; // Max visual

                    const timeSelect = document.getElementById('time-filter');
                    if (timeSelect) timeSelect.value = '24'; // Max visual option, though logic overrides

                    // Update UI
                    applyFilters();

                    // Alert user with Exit button
                    showExitMapModeButton('📍 Mostrando tus reportes');

                    // Fit bounds
                    if (userFeatures.length > 0) {
                        const bounds = L.latLngBounds(userFeatures.map(f => [f.geometry.coordinates[1], f.geometry.coordinates[0]]));
                        map.fitBounds(bounds, { padding: [50, 50] });
                    }
                })
                .catch(err => console.error('Error loading user incidents:', err));
        };

        // Helper to show the floating exit button
        function showExitMapModeButton(text) {
            // Remove existing if any
            const existing = document.getElementById('map-mode-alert');
            if (existing) existing.remove();

            const alertDiv = document.createElement('div');
            alertDiv.id = 'map-mode-alert';
            alertDiv.style.cssText = 'position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: #3b82f6; color: white; padding: 0.75rem 1.5rem; border-radius: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 2000; display: flex; align-items: center; gap: 0.5rem; animation: slideUp 0.3s ease-out;';
            alertDiv.innerHTML = `
                                                                                                                                                                                                                <span>${text}</span>
                                                                                                                                                                                                                <button onclick="window.location.reload()" style="background: rgba(255,255,255,0.2); border: none; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; color: white; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">✕</button>
                                                                                                                                                                                                            `;
            document.body.appendChild(alertDiv);
        }

        // Incident Details & Comments Logic
        let currentIncidentId = null;

        function openIncidentDetails(id) {
            currentIncidentId = id;
            document.getElementById('incident-details-modal').style.display = 'flex';

            // Reset content
            const content = document.getElementById('incident-details-content');
            content.innerHTML = `
                                                                                                                                                                                                <div style="text-align: center; padding: 2rem;">
                                                                                                                                                                                                    <div style="width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                                                                                                                                                                                                    <p>Cargando detalles...</p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            `;
            document.getElementById('comments-list').innerHTML = '';

            // Fetch details
            fetch(`/api/incidents/${id}`)
                .then(res => res.json())
                .then(incident => {
                    renderIncidentDetails(incident);
                })
                .catch(err => {
                    console.error(err);
                    content.innerHTML = '<p style="color: red; text-align: center;">Error al cargar el incidente.</p>';
                });
        }

        function closeIncidentDetailsModal() {
            document.getElementById('incident-details-modal').style.display = 'none';
            currentIncidentId = null;
        }

        function renderIncidentDetails(incident) {
            const content = document.getElementById('incident-details-content');
            const category = incident.category ? incident.category.name : 'Otro';
            const config = categoryConfig[category] || categoryConfig['Otro'];
            const timeAgo = getTimeAgo(new Date(incident.created_at));

            // Render Photos
            let photosHtml = '';
            if (incident.photos && incident.photos.length > 0) {
                photosHtml = `
                                                                                                                                                                                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 0.5rem; margin: 1rem 0;">
                                                                                                                                                                                            ${incident.photos.map(p => `
                                                                                                                                                                                                <img src="/storage/${p.photo_path}" 
                                                                                                                                                                                                    onclick="openImageLightbox('/storage/${p.photo_path}')"
                                                                                                                                                                                                    style="width: 100%; height: 100px; object-fit: cover; border-radius: 0.375rem; cursor: zoom-in;"
                                                                                                                                                                                                >
                                                                                                                                                                                            `).join('')}
                                                                                                                                                                                        </div>
                                                                                                                                                                                    `;
            }

            content.innerHTML = `
                                                                                                                                                                                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                                                                                                                                                                        <div style="width: 48px; height: 48px; border-radius: 50%; background: ${config.color}; display: flex; align-items: center; justify-content: center; font-size: 20px; color: white;">
                                                                                                                                                                                            ${config.icon}
                                                                                                                                                                                        </div>
                                                                                                                                                                                        <div>
                                                                                                                                                                                            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">${category}</h2>
                                                                                                                                                                                            <div style="font-size: 0.875rem; color: var(--text-secondary);">${timeAgo}</div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>

                                                                                                                                                                                    <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                                                                                                                                                                                        ${incident.location_description ? `
                                                                                                                                                                                            <div style="margin-bottom: 0.5rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                                                                                                                                                                                                <span>📍</span> <strong>Ubicación:</strong> ${incident.location_description}
                                                                                                                                                                                            </div>
                                                                                                                                                                                        ` : ''}
                                                                                                                                                                                        <div style="line-height: 1.6; color: var(--text-primary);">
                                                                                                                                                                                            ${incident.description || 'Sin descripción disponible.'}
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>

                                                                                                                                                                                    ${photosHtml}
                                                                                                                                                                                `;

            // Render Comments
            const commentsContainer = document.getElementById('comments-list');
            const commentForm = document.getElementById('comment-form');

            // Social Stats Container
            const statsContainer = document.getElementById('social-stats-container');
            if (statsContainer) {
                statsContainer.style.display = 'none'; // reset
                statsContainer.innerHTML = '';
            }

            commentsContainer.innerHTML = ''; // Clear comments list

            if (incident.social_stats && statsContainer) {
                statsContainer.style.display = 'flex';
                renderSocialStats(incident.social_stats);
            }

            // Initial loading state for comments
            commentsContainer.innerHTML += '<div style="text-align: center; padding: 1rem;"><div class="spinner"></div><p>Cargando comentarios...</p></div>';

            // Fetch full incident details to get comments and relations
            fetch(`/api/incidents/${incident.id}?t=${new Date().getTime()}`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(res => res.json())
                .then(fullIncident => {
                    // Update current incident comments with fresh data
                    currentIncidentComments = fullIncident.comments || [];
                    commentsShown = 5;

                    // Clear loading spinner
                    const spinner = commentsContainer.querySelector('.spinner')?.parentElement;
                    if (spinner) spinner.remove();

                    if (!fullIncident.allow_comments) {
                        commentsContainer.innerHTML += '<p style="color: var(--text-secondary); text-align: center; font-style: italic;">Los comentarios están desactivados para este reporte.</p>';
                        if (commentForm) commentForm.style.display = 'none';
                    } else {
                        if (commentForm) commentForm.style.display = 'block';
                        renderCommentsList();
                    }

                    // Also update stats if needed
                    if (fullIncident.social_stats) renderSocialStats(fullIncident.social_stats);
                })
                .catch(err => {
                    console.error('Error fetching details:', err);
                    commentsContainer.innerHTML += '<p style="color: var(--text-error); text-align: center;">Error al cargar comentarios.</p>';
                });
        }

        function renderCommentsList() {
            const commentsContainer = document.getElementById('comments-list');
            if (!commentsContainer) return;

            if (currentIncidentComments.length === 0) {
                commentsContainer.innerHTML = '<p style="color: var(--text-secondary); text-align: center;">No hay comentarios aún. ¡Sé el primero!</p>';
                return;
            }

            const visibleComments = currentIncidentComments.slice(0, commentsShown);
            commentsContainer.innerHTML = visibleComments.map(comment => renderComment(comment)).join('');

            // Load More Button
            if (currentIncidentComments.length > commentsShown) {
                const remaining = currentIncidentComments.length - commentsShown;
                const loadMoreBtn = document.createElement('button');
                loadMoreBtn.className = 'btn btn-secondary btn-sm';
                loadMoreBtn.style.width = '100%';
                loadMoreBtn.style.marginTop = '1rem';
                loadMoreBtn.textContent = `Ver más comentarios (${remaining} restantes)`;
                loadMoreBtn.onclick = function () {
                    commentsShown += 5; // Load 5 more
                    renderCommentsList();
                };
                commentsContainer.appendChild(loadMoreBtn);
            }
        }

        const reactionIcons = { 'like': '👍', 'support': '❤️', 'angry': '😡', 'useful': '💡' };
        const reactionLabels = { 'like': 'Me gusta', 'support': 'Me encanta', 'angry': 'Me enoja', 'useful': 'Útil' };
        // Changed "like" color to dark/black as requested (no blue)
        const reactionColors = { 'like': '#111827', 'support': '#ef4444', 'angry': '#eab308', 'useful': '#f97316' };

        // Global functions for social stats
        function renderSocialStats(stats) {
            const container = document.getElementById('social-stats-container');
            if (!container) return; // Should not happen if structure matches

            if (!stats || (stats.reactions_count === 0 && stats.comments_count === 0)) {
                // Keep container but maybe empty? Or hide?
                // For now let's just show empty states if needed, or hide
                // Ideally if empty we might want to hide it, but user wants to see "stats"
                if (stats && stats.comments_count === 0) {
                    container.style.display = 'none';
                }
                return;
            } else {
                container.style.display = 'flex';
            }

            // Generate icons
            let iconsHtml = '';

            // reaction icons/colors from global scope
            const localIcons = { 'like': '👍', 'support': '❤️', 'useful': '💡', 'angry': '😡' };
            const localColors = { 'like': '#3b82f6', 'support': '#ef4444', 'useful': '#eab308', 'angry': '#f97316' };

            const types = Object.keys(stats.reaction_types || {}).sort((a, b) => (stats.reaction_types[b] || 0) - (stats.reaction_types[a] || 0));

            if (types.length > 0) {
                iconsHtml = `<div style="display: flex; align-items: center; margin-right: 4px;">`;
                types.slice(0, 3).forEach((type, i) => {
                    iconsHtml += `
                                                                                                                                        <div style="
                                                                                                                                            width: 18px; 
                                                                                                                                            height: 18px; 
                                                                                                                                            border-radius: 50%; 
                                                                                                                                            background: ${localColors[type] || '#ccc'}; 
                                                                                                                                            display: flex; 
                                                                                                                                            align-items: center; 
                                                                                                                                            justify-content: center; 
                                                                                                                                            font-size: 10px; 
                                                                                                                                            border: 2px solid white; 
                                                                                                                                            margin-left: ${i > 0 ? '-6px' : '0'};
                                                                                                                                            z-index: ${3 - i};
                                                                                                                                        ">${localIcons[type] || ''}</div>
                                                                                                                                    `;
                });
                iconsHtml += `</div>`;
            }

            container.innerHTML = `
                                                                                                                                <div style="display: flex; align-items: center;">
                                                                                                                                    ${iconsHtml}
                                                                                                                                    <span style="font-weight: 500; margin-left: ${iconsHtml ? '4px' : '0'};">${stats.reactions_count > 0 ? stats.reactions_count : ''}</span>
                                                                                                                                </div>
                                                                                                                                <div>
                                                                                                                                    ${stats.comments_count} ${stats.comments_count === 1 ? 'comentario' : 'comentarios'}
                                                                                                                                </div>
                                                                                                                            `;
        }

        function refreshIncidentStats() {
            if (!currentIncidentId) return;
            // Add timestamp to prevent caching and credentials for auth
            fetch(`/api/incidents/${currentIncidentId}?t=${new Date().getTime()}`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                // Important: for standard web session auth to work with API routes
                // we usually rely on cookies being sent.
                // Assuming 'same-origin', fetch usually sends cookies, but let's be explicit if needed.
                // However, Sanctum SPA mode requires 'include' or 'same-origin'. default is same-origin for same domain.
                // The issue implies we might need to set X-CSRF-TOKEN even for GET if middleware checks it? 
                // No, GET is safe. The issue is likely just 'Accept: application/json' finding the redirect.
                // Let's add that first.
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(incident => {
                    if (incident.social_stats) {
                        renderSocialStats(incident.social_stats);
                    }
                })
                .catch(err => console.error('Error refreshing stats:', err));
        }

        function renderComment(comment) {
            const userInitial = comment.user ? comment.user.name.charAt(0).toUpperCase() : '?';
            const userName = comment.user ? comment.user.name : 'Usuario';
            const timeAgo = getTimeAgo(new Date(comment.created_at));

            // Avatar Logic
            // Avatar Logic
            let avatarHtml = `<div class="comment-avatar">${userInitial}</div>`;

            // Debugging
            if (comment.user) {
                // console.log('Comment User:', comment.user.name, comment.user.profile_photo_url, comment.user.profile_photo_path);
            }

            if (comment.user) {
                if (comment.user.profile_photo_url) {
                    avatarHtml = `<img src="${comment.user.profile_photo_url}" alt="${userName}" class="comment-avatar" style="object-fit: cover;">`;
                } else if (comment.user.profile_photo_path) {
                    // Robust fallback for raw paths
                    const path = comment.user.profile_photo_path;
                    const src = path.startsWith('http') || path.startsWith('/') ? path : `/storage/${path}`;
                    avatarHtml = `<img src="${src}" alt="${userName}" class="comment-avatar" style="object-fit: cover;">`;
                }
            }

            // Reactions Summary (Facebook Style)
            let reactionSummaryHtml = '';
            if (comment.reactions_summary && Object.keys(comment.reactions_summary).length > 0) {
                const totalReactions = Object.values(comment.reactions_summary).reduce((a, b) => a + b, 0);
                const sortedTypes = Object.keys(comment.reactions_summary).sort((a, b) => comment.reactions_summary[b] - comment.reactions_summary[a]);
                const topIcons = sortedTypes.slice(0, 3).map(type =>
                    `<span class="reaction-icon-stack">${reactionIcons[type]}</span>`
                ).join('');

                reactionSummaryHtml = `
                                                                                                                                                                                        <div class="reaction-summary">
                                                                                                                                                                                            <div class="reaction-stack-icons">${topIcons}</div>
                                                                                                                                                                                            <span style="margin-left: 4px;">${totalReactions}</span>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    `;
            }

            // User reaction state
            const myReaction = comment.user_reaction;
            const likeBtnColor = myReaction ? reactionColors[myReaction] : 'inherit';
            const likeBtnText = myReaction ? reactionLabels[myReaction] : 'Me gusta';
            const likeBtnWeight = myReaction ? '600' : '500';

            // Replies Toggle
            let repliesHtml = '';
            let repliesToggleHtml = '';

            if (comment.replies && comment.replies.length > 0) {
                const replyCount = comment.replies.length;
                repliesToggleHtml = `
                                                                                                                                                                                        <button class="view-replies-btn" onclick="toggleReplies(${comment.id})">
                                                                                                                                                                                            Ver las ${replyCount} respuestas
                                                                                                                                                                                        </button>
                                                                                                                                                                                    `;

                repliesHtml = `
                                                                                                                                                                                        <div class="replies-container" id="replies-${comment.id}" style="display: none;">
                                                                                                                                                                                            ${comment.replies.map(reply => renderComment(reply)).join('')}
                                                                                                                                                                                        </div>
                                                                                                                                                                                    `;
            } else {
                // Ensure invisible container exists for potential future replies
                repliesHtml = `<div class="replies-container" id="replies-${comment.id}" style="display: none;"></div>`;
            }

            return `
                                                                                                                                                                                    <div class="comment-thread" id="comment-${comment.id}">
                                                                                                                                                                                        <div class="comment-row">
                                                                                                                                                                                            ${avatarHtml}
                                                                                                                                                                                            <div class="comment-content-wrapper" style="flex: 1;">
                                                                                                                                                                                                <div class="comment-bubble">
                                                                                                                                                                                                    <div class="comment-author">${userName}</div>
                                                                                                                                                                                                    <div class="comment-text">${comment.content}</div>
                                                                                                                                                                                                    ${reactionSummaryHtml}
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="comment-actions">
                                                                                                                                                                                                    <span>${timeAgo}</span>

                                                                                                                                                                                                    <div class="reaction-picker-container">
                                                                                                                                                                                                        <button class="comment-action-btn" style="color: ${likeBtnColor}; font-weight: ${likeBtnWeight}" onclick="toggleReaction(${comment.id}, '${myReaction ? myReaction : 'like'}')">
                                                                                                                                                                                                            ${likeBtnText}
                                                                                                                                                                                                        </button>
                                                                                                                                                                                                        <div class="reaction-picker">
                                                                                                                                                                                                            <div class="reaction-option" onclick="toggleReaction(${comment.id}, 'like')">👍</div>
                                                                                                                                                                                                            <div class="reaction-option" onclick="toggleReaction(${comment.id}, 'support')">❤️</div>
                                                                                                                                                                                                            <div class="reaction-option" onclick="toggleReaction(${comment.id}, 'useful')">💡</div>
                                                                                                                                                                                                            <div class="reaction-option" onclick="toggleReaction(${comment.id}, 'angry')">😡</div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>

                                                                                                                                                                                                    <button class="comment-action-btn" onclick="toggleReplyForm(${comment.id})">Responder</button>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>

                                                                                                                                                                                        ${repliesToggleHtml}

                                                                                                                                                                                        <!-- Reply Form -->
                                                                                                                                                                                        <form id="reply-form-${comment.id}" onsubmit="submitReply(event, ${comment.id})" style="display: none; margin-left: 3.5rem; margin-top: 0.5rem; gap: 0.5rem;">
                                                                                                                                                                                            <input type="text" name="content" required placeholder="Escribe una respuesta..." 
                                                                                                                                                                                                style="flex: 1; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 1rem; width: 100%; font-size: 0.875rem;">
                                                                                                                                                                                        </form>

                                                                                                                                                                                        ${repliesHtml}
                                                                                                                                                                                    </div>
                                                                                                                                                                                `;
        }

        function toggleReplies(commentId) {
            const container = document.getElementById(`replies-${commentId}`);
            if (container) {
                container.style.display = container.style.display === 'none' ? 'block' : 'none';
            }
        }

        function toggleReplyForm(commentId) {
            const form = document.getElementById(`reply-form-${commentId}`);
            form.style.display = form.style.display === 'none' ? 'flex' : 'none';
            if (form.style.display === 'flex') form.querySelector('input').focus();
        }

        function submitReply(e, parentId) {
            e.preventDefault();
            const input = e.target.querySelector('input');
            const content = input.value;

            fetch(`/api/incidents/${currentIncidentId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ content: content, parent_id: parentId })
            })
                .then(res => res.json())
                .then(data => {
                    input.value = '';
                    toggleReplyForm(parentId); // Hide form

                    // Append new reply to DOM
                    let repliesContainer = document.getElementById(`replies-${parentId}`);
                    if (!repliesContainer) {
                        // Fallback creation if not found
                        const parentThread = document.getElementById(`comment-${parentId}`);
                        if (parentThread) {
                            repliesContainer = document.createElement('div');
                            repliesContainer.id = `replies-${parentId}`;
                            repliesContainer.className = 'replies-container';
                            parentThread.appendChild(repliesContainer);
                        }
                    }

                    if (repliesContainer) {
                        repliesContainer.innerHTML += renderComment(data.comment);

                        // Show replies if hidden
                        repliesContainer.style.display = 'block';

                        // Also update the "View X replies" button text if it exists
                        // This is tricky, easier to just accept the visual state
                    }

                    refreshIncidentStats();
                })
                .catch(err => console.error(err));
        }

        function toggleReaction(commentId, type) {
            event.stopPropagation(); // Prevent bubbling if needed
            fetch(`/api/comments/${commentId}/reactions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type: type })
            })
                .then(res => res.json())
                .then(data => {
                    const commentEl = document.getElementById(`comment-${commentId}`);
                    if (!commentEl) return;

                    // Update Reaction Summary
                    let summaryContainer = commentEl.querySelector('.comment-bubble .reaction-summary');
                    if (data.summary && Object.keys(data.summary).length > 0) {
                        const totalReactions = Object.values(data.summary).reduce((a, b) => a + b, 0);
                        const sortedTypes = Object.keys(data.summary).sort((a, b) => data.summary[b] - data.summary[a]);
                        const topIcons = sortedTypes.slice(0, 3).map(type =>
                            `<span class="reaction-icon-stack">${reactionIcons[type]}</span>`
                        ).join('');

                        const newSummaryHtml = `
                                                                                                                                                                                    <div class="reaction-stack-icons">${topIcons}</div>
                                                                                                                                                                                    <span style="margin-left: 4px;">${totalReactions}</span>
                                                                                                                                                                                `;

                        if (summaryContainer) {
                            summaryContainer.innerHTML = newSummaryHtml;
                        } else {
                            const bubble = commentEl.querySelector('.comment-bubble');
                            const newDiv = document.createElement('div');
                            newDiv.className = 'reaction-summary';
                            newDiv.innerHTML = newSummaryHtml;
                            bubble.appendChild(newDiv);
                        }
                    } else {
                        if (summaryContainer) summaryContainer.remove();
                    }

                    // Update User Action Button State
                    const actionBtn = commentEl.querySelector('.comment-actions > .reaction-picker-container > .comment-action-btn');

                    if (actionBtn) {
                        const myReaction = data.user_reaction;
                        actionBtn.style.color = myReaction ? reactionColors[myReaction] : 'inherit';
                        actionBtn.innerText = myReaction ? reactionLabels[myReaction] : 'Me gusta';
                        actionBtn.style.fontWeight = myReaction ? '600' : '500';
                        actionBtn.setAttribute('onclick', `toggleReaction(${commentId}, '${myReaction ? myReaction : 'like'}')`);
                    }

                    refreshIncidentStats();
                })
                .catch(err => console.error(err));
        }

        function submitComment(e) {
            e.preventDefault();
            if (!currentIncidentId) return;

            const form = e.target;
            const input = form.querySelector('input[name="content"]');
            const content = input.value;
            const btn = form.querySelector('button');
            const originalText = btn.innerText;

            btn.disabled = true;
            btn.innerText = 'Enviando...';

            fetch(`/api/incidents/${currentIncidentId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content })
            })
                .then(res => res.json())
                .then(data => {
                    form.reset();

                    // Unwrap the actual comment object
                    const newComment = data.comment;

                    // Update local state for pagination consistency
                    currentIncidentComments.unshift(newComment);
                    commentsShown++; // Ensure the new comment is visible
                    renderCommentsList();

                    refreshIncidentStats();
                })
                .catch(err => {
                    alert('No se pudo enviar el comentario. Inténtalo de nuevo.');
                    console.error(err);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
        } 
    </script>
@endpush