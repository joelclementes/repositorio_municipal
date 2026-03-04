{{-- resources/views/livewire/avisos/avisos-panel.blade.php --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
    {{-- Columna izquierda: Lista de avisos --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Avisos</h3>
            
            {{-- Buscador de avisos --}}
            <div class="mt-2">
                <input type="text" 
                       wire:model.live.debounce.300ms="searchAviso"
                       placeholder="Buscar avisos..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-transparent">
            </div>
        </div>

        <div class="overflow-y-auto max-h-[600px]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->avisos as $aviso)
                        <tr wire:key="aviso-{{ $aviso->id }}"
                            wire:click="seleccionarAviso({{ $aviso->id }})"
                            class="cursor-pointer transition-colors {{ $avisoSeleccionadoId === $aviso->id ? 'bg-vino-50 border-l-4 border-vino-900' : 'hover:bg-gray-50' }}">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $aviso->titulo }}</div>
                                @if(!$aviso->activo)
                                    <span class="inline-flex mt-1 px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($aviso->tipo_aviso === 'Aviso') bg-blue-100 text-blue-800
                                    @elseif($aviso->tipo_aviso === 'Invitación') bg-green-100 text-green-800
                                    @elseif($aviso->tipo_aviso === 'Exhorto') bg-yellow-100 text-yellow-800
                                    @elseif($aviso->tipo_aviso === 'Convocatoria') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $aviso->tipo_aviso }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $aviso->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="px-2 py-1 bg-gray-100 rounded-full text-xs">
                                    {{ $aviso->total_entes ?? $aviso->avisoEntes->count() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                No hay avisos disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Columna derecha: Detalles del aviso seleccionado --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($this->avisoSeleccionado)
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <div class="grid">
                    <h3 class="text-md font-semibold text-gray-700">
                        {{ $this->avisoSeleccionado->titulo }}
                    </h3>
                    
                    {{-- Filtros por estado de lectura --}}
                    {{-- <div class="mt-2">
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" 
                                       wire:model.live="filtroEstado" 
                                       value="todos"
                                       class="border-vino-900 text-vino-900 focus:ring-vino-900">
                                <span class="ml-2 text-sm text-gray-700">Todos</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" 
                                       wire:model.live="filtroEstado" 
                                       value="leido"
                                       class="border-vino-900 text-vino-900 focus:ring-vino-900">
                                <span class="ml-2 text-sm text-gray-700">Leídos</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" 
                                       wire:model.live="filtroEstado" 
                                       value="no_leido"
                                       class="border-vino-900 text-vino-900 focus:ring-vino-900">
                                <span class="ml-2 text-sm text-gray-700">No leídos</span>
                            </label>
                        </div>
                    </div> --}}
                </div>
            </div>

            <div class="overflow-y-auto max-h-[600px]">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ente</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha envío</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha lectura</th>
                            {{-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th> --}}
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($this->entesFiltrados as $avisoEnte)
                            <tr wire:key="ente-{{ $avisoEnte->id }}">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $avisoEnte->ente->nombre }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($avisoEnte->estado_envio === 'leido') bg-green-100 text-green-800
                                        @elseif($avisoEnte->estado_envio === 'pendiente') bg-yellow-100 text-yellow-800
                                        @elseif($avisoEnte->estado_envio === 'enviado') bg-blue-100 text-blue-800
                                        @elseif($avisoEnte->estado_envio === 'entregado') bg-indigo-100 text-indigo-800
                                        @elseif($avisoEnte->estado_envio === 'vencido') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($avisoEnte->estado_envio) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $avisoEnte->fecha_envio ? $avisoEnte->fecha_envio->format('d/m/Y H:i') : 'No enviado' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $avisoEnte->fecha_lectura ? $avisoEnte->fecha_lectura->format('d/m/Y H:i') : 'No leído' }}
                                </td>
                                {{-- <td class="px-4 py-3">
                                    <select wire:change="actualizarEstado({{ $avisoEnte->id }}, $event.target.value)"
                                            class="text-xs border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-1 focus:ring-vino-900">
                                        <option value="pendiente" {{ $avisoEnte->estado_envio === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="enviado" {{ $avisoEnte->estado_envio === 'enviado' ? 'selected' : '' }}>Enviado</option>
                                        <option value="entregado" {{ $avisoEnte->estado_envio === 'entregado' ? 'selected' : '' }}>Entregado</option>
                                        <option value="leido" {{ $avisoEnte->estado_envio === 'leido' ? 'selected' : '' }}>Leído</option>
                                        <option value="vencido" {{ $avisoEnte->estado_envio === 'vencido' ? 'selected' : '' }}>Vencido</option>
                                    </select>
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No hay entes para mostrar con el filtro seleccionado
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Resumen estadístico --}}
            @if($this->entesFiltrados->isNotEmpty())
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    <div class="grid grid-cols-4 gap-4 text-center">
                        <div>
                            <span class="text-xs text-gray-500">Total</span>
                            <p class="text-lg font-bold text-gray-700">{{ $this->entesFiltrados->count() }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Leídos</span>
                            <p class="text-lg font-bold text-green-600">
                                {{ $this->entesFiltrados->where('estado_envio', 'leido')->count() }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">No leídos</span>
                            <p class="text-lg font-bold text-yellow-600">
                                {{ $this->entesFiltrados->where('estado_envio', '!=', 'leido')->count() }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Pendientes</span>
                            <p class="text-lg font-bold text-blue-600">
                                {{ $this->entesFiltrados->where('estado_envio', 'pendiente')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="flex items-center justify-center h-full min-h-[400px]">
                <div class="text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-lg">Selecciona un aviso de la lista</p>
                    <p class="text-sm text-gray-400 mt-2">para ver los entes relacionados</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Notificaciones toast --}}
    @script
    <script>
        $wire.on('estado-actualizado', (data) => {
            // Crear notificación
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in-down';
            notification.textContent = data.message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        });
    </script>
    @endscript
</div>

{{-- Estilos adicionales --}}
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