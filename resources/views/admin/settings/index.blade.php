@extends('admin.layouts.admin')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Configuración Global</h1>
        <p class="text-gray-600">Ajusta los valores por defecto del sistema.</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Sistema de Alertas Ciudadanas</h2>
            <p class="text-sm text-gray-500 mb-6">Define los valores por defecto que se le asignarán a los usuarios cuando no hayan personalizado su perfil.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Default Threshold -->
                <div>
                    <label for="default_alert_threshold" class="block text-sm font-medium text-gray-700 mb-1">Umbral por defecto (Incidentes)</label>
                    <input type="number" min="1" id="default_alert_threshold" name="default_alert_threshold" value="{{ $settings['default_alert_threshold'] ?? 10 }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Cantidad de incidentes necesarios para disparar una alerta.</p>
                </div>

                <!-- Default Timeframe -->
                <div>
                    <label for="default_alert_timeframe" class="block text-sm font-medium text-gray-700 mb-1">Rango de horas por defecto</label>
                    <input type="number" min="1" id="default_alert_timeframe" name="default_alert_timeframe" value="{{ $settings['default_alert_timeframe'] ?? 3 }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">El bloque de tiempo en el que se evalúa la cantidad de incidentes.</p>
                </div>

                <!-- Default Cooldown -->
                <div>
                    <label for="default_alert_cooldown" class="block text-sm font-medium text-gray-700 mb-1">Periodo de enfriamiento por defecto</label>
                    <input type="number" min="1" id="default_alert_cooldown" name="default_alert_cooldown" value="{{ $settings['default_alert_cooldown'] ?? 12 }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Horas de espera antes de volver a alertar a un usuario por la misma localidad.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t mt-4">
                <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition-colors shadow-sm">
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
