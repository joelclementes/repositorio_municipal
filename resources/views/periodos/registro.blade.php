<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Periodos') }}
        </h2>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <button id="btnRegistroPeriodo" class="bg-vino-900 text-white py-2 px-4 rounded-md mt-4 ml-6">
        {{ __('Periodo Nuevo') }}
    </button>

    {{-- <div class="py-12"> --}}
    <div class="w-1/2 mx-auto sm:px-4 lg:px-6" id="registro-periodo" {{ $errors->any() ? '' : 'hidden' }}>
        <div class="p-6">
            {{-- Mostrar errores de validación --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>¡Atención!</strong>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('periodos.registro.store') }}">
                @csrf
                <div class="mb-4 flex items-center">
                    <label for="mes" class="text-gray-700 font-bold w-48 mr-4">Mes:</label>
                    @php
                        $meses = explode(',', env('APP_MESES_SELECTOR'));
                        //dd($meses[1]);
                    @endphp
                    <select id="mes" name="mes"
                        class="w-full px-3 py-2 text-sm border {{ $errors->has('mes') ? 'border-red-500' : 'border-vino-900' }} rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required>
                        <option value="" class="text-gray-500">📅 Seleccione un mes</option>
                        @foreach ($meses as $mes)
                            <option value="{{ $mes }}" {{ old('mes') == $mes ? 'selected' : '' }} class="py-1">{{ $mes }}</option>
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
                        class="w-full px-3 py-2 text-sm border {{ $errors->has('anio') ? 'border-red-500' : 'border-vino-900' }} rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required wire:model.live="periodosSeleccionados">
                        <option value="" class="text-gray-500">📅 Seleccione un año</option>
                        @for ($i = $año_inicio; $i <= $año_fin; $i++)
                            <option name="anio" value="{{ $i }}" {{ old('anio') == $i ? 'selected' : '' }} class="py-1">
                                {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="mb-4 flex items-center">
                    <label for="descripcion" class="text-gray-700 font-bold w-48 mr-4">Descripción:</label>
                    <textarea id="descripcion" name="descripcion"
                        class="w-full px-3 py-2 text-sm border {{ $errors->has('descripcion') ? 'border-red-500' : 'border-vino-900' }} rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required placeholder="La descripción que especifique aquí se mostrará cuando el Ente obligado seleccione un periodo para subir archivos.">{{ old('descripcion') }}</textarea>
                </div>
                <div class="mb-4 flex items-center">
                    <label for="fecha_inicio" class="text-gray-700 font-bold w-48 mr-4">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}"
                        class="w-full px-3 py-2 text-sm border {{ $errors->has('fecha_inicio') ? 'border-red-500' : 'border-vino-900' }} rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required>
                </div>
                <div class="mb-4 flex items-center">
                    <label for="fecha_fin" class="text-gray-700 font-bold w-48 mr-4">Fecha de Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}"
                        class="w-full px-3 py-2 text-sm border {{ $errors->has('fecha_fin') ? 'border-red-500' : 'border-vino-900' }} rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
                        required>
                </div>
                <div class="mb-4 flex items-center">
                    <label for="activo" class="text-gray-700 font-bold w-48 mr-4">Activo:</label>
                    <div class="flex items-center">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" id="activo" name="activo" value="1" {{ old('activo', '1') ? 'checked' : '' }}
                            class="h-4 w-4 text-vino-600 focus:ring-vino-500 border-vino-300 rounded">
                        <label for="activo" class="ml-2 text-sm text-gray-600">Periodo activo</label>
                    </div>
                </div>
                <div class="flex items-center justify-end">
                    <x-button type="submit" class="ms-4 bg-vino-900 hover:bg-vino-800 focus:bg-vino-800 active:bg-vino-900">
                        Enviar
                    </x-button>
                    <x-button class="ms-4 bg-gray-900 hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900"
                        id="btnCancelarPeriodo" type="button">
                        {{ __('Cancelar') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTable de Períodos -->
    <div class="w-full mx-auto sm:px-4 lg:px-6 mt-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Períodos Registrados</h3>

                    <!-- Campo de búsqueda -->
                    <div class="relative">
                        <form method="GET" action="{{ route('periodos.registro.index') }}">
                            <div class="flex items-center space-x-2">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Buscar períodos..."
                                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-vino-500 focus:border-vino-500">
                                <button type="submit"
                                    class="bg-vino-600 hover:bg-vino-700 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-vino-500">
                                    🔍 Buscar
                                </button>
                                @if (request('search'))
                                    <a href="{{ route('periodos.registro.index') }}"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md focus:outline-none">
                                        ✖ Limpiar
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
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
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    @if (request('search'))
                                        No se encontraron períodos con "{{ request('search') }}"
                                    @else
                                        No hay períodos registrados
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($periodos->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Mostrando {{ $periodos->firstItem() }} a {{ $periodos->lastItem() }} de
                            {{ $periodos->total() }} resultados
                        </div>
                        <div class="flex items-center space-x-2">
                            {{ $periodos->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    {{-- </div> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar el botón de toggle para mostrar/ocultar el formulario de registro
            const btnRegistroPeriodo = document.getElementById('btnRegistroPeriodo');
            const btnCancelarPeriodo = document.getElementById('btnCancelarPeriodo');
            const registroDiv = document.getElementById('registro-periodo');

            // Verificar si hay errores al cargar la página y ajustar el estado del botón
            @if($errors->any())
                btnRegistroPeriodo.hidden = true;
            @endif

            btnRegistroPeriodo.addEventListener('click', function() {
                if (registroDiv.hasAttribute('hidden')) {
                    registroDiv.removeAttribute('hidden');
                    document.getElementById('btnRegistroPeriodo').hidden=true;

                } else {
                    registroDiv.setAttribute('hidden', '');
                    document.getElementById('btnRegistroPeriodo').hidden=false;
                }
            });

            btnCancelarPeriodo.addEventListener('click', function() {
                registroDiv.setAttribute('hidden', '');
                document.getElementById('btnRegistroPeriodo').hidden=false;
            });

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
                                if (label) {
                                    label.textContent = data.status ? 'Activo' : 'Inactivo';
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
