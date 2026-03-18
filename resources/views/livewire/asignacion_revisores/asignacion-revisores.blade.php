<div>
    {{-- Título --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Asignación de Revisores a Municipios</h1>
        <p class="text-sm text-gray-600 mt-1">Selecciona un revisor y marca los municipios para asignarlos automáticamente</p>
    </div>

    {{-- Mensajes de notificación --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded"
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)">
            {{ session('error') }}
        </div>
    @endif

    {{-- Indicador de guardado automático (opcional) --}}
    <div 
        x-data="{ show: false }"
        x-on:asignacion-guardada.window="show = true; setTimeout(() => show = false, 1500)"
        x-show="show"
        x-transition
        class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2 z-50"
        style="display: none;"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>Cambios guardados</span>
    </div>

    {{-- Formulario (sin botón de guardar) --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Columna izquierda: Lista de Revisores --}}
            <div class="lg:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Lista de Revisores <span class="text-red-500">*</span>
                </label>
                
                <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto bg-gray-50">
                    <div class="space-y-1 p-2">
                        @forelse($revisores as $revisor)
                            <button 
                                type="button"
                                wire:click="$set('revisor_id', '{{ $revisor->id }}')"
                                class="w-full text-left p-3 rounded-md transition-all duration-200 
                                    {{ $revisor_id == $revisor->id 
                                        ? 'bg-vino-900 text-white shadow-md transform scale-[1.02]' 
                                        : 'bg-white hover:bg-gray-100 text-gray-700 hover:shadow border border-gray-200' 
                                    }}"
                            >
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-3">
                                        @if($revisor->profile_photo_path)
                                            <img class="h-10 w-10 rounded-full object-cover" 
                                                 src="{{ asset('storage/'.$revisor->profile_photo_path) }}" 
                                                 alt="{{ $revisor->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full {{ $revisor_id == $revisor->id ? 'bg-vino-700' : 'bg-gray-300' }} flex items-center justify-center">
                                                <span class="text-sm font-medium {{ $revisor_id == $revisor->id ? 'text-white' : 'text-gray-600' }}">
                                                    {{ strtoupper(substr($revisor->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium truncate {{ $revisor_id == $revisor->id ? 'text-white' : 'text-gray-900' }}">
                                            {{ $revisor->name }}
                                        </p>
                                        <p class="text-xs {{ $revisor_id == $revisor->id ? 'text-vino-100' : 'text-gray-500' }} truncate">
                                            {{ $revisor->email }}
                                        </p>
                                    </div>
                                    
                                    @if($revisor_id == $revisor->id)
                                        <svg class="w-5 h-5 text-white ml-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                                
                                @php
                                    $cantidadAsignados = App\Models\EnteRevisor::where('revisor_id', $revisor->id)->count();
                                @endphp
                                @if($cantidadAsignados > 0)
                                    <div class="mt-2 text-xs {{ $revisor_id == $revisor->id ? 'text-vino-100' : 'text-gray-500' }}">
                                        {{ $cantidadAsignados }} municipio(s) asignado(s)
                                    </div>
                                @endif
                            </button>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p>No hay revisores disponibles</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 mt-2">
                    <span class="inline-block w-3 h-3 bg-vino-900 rounded mr-1"></span> 
                    Revisor seleccionado actualmente
                </p>
            </div>

            {{-- Columna derecha: Municipios --}}
            <div class="lg:col-span-2">
                @if($revisor_id)
                    <div>
                        <div class="mb-4 flex justify-between items-center">
                            <label class="block text-sm font-medium text-gray-700">
                                Municipios disponibles <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 ml-2">(los cambios se guardan automáticamente)</span>
                            </label>
                            <div class="flex space-x-2">
                                <button type="button" 
                                        wire:click="seleccionarTodos"
                                        class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">
                                    Seleccionar todos
                                </button>
                                <button type="button" 
                                        wire:click="deseleccionarTodos"
                                        class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">
                                    Deseleccionar todos
                                </button>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto p-2 bg-gray-50">
                            <div class="space-y-2">
                                @forelse($entes as $ente)
                                    @php
                                        $isAsignado = in_array($ente->id, $entesSeleccionados);
                                        $isDisabled = $ente->asignado_a_otro;
                                        $bgClass = $isAsignado ? 'bg-green-50' : ($isDisabled ? 'bg-gray-100' : 'bg-white');
                                        $borderClass = $isAsignado ? 'border-green-300' : ($isDisabled ? 'border-gray-300' : 'border-gray-200');
                                    @endphp
                                    
                                    <div class="flex items-center p-3 {{ $bgClass }} border {{ $borderClass }} rounded-md {{ $isDisabled ? 'opacity-75' : 'hover:border-vino-900' }} transition-colors mb-2">
                                        <input type="checkbox" 
                                               wire:model.live="entesSeleccionados"
                                               value="{{ $ente->id }}" 
                                               id="ente-{{ $ente->id }}"
                                               class="w-4 h-4 text-vino-900 border-gray-300 rounded focus:ring-vino-900"
                                               {{ $isAsignado ? 'checked' : '' }}
                                               {{ $isDisabled ? 'disabled' : '' }}>
                                        <label for="ente-{{ $ente->id }}" class="ml-3 block text-sm text-gray-700 flex-1 cursor-{{ $isDisabled ? 'not-allowed' : 'pointer' }}">
                                            <span class="font-medium">{{ $ente->nombre }}</span>
                                            @if($ente->tipoEnte)
                                                <span class="text-xs text-gray-500 ml-2">({{ $ente->tipoEnte->nombre }})</span>
                                            @endif
                                        </label>
                                        @if($isAsignado)
                                            <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full">Asignado</span>
                                        @elseif($isDisabled)
                                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">Ocupado</span>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p>No hay municipios disponibles</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-2">
                            <span class="inline-block w-3 h-3 bg-green-100 border border-green-300 rounded mr-1"></span> 
                            Municipios ya asignados a este revisor
                            <br>
                            <span class="inline-block w-3 h-3 bg-white border border-gray-300 rounded mr-1 mt-1"></span>
                            Municipios disponibles
                            <br>
                            <span class="inline-block w-3 h-3 bg-gray-100 border border-gray-300 rounded mr-1 mt-1"></span>
                            Municipios asignados a otros revisores (no disponibles)
                        </p>
                    </div>

                    {{-- Botón de cancelar solamente --}}
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('dashboard') }}" 
                           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors text-sm">
                            Cancelar
                        </a>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-400 border border-gray-200 rounded-lg bg-gray-50">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-lg">Seleccione un revisor de la lista para ver los municipios disponibles</p>
                        <p class="text-sm mt-2">Haga clic en cualquier revisor de la columna izquierda para comenzar</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .bg-vino-700 {
            background-color: #7c2d12;
        }
        .bg-vino-900 {
            background-color: #7c2d12;
        }
        .hover\:bg-vino-800:hover {
            background-color: #92400e;
        }
        .border-vino-900 {
            border-color: #7c2d12;
        }
        .text-vino-100 {
            color: #ffedd5;
        }
        .focus\:ring-vino-900:focus {
            --tw-ring-color: #7c2d12;
        }
        .text-vino-900 {
            color: #7c2d12;
        }
        .transform {
            transition-property: transform;
        }
        .scale-\[1\.02\] {
            transform: scale(1.02);
        }
    </style>
</div>