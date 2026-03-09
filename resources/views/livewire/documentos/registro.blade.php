{{-- <div class="flex flex-col space-y-3 px-6 max-w-4xl mx-auto "> --}}
<div>
    <div class="w-1/3 mx-auto mb-6">
        {{-- Select de Periodos --}}
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
            required wire:model.live="periodosSeleccionados">
            <option value="" class="text-gray-500">📅 Seleccione un periodo</option>
            @foreach ($this->periodos as $periodo)
                <option value="{{ $periodo->id }}" class="py-1" {{ $periodo->is_active ? '' : 'disabled' }}>
                    {{ ucfirst($periodo->nombre) }}</option>
            @endforeach
        </select>


        {{-- Select de Categorías --}}
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white mt-3"
            required wire:model.live="categoriaSeleccionada">
            <option value="" class="text-gray-500">📁 Seleccione una categoría</option>
            @foreach ($this->categorias as $categoria)
                <option value="{{ $categoria->id }}" class="py-1">{{ $categoria->nombre }}</option>
            @endforeach
        </select>

        {{-- Select de Subcategorías --}}
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white disabled:bg-gray-100 disabled:border-gray-300 disabled:text-gray-500 transition-colors mt-3"
            name="subcategoria" wire:model.live="subcategoriaSeleccionada"
            {{ !$categoriaSeleccionada ? 'disabled' : '' }}>

            @if (!$categoriaSeleccionada)
                <option value="" class="text-gray-400">⚠️ Primero seleccione una categoría</option>
            @elseif($this->subcategorias->isEmpty())
                <option value="" class="text-amber-600">📭 No hay subcategorías disponibles</option>
            @else
                <option value="" class="text-gray-500">📄 Seleccione una subcategoría</option>
                @foreach ($this->subcategorias as $subcategoria)
                    <option value="{{ $subcategoria->id }}" class="py-1">{{ $subcategoria->nombre }}</option>
                @endforeach
            @endif
        </select>

        {{-- Indicador de selección actual --}}
        @if ($periodosSeleccionados && $categoriaSeleccionada && $subcategoriaSeleccionada)
            <div
                class="mt-2 p-2 bg-green-50 border border-green-200 rounded-md text-xs text-green-700 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Selección:
                <span class="font-semibold ml-1">
                    {{ $this->periodos->firstWhere('id', $periodosSeleccionados)?->nombre }}
                </span>
                <span class="mx-2">→</span>
                <span class="font-semibold ml-1">
                    {{ $this->categorias->firstWhere('id', $categoriaSeleccionada)?->nombre }}
                </span>
                <span class="mx-2">→</span>
                <span class="font-semibold">
                    {{ $this->subcategorias->firstWhere('id', $subcategoriaSeleccionada)?->nombre }}
                </span>
            </div>
        @endif
    </div>

    {{-- Documentos relacionados --}}
    @if ($this->documentos->isNotEmpty())
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-vino-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Documentos requeridos
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($this->documentos as $documento)
                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                {{-- Clave y nombre --}}
                                <div class="flex items-center mb-2">
                                    <span class="text-xs font-bold text-vino-900 bg-vino-50 px-2 py-1 rounded mr-2">
                                        {{ $documento->clave }}
                                    </span>
                                </div>

                                <h4 class="font-semibold text-gray-900 mb-1">{{ $documento->nombre }}</h4>

                            </div>

                            <div class="flex flex-col space-y-2 mt-4 min-w-[140px]">
                                @php
                                    $formatos = explode(',', $documento->formato);
                                    $formatos = array_map('trim', $formatos); // Limpiar espacios
                                @endphp

                                {{-- Botón PDF --}}
                                @if (in_array('PDF', $formatos))
                                    <button type="button"
                                        class="w-full px-3 py-2 bg-vino-900 hover:bg-vino-800 text-white text-sm rounded-md transition-colors flex items-center justify-center whitespace-nowrap">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 3v4a1 1 0 001 1h4" />
                                            <text x="10" y="18" font-size="8" font-weight="bold" fill="currentColor"
                                                stroke="none">PDF</text>
                                        </svg>
                                        <span>Subir PDF</span>
                                    </button>
                                @endif

                                {{-- Botón Excel --}}
                                @if (in_array('XLSX', $formatos) || in_array('XLS', $formatos))
                                    <button type="button"
                                        class="w-full px-3 py-2 text-white text-sm rounded-md transition-colors flex items-center justify-center whitespace-nowrap"
                                        style="background-color: #1D6F42;">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 3v4a1 1 0 001 1h4" />
                                            <text x="6" y="18" font-size="6" font-weight="bold" fill="currentColor"
                                                stroke="none">XLSX</text>
                                        </svg>
                                        <span>Subir Excel</span>
                                    </button>
                                @endif

                                {{-- Mensaje si no hay formatos reconocidos --}}
                                @if (empty($formatos) || (count($formatos) === 1 && empty($formatos[0])))
                                    <span class="text-xs text-gray-400 italic">Sin formatos especificados</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($subcategoriaSeleccionada && $this->documentos->isEmpty())
        <div class="mt-6 p-8 bg-gray-50 border border-gray-200 rounded-lg text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-500 text-lg mb-2">No hay documentos disponibles</p>
            <p class="text-gray-400 text-sm">Esta subcategoría no tiene documentos asociados actualmente.</p>
        </div>
    @endif
</div>
{{-- </div> --}}
