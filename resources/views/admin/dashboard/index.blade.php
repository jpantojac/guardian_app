@extends('admin.layouts.admin')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Estratégico Institucional</h1>
        <p class="text-gray-600">Visualización de patrones y zonas de riesgo histórico.</p>
    </div>
    
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-end gap-3 bg-white p-3 rounded-lg shadow-sm border border-gray-200">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tipo de Delito</label>
            <div class="min-w-[250px] max-w-sm">
                <select id="category-select" name="categories[]" multiple autocomplete="off" class="block w-full text-sm">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCategories) ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Año</label>
            <select name="year" class="block w-full rounded-md border-gray-300 shadow-sm px-3 py-1.5 border text-sm">
                <option value="">Todos</option>
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Mes</label>
            <select name="month" class="block w-full rounded-md border-gray-300 shadow-sm px-3 py-1.5 border text-sm">
                <option value="">Todos</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Desde</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="block w-full rounded-md border-gray-300 shadow-sm px-3 py-1.5 border text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Hasta</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="block w-full rounded-md border-gray-300 shadow-sm px-3 py-1.5 border text-sm">
            </div>
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-md hover:bg-indigo-700 text-sm font-medium transition-colors">Filtrar</button>
            @if($year || $month || $startDate || $endDate || count($selectedCategories) > 0)
                <a href="{{ route('admin.dashboard') }}" class="ml-2 text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            @endif
        </div>
    </form>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm font-medium">Total Incidentes</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $totalIncidents }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm font-medium">Incidentes Hoy</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $incidentsToday }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm font-medium">Total Usuarios</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm font-medium">Usuarios Activos</h3>
        <p class="text-3xl font-bold text-green-600">{{ $activeUsers }}</p>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Line Chart: Temporal Pattern -->
    <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Evolución Temporal de Reportes</h3>
        <canvas id="trendChart" height="100"></canvas>
    </div>
    
    <!-- Doughnut Chart: Categorial Pattern -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Incidentes por Categoría</h3>
        <canvas id="categoryChart" height="200"></canvas>
    </div>
</div>

<!-- New Operational Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Crime Clock Chart (Radar/Bar) -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Reloj del Delito (Distribución Horaria)</h3>
        <canvas id="clockChart" height="200"></canvas>
    </div>
    
    <!-- Top Localities Chart (Horizontal Bar) -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Top 5 Localidades Afectadas</h3>
        <canvas id="localityChart" height="200"></canvas>
    </div>
</div>

<!-- WebMap -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Mapa de Calor Institucional (Hot Spots)</h3>
    </div>
    <div id="adminMap" style="height: 500px; width: 100%; border-radius: 0.5rem; z-index: 1;"></div>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Detalle Analítico de Incidentes</h3>
    </div>
    <div class="table-responsive border border-gray-200 rounded-lg" style="max-height: 65vh; overflow: auto; position: relative;">
        <table id="incidents-table" class="display w-full text-sm text-left">
            <thead>
                <tr>
                    <th class="border-b-2 p-2 w-10 text-center">#</th>
                    <th class="border-b-2 p-2">Fecha del Incidente</th>
                    <th class="border-b-2 p-2">Categoría</th>
                    <th class="border-b-2 p-2">Ubicación / Descripción</th>
                    <th class="border-b-2 p-2">Detalle</th>
                    <th class="border-b-2 p-2">Nivel de Privacidad</th>
                    <th class="border-b-2 p-2">Localidad</th>
                </tr>
                <tr class="filters-row">
                    <th class="border-b-2 p-2 w-10 text-center"></th>
                    <th class="border-b-2 p-2" data-idx="1"></th>
                    <th class="border-b-2 p-2" data-idx="2"></th>
                    <th class="border-b-2 p-2" data-idx="3"></th>
                    <th class="border-b-2 p-2" data-idx="4"></th>
                    <th class="border-b-2 p-2" data-idx="5"></th>
                    <th class="border-b-2 p-2" data-idx="6"></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.css" rel="stylesheet">
<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" crossorigin="anonymous">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" crossorigin="anonymous">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-buttons-dt@2.4.2/css/buttons.dataTables.min.css">
<style>
    /* Native Sticky Table Header */
    #incidents-table thead {
        position: sticky;
        top: 0;
        z-index: 20;
    }
    #incidents-table thead th {
        background-color: #ffffff;
        box-shadow: inset 0 -1px 0 #e5e7eb;
        border-bottom: none !important;
    }
    
    /* DataTables Tailwind Fixes */
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        margin-left: 0.5rem;
        outline: none;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 1px #4f46e5;
    }
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 1rem;
    }
    .dataTables_wrapper .dt-buttons .dt-button {
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 0.75rem;
        color: #374151;
        font-weight: 500;
        margin-right: 0.25rem;
    }
    .dataTables_wrapper .dt-buttons .dt-button:hover {
        background: #e5e7eb;
    }
    
    /* Tailwind adjustments for Tom Select */
    .ts-control {
        border-color: #d1d5db;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        min-height: 38px;
    }
    .ts-wrapper.multi .ts-control > div {
        background: #4f46e5;
        color: white;
        border-radius: 0.25rem;
        padding: 2px 6px;
        border: none;
    }
    .ts-wrapper.multi .ts-control > div.active {
        background: #4338ca;
        color: white;
    }
</style>
@endpush

@push('scripts')
<!-- DataTables JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-buttons@2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-buttons@2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-buttons@2.4.2/js/buttons.print.min.js"></script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Leaflet.heat plugin -->
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<!-- Leaflet.markercluster plugin -->
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 0. Init Tom Select ---
        new TomSelect('#category-select', {
            plugins: ['remove_button'],
            placeholder: 'Seleccione delitos...',
            maxOptions: null
        });

        // --- 1. Line Chart (Temporal) ---
        const trendData = @json($incidentsTrend);
        const labels = trendData.map(d => d.date);
        const counts = trendData.map(d => d.count);

        if (trendData.length === 0) {
            // Show empty state message instead of empty chart
            const canvas = document.getElementById('trendChart');
            const wrapper = canvas.parentElement;
            canvas.style.display = 'none';
            const msg = document.createElement('div');
            msg.style.cssText = 'display:flex;flex-direction:column;align-items:center;justify-content:center;height:180px;color:#9ca3af;';
            msg.innerHTML = '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:0.75rem;opacity:0.4"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 4-6"/></svg><p style="font-size:0.875rem;font-weight:500;">Sin datos para el período seleccionado</p><p style="font-size:0.75rem;margin-top:0.25rem;">Prueba ajustando los filtros de fecha</p>';
            wrapper.appendChild(msg);
        } else {
            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Incidentes',
                        data: counts,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: trendData.length === 1 ? 8 : 4,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (items) => items[0].label,
                                label: (item) => ` ${item.raw} incidente${item.raw !== 1 ? 's' : ''}`
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // --- 2. Doughnut Chart (Categories) ---
        const catData = @json($incidentsByCategory);
        const catLabels = catData.map(d => d.name);
        const catCounts = catData.map(d => d.count);
        const catColors = catData.map(d => d.color);
        
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catCounts,
                    backgroundColor: catColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // --- 2a. Reloj del Delito (Hourly Clock) ---
        const clockData = @json($incidentsByHour);
        new Chart(document.getElementById('clockChart'), {
            type: 'bar',
            data: {
                labels: clockData.map(d => d.hour),
                datasets: [{
                    label: 'Incidentes',
                    data: clockData.map(d => d.count),
                    backgroundColor: 'rgba(239, 68, 68, 0.7)', // Red base
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { grid: { display: false } },
                    y: { beginAtZero: true } 
                }
            }
        });

        // --- 2b. Top Localidades (Horizontal Bar) ---
        const locData = @json($topLocalidades);
        new Chart(document.getElementById('localityChart'), {
            type: 'bar',
            data: {
                labels: locData.map(d => d.name),
                datasets: [{
                    label: 'Incidentes',
                    data: locData.map(d => d.count),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)', // Blue base
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { beginAtZero: true },
                    y: { grid: { display: false } }
                }
            }
        });

        // --- 3. Leaflet Heatmap & MarkerCluster ---
        // Initialize Map
        const map = L.map('adminMap').setView([4.6097, -74.0817], 12); // Bogotá coordinates
        
        // Base Layers
        const lightMap = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        });
        const darkMap = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        });

        // Add default base layer
        lightMap.addTo(map);

        // Overlay Groups
        const heatLayerGroup = L.layerGroup();
        const localidadesLayerGroup = L.layerGroup();
        const markerClusterGroup = L.markerClusterGroup({
            chunkedLoading: true,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

        // Initially add the marker cluster to the map (user can toggle)
        map.addLayer(markerClusterGroup);

        // Layer Control
        const baseMaps = {
            "Mapa Claro": lightMap,
            "Mapa Oscuro": darkMap
        };
        const overlayMaps = {
            "Agrupación de Incidentes": markerClusterGroup,
            "Mapa de Calor": heatLayerGroup,
            "Límites Locales": localidadesLayerGroup
        };

        L.control.layers(baseMaps, overlayMaps, { position: 'topright' }).addTo(map);

        // Build URL for geojson
        const mapUrl = new URL(window.location.origin + '/api/geojson');
        @if($year) mapUrl.searchParams.append('year', '{{ $year }}'); @endif
        @if($month) mapUrl.searchParams.append('month', '{{ $month }}'); @endif
        @if($startDate) mapUrl.searchParams.append('start_date', '{{ $startDate }}'); @endif
        @if($endDate) mapUrl.searchParams.append('end_date', '{{ $endDate }}'); @endif
        @if(!empty($selectedCategories))
            @foreach($selectedCategories as $cat)
                mapUrl.searchParams.append('categories[]', '{{ $cat }}');
            @endforeach
        @endif

        // Fetch data and Render Layers
        fetch(mapUrl)
            .then(res => res.json())
            .then(data => {
                const heatPoints = [];

                data.features.forEach(f => {
                    const coords = f.geometry.coordinates;
                    const lat = coords[1];
                    const lng = coords[0];
                    const props = f.properties;

                    // 1. Data for Heatmap
                    heatPoints.push([lat, lng, 0.6]);

                    // 2. Data for MarkerCluster
                    const popupContent = `
                        <div class="px-1 py-1 min-w-[200px]">
                            <h4 class="font-bold text-gray-800 border-b pb-1 mb-2 text-sm" style="border-bottom-color: ${props.color}">
                                <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background-color:${props.color}; margin-right:5px;"></span>
                                ${props.category}
                            </h4>
                            <p class="text-xs text-gray-600 mb-1"><strong>Fecha:</strong> ${new Date(props.created_at).toLocaleString('es-CO')}</p>
                            <p class="text-sm text-gray-800 mt-2 mb-2 line-clamp-3">${props.description || 'Sin descripción'}</p>
                            <div class="mt-2 text-right">
                                <span class="px-2 py-0.5 mt-1 text-[10px] font-semibold rounded bg-indigo-100 text-indigo-700 uppercase border border-indigo-200">
                                    ${props.status}
                                </span>
                            </div>
                        </div>
                    `;

                    // Create marker style
                    const marker = L.circleMarker([lat, lng], {
                        radius: 8,
                        fillColor: props.color || '#3388ff',
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.95
                    });

                    marker.bindPopup(popupContent);
                    markerClusterGroup.addLayer(marker);
                });
                
                // Add heatmap layer to its group
                L.heatLayer(heatPoints, {
                    radius: 20,
                    blur: 15,
                    maxZoom: 15,
                    max: 3.0,
                    gradient: {0.4: 'blue', 0.6: 'cyan', 0.7: 'lime', 0.8: 'yellow', 1.0: 'red'}
                }).addTo(heatLayerGroup);
                
                // Update DataTable
                updateTableData(data.features);
            });

        // Fetch Localities Polygons
        fetch('/api/localidades-geojson')
            .then(res => res.json())
            .then(data => {
                L.geoJSON(data, {
                    style: function (feature) {
                        return {
                            color: '#64748b',
                            weight: 1.5,
                            opacity: 0.6,
                            fillOpacity: 0.05,
                            dashArray: '4'
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties && feature.properties.nombre) {
                            layer.bindTooltip(feature.properties.nombre, {
                                permanent: true, 
                                direction: 'center', 
                                className: 'bg-transparent border-0 shadow-none text-gray-500 font-semibold text-xs',
                                opacity: 0.7
                            });
                        }
                    }
                }).addTo(localidadesLayerGroup);
            });

        let dataTableInstance = null;

        function updateTableData(features) {
            try {
                if (!dataTableInstance) {
                dataTableInstance = $('#incidents-table').DataTable({
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    pageLength: 10,
                    orderCellsTop: true,
                    language: {
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        infoEmpty: "Mostrando 0 a 0 de 0 registros",
                        infoFiltered: "(filtrado de _MAX_ registros totales)",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior"
                        },
                        zeroRecords: "No se encontraron incidentes"
                    },
                    order: [[1, 'desc']],
                    columnDefs: [
                        {
                            searchable: false,
                            orderable: false,
                            targets: 0,
                            className: 'text-center font-bold text-gray-500 bg-gray-50 border-r border-gray-100'
                        }
                    ],
                    initComplete: function () {
                        this.api().columns().every(function () {
                            let column = this;
                            if (column.index() === 0) return;
                            
                            let cell = $('.filters-row th[data-idx="' + column.index() + '"]');
                            $('<select class="w-full border-gray-300 rounded text-xs py-1 filter-select" data-column-index="' + column.index() + '"><option value="">Todos</option></select>')
                                .appendTo(cell.empty());
                        });
                    }
                });

                $(document).on('change', '.filter-select', function () {
                    if (!dataTableInstance) return;
                    let columnIdx = $(this).data('column-index');
                    let val = $.fn.dataTable.util.escapeRegex($(this).val());
                    
                    $('.filter-select[data-column-index="' + columnIdx + '"]').val($(this).val());
                    dataTableInstance.column(columnIdx).search(val ? '^' + val + '$' : '', true, false).draw();
                });

                dataTableInstance.on('order.dt search.dt', function () {
                    let i = 1;
                    dataTableInstance.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                        this.data(i++);
                    });
                });
            }

            const tableData = features.map(f => {
                const props = f.properties;
                const dateObj = new Date(props.incident_date || props.created_at);
                const formattedDate = dateObj.toLocaleString('es-CO');

                return [
                    '', // placeholder for index
                    formattedDate,
                    props.category || 'Otro',
                    props.location_description || 'Sin descripción',
                    props.description || 'Sin detalle',
                    props.privacy_level || 'Público',
                    props.localidad || 'N/A'
                ];
            });

            dataTableInstance.clear();
            dataTableInstance.rows.add(tableData);
            dataTableInstance.draw();
            
            // Re-populate the column filters based on new data
            if (dataTableInstance) {
                dataTableInstance.columns().every(function () {
                    let column = this;
                    if (column.index() === 0) return;
                    
                    let selects = $('.filter-select[data-column-index="' + column.index() + '"]');
                    if(selects.length === 0) return;
                    
                    let currentVal = selects.first().val();
                    selects.empty().append('<option value="">Todos</option>');
                    
                    column.data().unique().sort().each(function (d, j) {
                        if (d) {
                            selects.append('<option value="' + d + '">' + d + '</option>');
                        }
                    });
                    selects.val(currentVal);
                });
            }
            
            } catch (err) {
                $('#incidents-table').parent().prepend('<div class="bg-red-100 text-red-700 p-3 rounded mb-3">Error cargando tabla: ' + err.message + '</div>');
                console.error('DataTables Error:', err);
            }
        }
    });
</script>
@endpush
@endsection
