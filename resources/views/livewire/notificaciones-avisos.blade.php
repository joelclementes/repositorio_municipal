{{-- resources/views/livewire/notificaciones-avisos.blade.php --}}
<div class="relative" x-data="{
    open: @entangle('mostrarDropdown'),
    init() {
        this.$watch('open', (value) => {
            if (value && this.$refs.dropdownContent) {
                this.$refs.dropdownContent.addEventListener('scroll', () => {
                    const dropdown = this.$refs.dropdownContent;
                    if (dropdown.scrollTop + dropdown.clientHeight >= dropdown.scrollHeight - 100) {
                        if (!$wire.cargando && $wire.tieneMas) {
                            $wire.dispatch('cargarMasAvisos');
                        }
                    }
                });
            }
        });
    }
}">

    <!-- Botón de campana -->
    <button @click="open = !open"
        class="relative p-1 text-gray-600 hover:text-gray-900 focus:outline-none transition-transform hover:scale-110">
        <!-- Icono de campana -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            :class="{ 'text-[#6C143B]': open }">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <!-- Círculo de notificación color #6C143B -->
        @if ($cantidadPendientes > 0)
            <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white"
                style="background-color: #6C143B;">
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" @click.away="open = false"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
        style="display: none;">

        <!-- Cabecera -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Notificaciones</h3>
                @if ($cantidadPendientes > 0)
                    <button wire:click="marcarTodosLeidos" wire:loading.attr="disabled"
                        class="text-sm text-[#6C143B] hover:text-[#4a0e29] font-medium transition-colors">
                        Marcar todas como leídas
                    </button>
                @endif
            </div>
        </div>

        <!-- Lista de notificaciones -->
        <div x-ref="dropdownContent" class="max-h-[32rem] overflow-y-auto overscroll-contain">
            @forelse($this->avisosPendientes as $avisoEnte)
                <div wire:key="aviso-{{ $avisoEnte->id }}"
                    class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors cursor-pointer"
                    wire:click="verAviso({{ $avisoEnte->id }})" x-data="{ hover: false }" @mouseenter="hover = true"
                    @mouseleave="hover = false">

                    <div class="flex items-start space-x-3">
                        <!-- Icono de aviso -->
                        <div class="flex-shrink-0">
                            @php
                                $colores = [
                                    'Aviso' => 'bg-blue-100 text-blue-600',
                                    'Invitación' => 'bg-green-100 text-green-600',
                                    'Exhorto' => 'bg-yellow-100 text-yellow-600',
                                    'Convocatoria' => 'bg-purple-100 text-purple-600',
                                    'Circular' => 'bg-orange-100 text-orange-600',
                                ];
                                $color = $colores[$avisoEnte->aviso->tipo_aviso] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <div class="w-10 h-10 {{ $color }} rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <p class="text-sm font-medium text-gray-900 truncate max-w-[180px]">
                                    {{ $avisoEnte->aviso->titulo }}
                                </p>
                                <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                    {{ $avisoEnte->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                                {{ $avisoEnte->aviso->texto }}
                            </p>
                            <div class="flex items-center mt-2">
                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">
                                    {{ $avisoEnte->aviso->tipo_aviso }}
                                </span>
                                @if ($avisoEnte->aviso->archivo)
                                    <span class="text-xs text-gray-400 ml-2 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Adjunto
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Indicador de no leído -->
                        @if ($avisoEnte->estado_envio !== 'leido')
                            <div class="flex-shrink-0 ml-2">
                                <span class="block h-2 w-2 rounded-full" style="background-color: #6C143B;"></span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="mt-4 text-gray-600">No tienes notificaciones pendientes</p>
                    <p class="text-sm text-gray-400 mt-1">¡Disfruta de tu día!</p>
                </div>
            @endforelse

            <!-- Indicador de carga -->
            @if ($cargando)
                <div class="px-4 py-3 text-center">
                    <svg class="animate-spin h-5 w-5 mx-auto text-[#6C143B]" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Footer -->
        @if ($cantidadPendientes > 0)
            <div class="px-4 py-2 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <a href="{{ route('avisos.pendientes') }}"
                    class="block text-center text-sm text-[#6C143B] hover:text-[#4a0e29] font-medium">
                    Ver todas las notificaciones ({{ $cantidadPendientes }})
                </a>
            </div>
        @endif
    </div>

    <!-- Modal -->
    @if ($mostrarModal && $avisoSeleccionado)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('mostrarModal') }" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <!-- Overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="show = false"></div>

            <!-- Modal content -->
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white rounded-lg max-w-2xl w-full shadow-xl"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $avisoSeleccionado->aviso->titulo }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $avisoSeleccionado->aviso->tipo_aviso }} •
                                    Recibido {{ $avisoSeleccionado->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-4">
                        <!-- Metadata -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Ente destinatario</p>
                                    <p class="text-sm font-medium">{{ $avisoSeleccionado->ente->nombre }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Fecha de publicación</p>
                                    <p class="text-sm font-medium">
                                        {{ $avisoSeleccionado->aviso->fecha_publicacion?->format('d/m/Y') ?? 'No definida' }}
                                    </p>
                                </div>
                                @if ($avisoSeleccionado->aviso->fecha_expiracion)
                                    <div>
                                        <p class="text-xs text-gray-500">Fecha de expiración</p>
                                        <p class="text-sm font-medium">
                                            {{ $avisoSeleccionado->aviso->fecha_expiracion->format('d/m/Y') }}
                                        </p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-xs text-gray-500">Creado por</p>
                                    <p class="text-sm font-medium">{{ $avisoSeleccionado->aviso->creador->name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="prose max-w-none">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Contenido del aviso:</h4>
                            <div class="text-gray-700 whitespace-pre-line">
                                {{ $avisoSeleccionado->aviso->texto }}
                            </div>
                        </div>

                        <!-- Archivo adjunto -->
                        @if ($avisoSeleccionado->aviso->archivo)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="{{ Storage::url($avisoSeleccionado->aviso->archivo) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    <span>Ver archivo adjunto</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                        @if ($avisoSeleccionado->estado_envio !== 'leido')
                            <button wire:click="marcarComoLeido({{ $avisoSeleccionado->id }})"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-[#6C143B] hover:bg-[#4a0e29] text-white rounded-lg transition-colors flex items-center">
                                <span wire:loading.remove wire:target="marcarComoLeido({{ $avisoSeleccionado->id }})">
                                    Marcar como leído
                                </span>
                                <span wire:loading wire:target="marcarComoLeido({{ $avisoSeleccionado->id }})">
                                    Procesando...
                                </span>
                            </button>
                        @endif
                        <button @click="show = false"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @script
        <script>
            // Script para polling cada 30 segundos
            setInterval(() => {
                $wire.dispatch('cargarNotificaciones');
            }, 30000);

            // Escuchar eventos
            $wire.on('notificacion-actualizada', () => {
                // Opcional: mostrar un toast o notificación
                console.log('Notificaciones actualizadas');
            });
        </script>
    @endscript
</div>
