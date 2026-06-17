<div>
    {{-- ===== SELECTORES DE ENTE Y AÑO ===== --}}
    <div class="flex flex-wrap gap-4 mb-4 items-end">
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
    </div>

    {{-- ===== BARRA DE ACCIONES (Filtros + Exportar) — aparece cuando hay datos ===== --}}
    @if ($this->datosReporte !== null)
        <div class="flex flex-wrap items-center gap-3 mb-4">

            {{-- Botón toggle de filtros --}}
            <button wire:click="toggleFiltros"
                class="relative inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border transition-all duration-200
                    {{ $mostrarFiltros
                        ? 'bg-[#6c143a] text-white border-[#6c143a] shadow-md'
                        : 'bg-white text-gray-700 border-gray-300 hover:border-[#6c143a] hover:text-[#6c143a] shadow-sm' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                {{ $mostrarFiltros ? 'Cerrar filtros' : 'Filtros' }}

                @if ($tieneFiltrosActivos)
                    <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center w-5 h-5 text-xs font-bold bg-amber-400 text-amber-900 rounded-full shadow">
                        !
                    </span>
                @endif
            </button>

            {{-- Separador --}}
            <div class="h-6 w-px bg-gray-300"></div>

            {{-- Botón PDF --}}
            <a href="{{ $this->urlPdf }}" target="_blank"
                class="inline-flex items-center px-4 py-2 bg-red-700 text-white text-sm font-medium rounded-lg hover:bg-red-800 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar PDF
            </a>

            {{-- Botón Excel --}}
            <a href="{{ $this->urlExcel }}"
                class="inline-flex items-center px-4 py-2 bg-green-700 text-white text-sm font-medium rounded-lg hover:bg-green-800 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar Excel
            </a>

            {{-- Etiqueta de filtros activos --}}
            @if ($tieneFiltrosActivos)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Filtros activos
                </span>
                <button wire:click="limpiarTodosFiltros"
                    class="text-xs text-gray-500 hover:text-red-600 underline transition-colors">
                    Restaurar todo
                </button>
            @endif
        </div>

        {{-- ===== PANEL DE FILTROS ===== --}}
        @if ($mostrarFiltros)
            <div class="mb-5 bg-gray-50 border border-gray-200 rounded-xl shadow-inner overflow-hidden"
                wire:loading.class="opacity-50 pointer-events-none"
                wire:target="toggleCategoria,toggleSubcategoria,toggleDocumento,seleccionarTodasCategorias,limpiarCategorias,seleccionarTodasSubcategorias,limpiarSubcategorias,seleccionarTodosDocumentos,limpiarDocumentos">

                <div class="flex items-center justify-between px-5 py-3 bg-white border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#6c143a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filtrar contenido del reporte
                    </h3>
                    <p class="text-xs text-gray-400">La selección se aplica en cascada: Categoría → Subcategoría → Documento</p>
                 <style>
                    .custom-scroll::-webkit-scrollbar {
                        width: 6px;
                        height: 6px;
                    }
                    .custom-scroll::-webkit-scrollbar-track {
                        background: transparent;
                    }
                    .custom-scroll::-webkit-scrollbar-thumb {
                        background-color: rgba(156, 163, 175, 0.4);
                        border-radius: 4px;
                    }
                    .custom-scroll::-webkit-scrollbar-thumb:hover {
                        background-color: rgba(156, 163, 175, 0.7);
                    }
                </style>

                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200">

                    {{-- ──────────── COLUMNA 1: CATEGORÍAS ──────────── --}}
                    <div x-data="{ search: '' }" class="p-4 flex flex-col gap-2">
                        {{-- Encabezado --}}
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-[#6c143a] uppercase tracking-wide flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                Categorías
                            </span>
                            <span class="text-xs text-gray-400 font-semibold">
                                {{ count($categoriasSeleccionadas) }}/{{ $this->totalCategorias }}
                            </span>
                        </div>

                        {{-- Botones rápidos --}}
                        <div class="flex gap-1.5 mb-1">
                            <button wire:click="seleccionarTodasCategorias"
                                class="flex-1 py-1 text-xs rounded border border-gray-300 hover:bg-[#6c143a] hover:text-white hover:border-[#6c143a] transition-colors text-gray-600 font-medium">
                                ✓ Todas
                            </button>
                            <button wire:click="limpiarCategorias"
                                class="flex-1 py-1 text-xs rounded border border-gray-300 hover:bg-red-50 hover:text-red-600 hover:border-red-300 transition-colors text-gray-600 font-medium">
                                ✕ Ninguna
                            </button>
                        </div>

                        {{-- Buscador --}}
                        <div class="relative">
                            <input x-model="search" type="text" placeholder="Buscar categoría..."
                                class="w-full pl-7 pr-3 py-1.5 text-xs border border-gray-200 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-[#6c143a] focus:border-[#6c143a]">
                            <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        {{-- Lista estilo Asignación de Entes --}}
                        <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto p-2 bg-gray-50 mt-1 custom-scroll space-y-2">
                            @foreach ($this->todasLasCategorias as $cat)
                                @php $isSelected = in_array($cat->id, $categoriasSeleccionadas); @endphp
                                <label
                                    data-nombre="{{ strtolower($cat->nombre) }}"
                                    x-show="search === '' || $el.dataset.nombre.includes(search.toLowerCase())"
                                    class="flex items-center p-3 rounded-md border transition-all duration-200 cursor-pointer shadow-sm
                                        {{ $isSelected 
                                            ? 'bg-[#6c143a]/5 border-[#6c143a]/30 hover:border-[#6c143a]/50' 
                                            : 'bg-white border-gray-200 hover:border-[#6c143a]' }}">
                                    <input type="checkbox"
                                        wire:click="toggleCategoria({{ $cat->id }})"
                                        @checked($isSelected)
                                        class="w-5 h-5 text-[#6c143a] border-gray-300 rounded focus:ring-[#6c143a] cursor-pointer transition duration-150 ease-in-out shrink-0">
                                    <div class="ml-3 flex-1 min-w-0">
                                        <span class="block text-[13px] font-medium text-gray-700 leading-normal">{{ $cat->nombre }}</span>
                                    </div>
                                    @if($isSelected)
                                        <span class="text-[10px] bg-[#6c143a]/10 text-[#6c143a] px-2 py-0.5 rounded-full font-bold ml-2 shrink-0">Actual</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- ──────────── COLUMNA 2: SUBCATEGORÍAS ──────────── --}}
                    <div x-data="{ search: '' }" class="p-4 flex flex-col gap-2">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-[#2e7d32] uppercase tracking-wide flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                Subcategorías
                            </span>
                            <span class="text-xs text-gray-400 font-semibold">
                                {{ count($subcategoriasSeleccionadas) }}/{{ $this->totalSubcategorias }}
                            </span>
                        </div>

                        <div class="flex gap-1.5 mb-1">
                            <button wire:click="seleccionarTodasSubcategorias"
                                class="flex-1 py-1 text-xs rounded border border-gray-300 hover:bg-[#2e7d32] hover:text-white hover:border-[#2e7d32] transition-colors text-gray-600 font-medium">
                                ✓ Todas
                            </button>
                            <button wire:click="limpiarSubcategorias"
                                class="flex-1 py-1 text-xs rounded border border-gray-300 hover:bg-red-50 hover:text-red-600 hover:border-red-300 transition-colors text-gray-600 font-medium">
                                ✕ Ninguna
                            </button>
                        </div>

                        <div class="relative">
                            <input x-model="search" type="text" placeholder="Buscar subcategoría..."
                                class="w-full pl-7 pr-3 py-1.5 text-xs border border-gray-200 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-[#2e7d32] focus:border-[#2e7d32]">
                            <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        {{-- Lista estilo Asignación de Entes --}}
                        <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto p-2 bg-gray-50 mt-1 custom-scroll space-y-2">
                            @forelse ($this->subcategoriasDisponibles as $sub)
                                @php $isSelected = in_array($sub->id, $subcategoriasSeleccionadas); @endphp
                                <label
                                    data-nombre="{{ strtolower($sub->nombre) }}"
                                    x-show="search === '' || $el.dataset.nombre.includes(search.toLowerCase())"
                                    class="flex items-center p-3 rounded-md border transition-all duration-200 cursor-pointer shadow-sm
                                        {{ $isSelected 
                                            ? 'bg-green-50 border-green-300 hover:border-green-400' 
                                            : 'bg-white border-gray-200 hover:border-[#2e7d32]' }}">
                                    <input type="checkbox"
                                        wire:click="toggleSubcategoria({{ $sub->id }})"
                                        @checked($isSelected)
                                        class="w-5 h-5 text-[#2e7d32] border-gray-300 rounded focus:ring-[#2e7d32] cursor-pointer transition duration-150 ease-in-out shrink-0">
                                    <div class="ml-3 flex-1 min-w-0">
                                        <span class="block text-[13px] font-medium text-gray-700 leading-normal">{{ $sub->nombre }}</span>
                                    </div>
                                    @if($isSelected)
                                        <span class="text-[10px] bg-green-100 text-green-800 px-2 py-0.5 rounded-full font-bold ml-2 shrink-0">Actual</span>
                                    @endif
                                </label>
                            @empty
                                <div class="text-center py-12 text-gray-400 bg-white border border-gray-200 rounded-md">
                                    <p class="text-sm italic">Sin subcategorías disponibles</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- ──────────── COLUMNA 3: DOCUMENTOS ──────────── --}}
                    <div x-data="{ search: '' }" class="p-4 flex flex-col gap-2">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-blue-700 uppercase tracking-wide flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Documentos
                            </span>
                            <span class="text-xs text-gray-400 font-semibold">
                                {{ count($documentosSeleccionados) }}/{{ $this->totalDocumentos }}
                            </span>
                        </div>

                        <div class="flex gap-1.5 mb-1">
                            <button wire:click="seleccionarTodosDocumentos"
                                class="flex-1 py-1 text-xs rounded border border-gray-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-colors text-gray-600 font-medium">
                                ✓ Todos
                            </button>
                            <button wire:click="limpiarDocumentos"
                                class="flex-1 py-1 text-xs rounded border border-gray-300 hover:bg-red-50 hover:text-red-600 hover:border-red-300 transition-colors text-gray-600 font-medium">
                                ✕ Ninguno
                            </button>
                        </div>

                        <div class="relative">
                            <input x-model="search" type="text" placeholder="Buscar documento..."
                                class="w-full pl-7 pr-3 py-1.5 text-xs border border-gray-200 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        {{-- Lista estilo Asignación de Entes --}}
                        <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto p-2 bg-gray-50 mt-1 custom-scroll space-y-2">
                            @forelse ($this->documentosDisponibles as $doc)
                                @php $isSelected = in_array($doc->id, $documentosSeleccionados); @endphp
                                <label
                                    data-nombre="{{ strtolower($doc->nombre) }}"
                                    x-show="search === '' || $el.dataset.nombre.includes(search.toLowerCase())"
                                    class="flex items-center p-3 rounded-md border transition-all duration-200 cursor-pointer shadow-sm
                                        {{ $isSelected 
                                            ? 'bg-blue-50 border-blue-300 hover:border-blue-400' 
                                            : 'bg-white border-gray-200 hover:border-blue-500' }}">
                                    <input type="checkbox"
                                        wire:click="toggleDocumento({{ $doc->id }})"
                                        @checked($isSelected)
                                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer transition duration-150 ease-in-out shrink-0">
                                    <div class="ml-3 flex-1 min-w-0">
                                        <span class="block text-[13px] font-medium text-gray-700 leading-normal">{{ $doc->nombre }}</span>
                                    </div>
                                    @if($isSelected)
                                        <span class="text-[10px] bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-bold ml-2 shrink-0">Actual</span>
                                    @endif
                                </label>
                            @empty
                                <div class="text-center py-12 text-gray-400 bg-white border border-gray-200 rounded-md">
                                    <p class="text-sm italic">Sin documentos disponibles</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                {{-- Pie del panel --}}
                <div class="px-5 py-3 bg-white border-t border-gray-200 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        El reporte mostrará: <strong>{{ count($categoriasSeleccionadas) }}</strong> categoría(s),
                        <strong>{{ count($subcategoriasSeleccionadas) }}</strong> subcategoría(s),
                        <strong>{{ count($documentosSeleccionados) }}</strong> documento(s)
                    </p>
                    <div class="flex gap-2">
                        @if ($tieneFiltrosActivos)
                            <button wire:click="limpiarTodosFiltros"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-300 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-300 transition-colors">
                                Restaurar todo
                            </button>
                        @endif
                        <button wire:click="toggleFiltros"
                            class="px-4 py-1.5 text-xs rounded-lg bg-[#6c143a] text-white hover:bg-[#551030] transition-colors font-medium">
                            ✓ Aplicar y cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

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

    {{-- ===== REPORTE ===== --}}
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
                        @if ($tieneFiltrosActivos)
                            <p class="text-xs text-amber-600 mt-1 font-medium">⚠ Reporte filtrado: mostrando selección parcial de documentos</p>
                        @endif
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
            @forelse ($this->datosReporte['categorias'] as $categoria)
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
                                                            <div class="text-red-700">{{ $obs['texto'] }}</div>
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
            @empty
                <div class="text-center py-12 bg-amber-50 border border-amber-200 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-amber-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-amber-800 mb-1">Sin resultados con los filtros actuales</h3>
                    <p class="text-xs text-amber-600">Ningún documento coincide con la selección actual.</p>
                    <button wire:click="limpiarTodosFiltros"
                        class="mt-3 px-4 py-1.5 text-xs bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        Restaurar filtros
                    </button>
                </div>
            @endforelse

            {{-- Observación General --}}
            @if (!empty($this->datosReporte['categorias']))
                <div class="mt-6 p-4 bg-gray-50 border border-gray-300 rounded-lg">
                    <p class="font-bold text-sm text-gray-800">OBSERVACIÓN GENERAL:</p>
                    <p class="text-xs text-gray-600 mt-1">
                        Reporte generado automáticamente el {{ now()->format('d/m/Y H:i') }} hrs.
                        Criterio: Un documento se considera "Presentado" (P) si al menos el 80% de los archivos asociados cuentan con estado "Aprobado".
                    </p>
                </div>
            @endif
        </div>

    @elseif ($enteSeleccionado && $axoSeleccionado)
        <div wire:loading.remove wire:target="enteSeleccionado, axoSeleccionado" class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
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
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Generar Reporte de Obligaciones</h3>
            <p class="text-sm text-gray-500">Seleccione un ente y un año para generar el reporte.</p>
        </div>
    @endif
</div>
