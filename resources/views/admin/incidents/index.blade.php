@extends('admin.layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Incidentes</h1>
        <p class="text-gray-600">Verificar, rechazar y moderar los reportes ciudadanos.</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    
    <!-- Filtros Superiores -->
    <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <form method="GET" action="{{ route('admin.incidents.index') }}" class="flex gap-4 items-center w-full max-w-3xl">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar descripción, ubicación..." class="flex-1 rounded-md border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <select name="status" class="rounded-md border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todos los Estados</option>
                <option value="reported" {{ request('status') === 'reported' ? 'selected' : '' }}>Reportado (Pendiente)</option>
                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verificado (Aprobado)</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazado (Descartado)</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium transition-colors">Filtrar</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.incidents.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            @endif
        </form>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                <tr>
                    <th scope="col" class="px-6 py-3">ID / Fecha</th>
                    <th scope="col" class="px-6 py-3">Categoría</th>
                    <th scope="col" class="px-6 py-3">Descripción / Ubicación</th>
                    <th scope="col" class="px-6 py-3">Autor</th>
                    <th scope="col" class="px-6 py-3 text-center">Estado Actual</th>
                    <th scope="col" class="px-6 py-3 text-right">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $incident)
                <tr class="bg-white border-b hover:bg-gray-50" id="row-{{ $incident->id }}">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        <div class="text-indigo-600 font-bold">#{{ $incident->id }}</div>
                        <div class="text-xs text-gray-500">{{ $incident->incident_date ? $incident->incident_date->format('Y-m-d H:i') : $incident->created_at->format('Y-m-d H:i') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded" style="background-color: {{ $incident->category->color }}20; color: {{ $incident->category->color }}; border: 1px solid {{ $incident->category->color }}40;">
                            {{ $incident->category->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 max-w-xs">
                        <div class="truncate font-medium text-gray-800" title="{{ $incident->description }}">{{ $incident->description }}</div>
                        <div class="truncate text-xs text-gray-500 mt-1" title="{{ $incident->location_description }}">📍 {{ $incident->location_description ?? 'Sin descripción de ubicación' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($incident->privacy_level === 'IDENTIFIED' && $incident->user)
                            <div class="font-medium text-gray-900">{{ $incident->user->name }}</div>
                        @else
                            <div class="text-gray-500 italic">Anónimo</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <select onchange="updateStatus({{ $incident->id }}, this.value)" class="text-xs rounded border-gray-300 shadow-sm py-1 px-2 focus:border-indigo-500 focus:ring-indigo-500" 
                            style="background-color: {{ $incident->status === 'verified' ? '#d1fae5' : ($incident->status === 'rejected' ? '#fee2e2' : '#fef3c7') }}; font-weight: 600;">
                            <option value="reported" {{ $incident->status === 'reported' ? 'selected' : '' }}>Reportado</option>
                            <option value="verified" {{ $incident->status === 'verified' ? 'selected' : '' }}>Verificado</option>
                            <option value="rejected" {{ $incident->status === 'rejected' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="/?incident_id={{ $incident->id }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded transition-colors text-xs font-medium border border-indigo-200">
                            Ver Mapa
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No se encontraron incidentes con los filtros actuales.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    <div class="p-4 border-t border-gray-200">
        {{ $incidents->links() }}
    </div>
</div>

<!-- Modal para feedback AJAX -->
<div id="toast" class="fixed bottom-5 right-5 transform transition-transform duration-300 translate-y-20 opacity-0 bg-gray-900 text-white px-4 py-3 rounded shadow-lg flex items-center gap-3 z-50">
    <svg id="toast-icon-success" class="w-5 h-5 text-green-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    <svg id="toast-icon-error" class="w-5 h-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    <span id="toast-message" class="text-sm font-medium">Actualizado exitosamente</span>
</div>

@push('scripts')
<script>
    function updateStatus(id, newStatus) {
        fetch(`/admin/api/incidents/${id}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            showToast('Estado actualizado correctamente', 'success');
            
            // Actualizar colores del select
            const select = document.querySelector(`#row-${id} select`);
            if (newStatus === 'verified') select.style.backgroundColor = '#d1fae5';
            else if (newStatus === 'rejected') select.style.backgroundColor = '#fee2e2';
            else select.style.backgroundColor = '#fef3c7';
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al actualizar el estado', 'error');
        });
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const msg = document.getElementById('toast-message');
        const iconSuccess = document.getElementById('toast-icon-success');
        const iconError = document.getElementById('toast-icon-error');

        msg.textContent = message;
        
        iconSuccess.classList.add('hidden');
        iconError.classList.add('hidden');
        
        if (type === 'success') iconSuccess.classList.remove('hidden');
        else iconError.classList.remove('hidden');

        toast.classList.remove('translate-y-20', 'opacity-0');
        
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3000);
    }
</script>
@endpush
@endsection
