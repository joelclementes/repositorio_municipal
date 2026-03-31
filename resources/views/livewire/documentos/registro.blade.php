<div>
    <div class="w-1/3 mx-auto mb-6">
        {{-- Select de Periodos --}}
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

        {{-- Select de Categorías --}}
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white disabled:bg-gray-100 disabled:border-gray-300 disabled:text-gray-500 transition-colors mt-3"
            required wire:model.live="categoriaSeleccionada" {{ !$periodosSeleccionados ? 'disabled' : '' }}>
            @if (!$periodosSeleccionados)
                <option value="" class="text-gray-400">⚠️ Primero seleccione un periodo</option>
            @elseif($this->categorias->isEmpty())
                <option value="" class="text-amber-600">📭 No hay categorías disponibles</option>
            @else
                <option value="" class="text-gray-500">📁 Seleccione una categoría</option>
                @foreach ($this->categorias as $categoria)
                    <option value="{{ $categoria->id }}" class="py-1">{{ $categoria->nombre }}</option>
                @endforeach
            @endif
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
                class="mt-2 p-2 bg-green-50 border border-green-200 rounded-md text-xs text-green-700 flex items-center flex-wrap">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium mr-1">Selección:</span>
                <span class="font-semibold bg-green-100 px-2 py-0.5 rounded-full">
                    {{ $this->periodos->firstWhere('id', $periodosSeleccionados)?->descripcion }}
                </span>
                <span class="mx-1">→</span>
                <span class="font-semibold bg-green-100 px-2 py-0.5 rounded-full">
                    {{ $this->categorias->firstWhere('id', $categoriaSeleccionada)?->nombre }}
                </span>
                <span class="mx-1">→</span>
                <span class="font-semibold bg-green-100 px-2 py-0.5 rounded-full">
                    {{ $this->subcategorias->firstWhere('id', $subcategoriaSeleccionada)?->nombre }}
                </span>
            </div>
        @endif
    </div>

    {{-- Documentos relacionados --}}
    @if ($this->documentosRecibidos->isNotEmpty())
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-vino-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Documentos requeridos
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($this->documentosRecibidos as $documentoRecibido)
                    @php
                        $documento = $documentoRecibido->documento;
                        $formatos = explode(',', $documento->formato);
                        $formatos = array_map('trim', $formatos);
                        $archivos = $documentoRecibido->archivos;
                        $tieneArchivoPDF = $archivos->where('tipo_recepcion', 'PDF')->count() > 0;
                        $tieneArchivoExcel = $archivos->whereIn('tipo_recepcion', ['XLSX', 'XLS'])->count() > 0;
                    @endphp

                    <div
                        class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <span class="text-xs font-bold text-vino-900 bg-vino-50 px-2 py-1 rounded mr-2">
                                        {{ $documento->clave }}
                                    </span>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $documento->nombre }}</h4>
                                <p class="text-xs text-gray-500">Límite: del {{ $documento->fecha_inicio }} al
                                    {{ $documento->fecha_limite }}</p>

                                {{-- Mostrar archivos subidos --}}
                                @if ($archivos->count() > 0)
                                    <div class="mt-2 space-y-1">
                                        @php
                                            $autorizadoReenviarPDF = 0;
                                            $autorizadoReenviarXLSX = 0;
                                        @endphp
                                        @foreach ($archivos as $archivo)
                                            @php
                                                if ($archivo->tipo_recepcion === 'PDF') {
                                                    if ($archivo->autorizado_reenviar) {
                                                        $autorizadoReenviarPDF = 1;
                                                    }
                                                } elseif ($archivo->tipo_recepcion === 'XLSX') {
                                                    if ($archivo->autorizado_reenviar) {
                                                        $autorizadoReenviarXLSX = 1;
                                                    }
                                                }
                                                // $strAutorizPDF = 'PDF - ' . $autorizadoReenviarPDF;
                                                // $strAutorizXLSX = 'XLSX - ' . $autorizadoReenviarXLSX;
                                            @endphp
                                            <div
                                                class="flex items-center text-xs {{ $archivo->causas_rechazo_id ? 'text-red-600' : 'text-green-600' }}">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    @if ($archivo->causas_rechazo_id)
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    @endif
                                                </svg>
                                                <span class="truncate max-w-[150px]">{{ $archivo->nombre }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col space-y-2 mt-4 min-w-[140px]">
                                @if (in_array('PDF', $formatos))
                                    <button type="button"
                                        wire:click="abrirModalSubida({{ $documentoRecibido->id }}, 'PDF')"
                                        class="w-full px-3 py-2 {{ $tieneArchivoPDF && !$autorizadoReenviarPDF ? 'bg-gray-400 cursor-not-allowed' : 'bg-vino-900 hover:bg-vino-800' }} text-white text-sm rounded-md transition-colors flex items-center justify-center whitespace-nowrap"
                                        {{ $tieneArchivoPDF && !$autorizadoReenviarPDF ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 3v4a1 1 0 001 1h4" />
                                            <text x="10" y="18" font-size="8" font-weight="bold" fill="currentColor"
                                                stroke="none">PDF</text>
                                        </svg>
                                        <span>{{ $tieneArchivoPDF && !$autorizadoReenviarPDF ? 'Ya subido' : 'Subir PDF' }}</span>
                                    </button>
                                @endif

                                @if (in_array('XLSX', $formatos) || in_array('XLS', $formatos))
                                    <button type="button"
                                        wire:click="abrirModalSubida({{ $documentoRecibido->id }}, '{{ in_array('XLSX', $formatos) ? 'XLSX' : 'XLS' }}')"
                                        class="w-full px-3 py-2 {{ $tieneArchivoExcel && !$autorizadoReenviarXLSX ? 'bg-gray-400 cursor-not-allowed' : '' }} text-white text-sm rounded-md transition-colors flex items-center justify-center whitespace-nowrap"
                                        style="{{ $tieneArchivoExcel && !$autorizadoReenviarXLSX ? 'background-color: #9CA3AF;' : 'background-color: #1D6F42;' }}"
                                        {{ $tieneArchivoExcel && !$autorizadoReenviarXLSX ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 3v4a1 1 0 001 1h4" />
                                            <text x="6" y="18" font-size="6" font-weight="bold" fill="currentColor"
                                                stroke="none">XLSX</text>
                                        </svg>
                                        <span>{{ $tieneArchivoExcel && !$autorizadoReenviarXLSX ? 'Ya subido' : 'Subir Excel' }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($subcategoriaSeleccionada && $this->documentosRecibidos->isEmpty())
        <div class="mt-6 p-8 bg-gray-50 border border-gray-200 rounded-lg text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-500 text-lg mb-2">No hay documentos disponibles</p>
            <p class="text-gray-400 text-sm">Esta subcategoría no tiene documentos asociados actualmente.</p>
        </div>
    @endif

    {{-- Modal de subida de archivos --}}
    @if ($mostrarModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('mostrarModal') }" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>

                <div class="relative bg-white rounded-lg max-w-md w-full p-6"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Subir archivo {{ $tipoSubida }}
                        </h3>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">
                            Clave: <span class="font-semibold">{{ $documentoSeleccionado?->clave }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mb-2">
                            Documento: <span class="font-semibold">{{ $documentoSeleccionado?->nombre }}</span>
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Archivo {{ $tipoSubida }} *
                            </label>
                            <input type="file" wire:model="archivo" {{-- Esto fuerza que el input se reinicie --}}
                                accept="{{ $tipoSubida === 'PDF' ? '.pdf' : '.xlsx,.xls,.csv' }}"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-vino-50 file:text-vino-900 hover:file:bg-vino-100">
                            {{-- <input type="file" wire:model="archivo"
                                accept="{{ $tipoSubida === 'PDF' ? '.pdf' : '.xlsx,.xls,.csv' }}"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-vino-50 file:text-vino-900 hover:file:bg-vino-100"> --}}
                            @error('archivo')
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Descripción (opcional)
                            </label>
                            <textarea wire:model="descripcion" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-vino-900 focus:ring-vino-900 text-sm"
                                placeholder="Agrega una descripción o comentarios sobre el archivo..."></textarea>
                            @error('descripcion')
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="show = false"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors text-sm">
                            Cancelar
                        </button>
                        <button type="button" wire:click="guardarArchivo" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-emerald-900 hover:bg-emerald-700 text-white rounded-md transition-colors text-sm flex items-center">
                            <span wire:loading.remove wire:target="guardarArchivo">
                                Subir archivo
                            </span>
                            <span wire:loading wire:target="guardarArchivo" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Subiendo...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Script para notificaciones --}}
    @script
        <script>
            $wire.on('notificacion', (data) => {
                const [mensaje, tipo] = data;
                mostrarNotificacion(mensaje, tipo);
            });

            $wire.on('archivo-subido', (data) => {
                const [mensaje, tipo] = data;
                mostrarNotificacion(mensaje, tipo);
            });

            function mostrarNotificacion(mensaje, tipo = 'success') {
                const colores = {
                    success: 'bg-green-700',
                    error: 'bg-red-500',
                    warning: 'bg-yellow-500',
                    info: 'bg-blue-500'
                };

                const notificacion = document.createElement('div');
                notificacion.className =
                    `fixed top-4 right-4 ${colores[tipo] || 'bg-gray-500'} text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in-down`;
                notificacion.textContent = mensaje;

                document.body.appendChild(notificacion);

                setTimeout(() => {
                    notificacion.remove();
                }, 3000);
            }
        </script>
    @endscript

    <style>
        @keyframes fade-in-down {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out;
        }
    </style>
</div>
