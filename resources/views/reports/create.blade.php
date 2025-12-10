@extends('layouts.app')

@section('content')
    <div class="card" style="max-width: 700px; margin: 2rem auto;">
        <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">Reportar Incidente</h2>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Selecciona la ubicación en el mapa y describe el
            incidente.</p>

        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.875rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('report.store') }}">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label for="category_id">Categoría</label>
                <select id="category_id" name="category_id" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="description">Descripción</label>
                <textarea id="description" name="description" rows="4" placeholder="Describe el incidente..."></textarea>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label>Ubicación</label>
                <div id="map-picker"
                    style="height: 350px; width: 100%; border: 1px solid var(--border-color); border-radius: 0.375rem; overflow: hidden;">
                </div>
                <input type="hidden" id="latitude" name="latitude" required>
                <input type="hidden" id="longitude" name="longitude" required>
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
                <a href="/" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancelar</a>
                <button type="submit" class="btn btn-primary" style="flex: 2;">Enviar Reporte</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map-picker').setView([4.6097, -74.0817], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            let marker;

            // Try to get user location
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    updateMarker(lat, lng);
                    map.setView([lat, lng], 15);
                });
            }

            map.on('click', function (e) {
                updateMarker(e.latlng.lat, e.latlng.lng);
            });

            function updateMarker(lat, lng) {
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    marker.on('dragend', function (event) {
                        const position = marker.getLatLng();
                        document.getElementById('latitude').value = position.lat;
                        document.getElementById('longitude').value = position.lng;
                    });
                }
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            }
        });
    </script>
@endpush