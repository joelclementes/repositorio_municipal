<div>
    {{-- Selectores de Ente y Año --}}
    <div class="flex flex-wrap gap-4 mb-6 items-end">
        <div class="w-full md:w-1/3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ayuntamiento / Ente</label>
            <select class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#6c143a] focus:border-[#6c143a] bg-white"
                wire:model.live="enteSeleccionado">
                <option value="">🏛️ Seleccione un ente</option>
                @foreach ($this->entes as $ente)
                    <option value="{{ $ente->id }}">{{ $ente->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full md:w-1/4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
            <select class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#6c143a] focus:border-[#6c143a] bg-white"
                wire:model.live="axoSeleccionado">
                <option value="">📅 Seleccione un año</option>
                @foreach ($this->axosDisponibles as $axo)
                    <option value="{{ $axo }}">{{ $axo }}</option>
                @endforeach
            </select>
        </div>

        {{-- Botones de exportación --}}
        @if ($this->datosReporte)
            <div class="flex gap-2">
                <a href="{{ route('reportes.obligaciones.pdf', ['ente' => $enteSeleccionado, 'axo' => $axoSeleccionado]) }}"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-red-700 text-white text-sm font-medium rounded-md hover:bg-red-800 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    PDF
                </a>
                <a href="{{ route('reportes.obligaciones.excel', ['ente' => $enteSeleccionado, 'axo' => $axoSeleccionado]) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-700 text-white text-sm font-medium rounded-md hover:bg-green-800 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel
                </a>
            </div>
        @endif
    </div>

    {{-- Loading indicator --}}
    <div wire:loading wire:target="enteSeleccionado, axoSeleccionado" class="text-center py-8">
        <div class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-[#6c143a]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm text-gray-600">Generando reporte...</span>
        </div>
    </div>

    {{-- Reporte --}}
    @if ($this->datosReporte)
        <div wire:loading.remove wire:target="enteSeleccionado, axoSeleccionado" class="overflow-x-auto">

            {{-- Header institucional --}}
            <div class="mb-6 bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('assets/images/LOGO_LEGISLATURA.jpg') }}" alt="Logo Congreso" class="h-20 w-auto">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-[#6c143a]">Secretaría de Fiscalización</p>
                        <p class="text-sm font-semibold text-gray-700">Departamento de Capacitación, Asesoría, Revisión y Supervisión a Municipios</p>
                        <p class="text-sm font-semibold text-gray-700">Reporte de Obligaciones Municipales</p>
                        <p class="text-sm text-gray-600">Ayuntamiento: <span class="font-bold text-[#6c143a]">{{ $this->datosReporte['ente']->nombre }}</span></p>
                        <p class="text-sm text-gray-600">Periodo: enero a diciembre {{ $this->datosReporte['axo'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Leyenda --}}
            <div class="mb-4 flex flex-wrap gap-4 text-xs">
                <div class="flex items-center gap-1">
                    <span class="inline-block w-6 h-5 bg-white border border-gray-300 text-center leading-5 font-bold text-green-700 text-xs">P</span>
                    <span class="text-gray-600">= Presentado (≥80% aprobado)</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="inline-block w-6 h-5 bg-white border border-gray-300 text-center leading-5 font-bold text-red-600 text-xs">NP</span>
                    <span class="text-gray-600">= No Presentado</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="inline-block w-6 h-5 bg-gray-200 border border-gray-300"></span>
                    <span class="text-gray-600">= No aplica en este periodo</span>
                </div>
            </div>

            {{-- Tablas por categoría --}}
            @foreach ($this->datosReporte['categorias'] as $categoria)
                <div class="mb-8">
                    {{-- Título de categoría --}}
                    <div class="bg-[#2e7d32] text-white text-center py-2 px-4 text-sm font-bold rounded-t-lg">
                        {{ $categoria['nombre'] }}
                    </div>

                    @foreach ($categoria['subcategorias'] as $subcategoria)
                        <div class="mb-4 border border-gray-300 rounded-b-lg overflow-hidden">
                            {{-- Título de subcategoría --}}
                            <div class="bg-[#4caf50] text-white text-center py-1.5 px-4 text-xs font-semibold">
                                {{ $subcategoria['nombre'] }}
                            </div>

                            <table class="w-full text-xs border-collapse">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border border-gray-300 px-2 py-1 text-left w-8">#</th>
                                        <th class="border border-gray-300 px-2 py-1 text-left" style="min-width: 200px;">Documento</th>

                                        @if ($subcategoria['tipo_periodo'] === 'trimestral')
                                            <th class="border border-gray-300 px-1 py-1 text-center w-12">1er. Trim.</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-12">2do. Trim.</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-12">3er. Trim.</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-12">4to. Trim.</th>
                                        @else
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">ene</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">feb</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">mar</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">abr</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">may</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">jun</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">jul</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">ago</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">sep</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">oct</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">nov</th>
                                            <th class="border border-gray-300 px-1 py-1 text-center w-8">dic</th>
                                        @endif

                                        <th class="border border-gray-300 px-2 py-1 text-left" style="min-width: 200px;">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subcategoria['documentos'] as $index => $documento)
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-2 py-1 text-center">{{ $index + 1 }}</td>
                                            <td class="border border-gray-300 px-2 py-1">{{ $documento['nombre'] }}</td>

                                            @foreach ($documento['meses'] as $mes => $estadoData)
                                                <td class="border border-gray-300 px-1 py-1 text-center font-bold
                                                    @if ($estadoData['clase'] === 'no-aplica') bg-gray-200
                                                    @elseif ($estadoData['clase'] === 'presentado') bg-white
                                                    @elseif ($estadoData['clase'] === 'no-presentado') bg-white
                                                    @endif
                                                ">
                                                    @if ($estadoData['estado'] === 'P')
                                                        <span class="text-green-700">P</span>
                                                    @elseif ($estadoData['estado'] === 'NP')
                                                        <span class="text-red-600">NP</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            <td class="border border-gray-300 px-2 py-1 text-xs text-gray-700">
                                                @if (!empty($documento['observaciones']))
                                                    <div class="space-y-1">
                                                        @foreach ($documento['observaciones'] as $obs)
                                                            <div class="text-red-700">
                                                                {{ $obs['texto'] }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endforeach

            {{-- Observación General --}}
            <div class="mt-6 p-4 bg-gray-50 border border-gray-300 rounded-lg">
                <p class="font-bold text-sm text-gray-800">OBSERVACIÓN GENERAL:</p>
                <p class="text-xs text-gray-600 mt-1">
                    Reporte generado automáticamente el {{ now()->format('d/m/Y H:i') }} hrs.
                    Criterio: Un documento se considera "Presentado" (P) si al menos el 80% de los archivos asociados cuentan con estado "Aprobado".
                </p>
            </div>
        </div>

    @elseif ($enteSeleccionado && $axoSeleccionado)
        <div wire:loading.remove wire:target="enteSeleccionado, axoSeleccionado" class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Sin datos</h3>
            <p class="text-sm text-gray-500">No se encontraron datos para el ente y año seleccionados.</p>
        </div>

    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Generar Reporte de Obligaciones</h3>
            <p class="text-sm text-gray-500">Seleccione un ente y un año para generar el reporte.</p>
        </div>
    @endif
</div>
