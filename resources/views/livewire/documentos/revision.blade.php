<div>
    <div class="w-1/3 mx-auto mb-6">
        {{-- Select de Periodos --}}
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
            required wire:model.live="periodosSeleccionados">
            <option value="" class="text-gray-500">📅 Seleccione un periodo</option>
            @foreach ($this->periodos as $periodo)
                <option value="{{ $periodo->id }}" class="py-1">
                    {{ ucfirst($periodo->descripcion) }}
                </option>
            @endforeach
        </select>

        {{-- Select de Entes (solo los asignados al revisor) --}}
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white disabled:bg-gray-100 disabled:border-gray-300 disabled:text-gray-500 transition-colors mt-3"
            required wire:model.live="enteSeleccionado" {{ !$periodosSeleccionados ? 'disabled' : '' }}>
            @if (!$periodosSeleccionados)
                <option value="" class="text-gray-400">⚠️ Primero seleccione un periodo</option>
            @elseif($this->entesAsignados->isEmpty())
                <option value="" class="text-amber-600">📭 No hay entes asignados</option>
            @else
                <option value="" class="text-gray-500">🏛️ Seleccione un ente</option>
                @foreach ($this->entesAsignados as $ente)
                    <option value="{{ $ente->id }}" class="py-1">{{ $ente->nombre }}</option>
                @endforeach
            @endif
        </select>

        {{-- Select de Categorías --}}
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white disabled:bg-gray-100 disabled:border-gray-300 disabled:text-gray-500 transition-colors mt-3"
            required wire:model.live="categoriaSeleccionada" {{ !$enteSeleccionado ? 'disabled' : '' }}>
            @if (!$enteSeleccionado)
                <option value="" class="text-gray-400">⚠️ Primero seleccione un ente</option>
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
        @if ($periodosSeleccionados && $enteSeleccionado && $categoriaSeleccionada && $subcategoriaSeleccionada)
            <div
                class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md text-xs text-blue-700 flex items-center flex-wrap">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium mr-1">Revisando:</span>
                <span class="font-semibold bg-blue-100 px-2 py-0.5 rounded-full">
                    {{ $this->periodos->firstWhere('id', $periodosSeleccionados)?->descripcion }}
                </span>
                <span class="mx-1">→</span>
                <span class="font-semibold bg-blue-100 px-2 py-0.5 rounded-full">
                    {{ $this->entesAsignados->firstWhere('id', $enteSeleccionado)?->nombre }}
                </span>
                <span class="mx-1">→</span>
                <span class="font-semibold bg-blue-100 px-2 py-0.5 rounded-full">
                    {{ $this->categorias->firstWhere('id', $categoriaSeleccionada)?->nombre }}
                </span>
                <span class="mx-1">→</span>
                <span class="font-semibold bg-blue-100 px-2 py-0.5 rounded-full">
                    {{ $this->subcategorias->firstWhere('id', $subcategoriaSeleccionada)?->nombre }}
                </span>
            </div>
        @endif
    </div>

    {{-- Contenido principal de dos columnas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- Columna izquierda: Documentos a revisar --}}
        <div>
            @if ($this->documentosRecibidos->isNotEmpty())
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Documentos a revisar
                </h3>

                <div class="space-y-4 max-h-[calc(100vh-300px)] overflow-y-auto pr-2">
                    @foreach ($this->documentosRecibidos as $documentoRecibido)
                        @php
                            $documento = $documentoRecibido->documento;
                            $archivos = $documentoRecibido->archivos;
                            $clave = $documentoRecibido->clave;
                        @endphp

                        @foreach ($archivos as $archivo)
                            <div
                                class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 {{ $archivo->causas_rechazo_id ? 'border-l-4 border-l-red-500' : ($archivo->usuario_revisor ? 'border-l-4 border-l-green-500' : '') }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            @if ($archivo->causas_rechazo_id)
                                                <span
                                                    class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Rechazado</span>
                                            @elseif($archivo->usuario_revisor)
                                                <span
                                                    class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded">Aprobado</span>
                                            @else
                                                <span
                                                    class="text-xs bg-yellow-100 text-yellow-600 px-2 py-1 rounded">Pendiente</span>
                                            @endif
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-1">{{ $documento->nombre }}</h4>
                                        <p class="text-xs text-gray-500">
                                            {{ $archivo->nombre }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">Subido:</span>
                                            {{ $archivo->created_at->format('d/m/Y H:i') }}
                                        </p>
                                        @if ($archivo->observaciones_ente)
                                            <p class="text-xs text-gray-600 mt-1 italic">
                                                "{{ $archivo->observaciones_ente }}"
                                            </p>
                                        @endif
                                        @if ($archivo->causas_rechazo_id)
                                            <p class="text-xs text-red-600 mt-1">
                                                Causa: {{ $archivo->causaRechazo?->descripcion }}
                                            </p>
                                            @if ($archivo->observaciones_revisor)
                                                <p class="text-xs text-gray-600 mt-1">
                                                    Obs: {{ $archivo->observaciones_revisor }}
                                                </p>
                                            @endif
                                        @endif
                                    </div>

                                    <div class="flex flex-col space-y-2 ml-4">
                                        <button type="button" wire:click="verArchivo({{ $archivo->id }})"
                                            class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md transition-colors flex items-center justify-center whitespace-nowrap">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver
                                        </button>

                                        @if (!$archivo->usuario_revisor && !$archivo->causas_rechazo_id)
                                            <button type="button" wire:click="aprobarArchivo({{ $archivo->id }})"
                                                class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md transition-colors flex items-center justify-center whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                </svg>
                                                Aprobar
                                            </button>

                                            <button type="button"
                                                wire:click="mostrarPanelRechazo({{ $archivo->id }})"
                                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition-colors flex items-center justify-center whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                                                </svg>
                                                Rechazar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @elseif($subcategoriaSeleccionada && $this->documentosRecibidos->isEmpty())
                <div class="p-8 bg-gray-50 border border-gray-200 rounded-lg text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 text-lg mb-2">No hay documentos para revisar</p>
                    <p class="text-gray-400 text-sm">El ente no ha subido archivos para esta subcategoría.</p>
                </div>
            @endif
        </div>

        {{-- Columna derecha: Visor PDF --}}
        @if ($categoriaSeleccionada && $subcategoriaSeleccionada && $enteSeleccionado && $periodosSeleccionados)
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Visor de archivos
                </h3>

                <div class="bg-gray-100 rounded-lg p-4 h-[calc(100vh-300px)] overflow-auto">
                    @if ($archivoEnRevision)
                        @if (pathinfo($archivoEnRevision->nombre, PATHINFO_EXTENSION) === 'pdf')
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded mr-2">
                                {{ $documento->clave ?? 'S/C' }}
                            </span>
                            <span>
                                {{ asset(
                                    'storage/documentos/' .
                                        $archivoEnRevision->documentoRecibido->periodo->axo .
                                        '/' .
                                        $archivoEnRevision->ente->nombre .
                                        '/' .
                                        $archivoEnRevision->documentoRecibido->periodo->mes_nombre .
                                        '/' .
                                        $archivoEnRevision->nombre,
                                ) }}
                            </span>
                            <iframe
                                src="{{ asset(
                                    'storage/documentos/' .
                                        $archivoEnRevision->documentoRecibido->periodo->axo .
                                        '/' .
                                        $archivoEnRevision->ente->nombre .
                                        '/' .
                                        $archivoEnRevision->documentoRecibido->periodo->mes_nombre .
                                        '/' .
                                        $archivoEnRevision->nombre,
                                ) }}#toolbar=0&navpanes=0"
                                class="w-full h-full rounded-lg" frameborder="0">
                            </iframe>
                        @else
                            <div class="flex flex-col items-center justify-center h-full">
                                <svg class="w-24 h-24 text-green-600 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-700 text-lg mb-2">Archivo Excel</p>
                                <a href="{{ $archivoEnRevision->url }}" target="_blank"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                    Descargar para visualizar
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-400">
                            <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <p class="text-lg">Selecciona un archivo para visualizar</p>
                            <p class="text-sm">Haz clic en "Ver" en cualquier documento</p>
                        </div>
                    @endif
                </div>
            </div>
            {{-- @else

            <div class="bg-gray-50 rounded-lg p-8 text-center text-gray-400">
                <svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-lg">Complete los filtros para ver el visor</p>
                <p class="text-sm mt-2">Seleccione periodo, ente, categoría y subcategoría</p>
            </div> --}}
        @endif
    </div>

    {{-- Panel de rechazo (modal) --}}
    @if ($mostrarPanelRechazo)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false; $wire.cancelarRechazo()">
                </div>

                <div class="relative bg-white rounded-lg max-w-md w-full p-6"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Rechazar archivo
                        </h3>
                        <button @click="show = false; $wire.cancelarRechazo()"
                            class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">
                            Archivo: <span class="font-semibold">{{ $archivoSeleccionado?->nombre }}</span>
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Causa de rechazo *
                            </label>
                            <select wire:model="causaRechazoId"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-vino-900 focus:ring-vino-900">
                                <option value="">Seleccione una causa</option>
                                @foreach ($causasRechazo as $causa)
                                    <option value="{{ $causa->id }}">{{ $causa->descripcion }}</option>
                                @endforeach
                            </select>
                            @error('causaRechazoId')
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Observaciones (opcional)
                            </label>
                            <textarea wire:model="observacionesRevisor" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-vino-900 focus:ring-vino-900 text-sm"
                                placeholder="Comentarios adicionales sobre el rechazo..."></textarea>
                            @error('observacionesRevisor')
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="show = false; $wire.cancelarRechazo()"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors text-sm">
                            Cancelar
                        </button>
                        <button type="button" wire:click="rechazarArchivo"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors text-sm">
                            Confirmar rechazo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Script para notificaciones y eventos --}}
    @script
        <script>
            $wire.on('notificacion', (data) => {
                const [mensaje, tipo] = data;
                mostrarNotificacion(mensaje, tipo);
            });

            $wire.on('actualizar-visorpdf', (data) => {
                // El visor se actualiza automáticamente con el iframe
                console.log('Actualizando visor PDF');
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
