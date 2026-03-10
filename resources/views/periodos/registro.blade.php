<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Periodos') }}
        </h2>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <div class="py-12"> --}}
    <div class="w-1/2 mx-auto sm:px-4 lg:px-6">
        <div class="p-6">
            <form method="POST" action="{{ route('periodos.registro.store') }}">
                @csrf
                <div class="mb-4 flex items-center">
                    <label for="mes" class="text-gray-700 font-bold w-48 mr-4">Mes:</label>
                    @php
                        $meses = explode(',', env('APP_MESES_SELECTOR'));
                        //dd($meses[1]);                        
                    @endphp
                    <select id="mes" name="mes"
                        class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required>
                        <option value="" class="text-gray-500">📅 Seleccione un mes</option>
                        @foreach ($meses as $mes)
                            <option value="{{ $mes }}" class="py-1">{{ $mes }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4 flex items-center">
                    <label for="anio" class="text-gray-700 font-bold w-48 mr-4">Año:</label>
                    @php
                        $año_inicio = env('APP_AÑO_INICIO_SELECTOR');
                        $año_fin = env('APP_AÑO_FIN_SELECTOR');
                        //dd($año_inicio);
                    @endphp
                    <select id="anio" name="anio"
                        class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required wire:model.live="periodosSeleccionados">
                        <option value="" class="text-gray-500">📅 Seleccione un año</option>
                        @for ($i = $año_inicio; $i <= $año_fin; $i++)
                            <option name="anio" value="{{ $i }}" class="py-1"> 
                                {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="mb-4 flex items-center">
                    <label for="descripcion" class="text-gray-700 font-bold w-48 mr-4">Descripción:</label>
                    <textarea id="descripcion" name="descripcion"
                        class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required></textarea>
                </div>
                <div class="mb-4 flex items-center">
                    <label for="fecha_inicio" class="text-gray-700 font-bold w-48 mr-4">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio"
                        class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required>
                </div>
                <div class="mb-4 flex items-center">
                    <label for="fecha_fin" class="text-gray-700 font-bold w-48 mr-4">Fecha de Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin"
                        class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required>
                </div>
                <div class="mb-4 flex items-center">
                    <label for="activo" class="text-gray-700 font-bold w-48 mr-4">Activo:</label>
                    <div class="flex items-center">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" id="activo" name="activo" value="1"
                            class="h-4 w-4 text-vino-600 focus:ring-vino-500 border-vino-300 rounded" checked>
                        <label for="activo" class="ml-2 text-sm text-gray-600">Periodo activo</label>
                    </div>
                </div>
                <div class="flex items-center justify-end">
                    <button type="submit"
                        class="mt-4 bg-vino-900 text-white py-2 px-4 rounded-md">
                        Registrar Periodo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTable de Períodos -->
    <div class="w-full mx-auto sm:px-4 lg:px-6 mt-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Periodos Registrados</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Año</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha Inicio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha Fin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cambiar Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($periodos ?? [] as $periodo)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $periodo->mes ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $periodo->axo ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $periodo->descripcion ?? 'Sin descripción' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $periodo->fecha_inicio ? \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $periodo->fecha_fin ? \Carbon\Carbon::parse($periodo->fecha_fin)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $periodo->activo ?? false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $periodo->is_active ?? false ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="toggle-status h-4 w-4 text-vino-600 focus:ring-vino-500 border-vino-300 rounded"
                                            data-periodo-id="{{ $periodo->id }}"
                                            {{ $periodo->is_active ?? false ? 'checked' : '' }}>
                                        <label
                                            class="ml-2 text-sm text-gray-600">{{ $periodo->is_active ?? false ? 'Activo' : 'Inactivo' }}</label>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay períodos
                                    registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- </div> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar cambios en los checkboxes de estado
            document.querySelectorAll('.toggle-status').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const periodoId = this.getAttribute('data-periodo-id');
                    const isChecked = this.checked;

                    // Hacer petición AJAX para cambiar el estado
                    fetch(`/periodos/${periodoId}/toggle-status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                active: isChecked
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Actualizar el texto del label
                                const label = this.nextElementSibling;
                                label.textContent = data.status ? 'Activo' : 'Inactivo';

                                // Actualizar el badge de estado en la columna anterior
                                const row = this.closest('tr');
                                const statusBadge = row.querySelector('span');
                                if (data.status) {
                                    statusBadge.className =
                                        'px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800';
                                    statusBadge.textContent = 'Activo';
                                } else {
                                    statusBadge.className =
                                        'px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800';
                                    statusBadge.textContent = 'Inactivo';
                                }
                            } else {
                                // Revertir el checkbox si hubo error
                                this.checked = !isChecked;
                                alert('Error al actualizar el estado');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Revertir el checkbox si hubo error
                            this.checked = !isChecked;
                            alert('Error al actualizar el estado');
                        });
                });
            });
        });
    </script>
</x-app-layout>
