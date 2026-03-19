<div>
    <!-- Select de Periodo centrado -->
    <div class="w-1/3 mx-auto mb-8">
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
            required wire:model.live="periodosSeleccionados">
            <option value="" class="text-gray-500">📅 Seleccione un periodo</option>
            @foreach ($this->periodos as $periodo)
                <option value="{{ $periodo->id }}" class="py-1" {{ !$periodo->is_active ? 'disabled' : '' }}>
                    {{ ucfirst($periodo->descripcion) }}
                </option>
            @endforeach
        </select>
    </div>

    @if(!empty($periodosSeleccionados) && $this->revisores->count() > 0)
        <!-- Contenedor principal con dos columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna izquierda: Lista de Revisores -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow border">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            Lista de Revisores
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Selecciona un revisor para ver su progreso
                        </p>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @foreach ($this->revisores as $revisor)
                            <div 
                                wire:click="seleccionarRevisor({{ $revisor->id }})"
                                class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors {{ $revisorSeleccionado == $revisor->id ? 'bg-vino-50 border-vino-200' : '' }}">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-vino-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-vino-800">
                                                {{ strtoupper(substr($revisor->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $revisor->name }}
                                        </p>
                                        @if($revisorSeleccionado == $revisor->id)
                                            <p class="text-xs text-vino-600">
                                                ✓ Seleccionado
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Progreso del Revisor -->
            <div class="lg:col-span-2">
                @if($revisorSeleccionado)
                    <div class="bg-white rounded-lg shadow border">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                Progreso de Revisión
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $this->revisores->where('id', $revisorSeleccionado)->first()->name ?? 'Revisor seleccionado' }}
                            </p>
                        </div>
                        <div class="p-6">
                            @php 
                                $progresoPorEnte = $this->progresoPorEnte;
                                $totalGlobal = $progresoPorEnte->sum('total');
                                $completadosGlobal = $progresoPorEnte->sum('completados');
                                $porcentajeGlobal = $totalGlobal > 0 ? round(($completadosGlobal / $totalGlobal) * 100, 2) : 0;
                            @endphp
                            
                            <!-- Estadísticas globales -->
                            <div class="grid grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ $totalGlobal }}</div>
                                    <div class="text-sm text-gray-500">Total Global</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $completadosGlobal }}</div>
                                    <div class="text-sm text-gray-500">Completados Global</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $porcentajeGlobal }}%</div>
                                    <div class="text-sm text-gray-500">Progreso Global</div>
                                </div>
                            </div>

                            <!-- Progreso por Ente -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">
                                    Progreso por Ente
                                </h4>
                                
                                @forelse($progresoPorEnte as $ente)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h5 class="font-medium text-gray-900">
                                                {{ $ente['ente_nombre'] }}
                                            </h5>
                                            <div class="text-sm text-gray-500">
                                                {{ $ente['completados'] }}/{{ $ente['total'] }} documentos
                                            </div>
                                        </div>
                                        
                                        <!-- Estadísticas del ente -->
                                        <div class="grid grid-cols-3 gap-4 mb-4 text-center">
                                            <div>
                                                <div class="text-lg font-bold text-gray-700">{{ $ente['total'] }}</div>
                                                <div class="text-xs text-gray-500">Total</div>
                                            </div>
                                            <div>
                                                <div class="text-lg font-bold text-green-600">{{ $ente['completados'] }}</div>
                                                <div class="text-xs text-gray-500">Completados</div>
                                            </div>
                                            <div>
                                                <div class="text-lg font-bold text-blue-600">{{ $ente['porcentaje'] }}%</div>
                                                <div class="text-xs text-gray-500">Progreso</div>
                                            </div>
                                        </div>

                                        <!-- Barra de progreso del ente -->
                                        <div class="w-full bg-gray-200 rounded-full h-4">
                                            <div 
                                                class="h-4 rounded-full transition-all duration-300 {{ $ente['porcentaje'] >= 75 ? 'bg-green-500' : ($ente['porcentaje'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                style="width: {{ min($ente['porcentaje'], 100) }}%">
                                            </div>
                                        </div>
                                        
                                        <div class="text-xs text-gray-500 mt-2">
                                            @if($ente['total'] == 0)
                                                Sin documentos asignados en este período
                                            @elseif($ente['porcentaje'] == 100)
                                                ✅ Todas las revisiones completadas
                                            @elseif($ente['porcentaje'] >= 75)
                                                🟢 Progreso excelente
                                            @elseif($ente['porcentaje'] >= 50)
                                                🟡 Progreso moderado
                                            @else
                                                🔴 Requiere atención
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <div class="text-gray-400 mb-2">
                                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500">
                                            No hay entes asignados para este revisor
                                        </p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="text-xs text-gray-500 mt-6 text-center">
                                Estados considerados como completados: Aprobado (2), Observado (3)
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow border">
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                Selecciona un revisor
                            </h3>
                            <p class="text-sm text-gray-500">
                                Haz clic en un revisor de la lista para ver su progreso de revisión
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @elseif(!empty($periodosSeleccionados) && $this->revisores->count() == 0)
        <!-- Mensaje cuando no hay revisores para el periodo -->
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.239" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                No hay revisores disponibles
            </h3>
            <p class="text-sm text-gray-500">
                No se encontraron revisores asignados para el período seleccionado
            </p>
        </div>
    @else
        <!-- Mensaje inicial -->
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4h4a2 2 0 012 2v6a2 2 0 01-2 2h-4v4a4 4 0 11-8 0v-4H4a2 2 0 01-2-2V9a2 2 0 012-2h4z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                Selecciona un período
            </h3>
            <p class="text-sm text-gray-500">
                Primero selecciona un período para ver los revisores disponibles
            </p>
        </div>
    @endif
</div>
