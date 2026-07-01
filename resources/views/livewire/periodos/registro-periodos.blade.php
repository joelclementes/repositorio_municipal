<div>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Registro de Periodos</h1>
        <p class="text-sm text-gray-600 mt-1">
            Administra periodos generales y fechas específicas por organismo.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3500)">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Periodo</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periodo existente</label>
                    <select wire:model.live="periodo_id"
                            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white">
                        <option value="">Crear periodo nuevo</option>
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo->id }}">
                                {{ $periodo->descripcion }} — {{ ucfirst($periodo->mes) }} {{ $periodo->axo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
                        <select wire:model.live="mes_numero"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white">
                            <option value="">Seleccione</option>
                            @foreach ($meses as $numero => $nombre)
                                <option value="{{ $numero }}">{{ ucfirst($nombre) }}</option>
                            @endforeach
                        </select>
                        @error('mes_numero') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                        <input type="number"
                            wire:model.live="axo"
                            min="2000"
                            max="2100"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900">
                        @error('axo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text"
                        wire:model="descripcion"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm text-gray-700 focus:outline-none">
                    @error('descripcion') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio</label>
                        <input type="date"
                               wire:model="fecha_inicio"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900">
                        @error('fecha_inicio') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin</label>
                        <input type="date"
                               wire:model="fecha_fin"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900">
                        @error('fecha_fin') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button"
                            wire:click="resetFormularioCompleto"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm">
                        Limpiar
                    </button>

                    <button type="button"
                            wire:click="guardarPeriodo"
                            class="px-4 py-2 bg-vino-900 hover:bg-vino-800 text-white rounded-md text-sm">
                        {{ $periodo_id ? 'Actualizar' : 'Crear' }}
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Organismos</h3>

            @if (!$periodo_id)
                <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 rounded">
                    Seleccione un periodo para modificar fechas por organismo.
                </div>
            @endif

            <div class="space-y-4 {{ !$periodo_id ? 'opacity-50 pointer-events-none' : '' }}">
                {{-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                    <select wire:model.live="periodo_id"
                            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white">
                        <option value="">Seleccione un periodo</option>
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo->id }}">
                                {{ $periodo->descripcion }} — {{ ucfirst($periodo->mes) }} {{ $periodo->axo }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organismo</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="ente_busqueda"
                           placeholder="Teclee al menos 2 caracteres..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900">

                    @if ($this->entesFiltrados->isNotEmpty())
                        <div class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            @foreach ($this->entesFiltrados as $ente)
                                <button type="button"
                                        wire:click="seleccionarEnte({{ $ente->id }})"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-vino-50 hover:text-vino-900 border-b last:border-b-0">
                                    {{ $ente->nombre }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    @error('periodo_ente_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio</label>
                        <input type="date"
                               wire:model="ente_fecha_inicio"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900">
                        @error('ente_fecha_inicio') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin</label>
                        <input type="date"
                               wire:model="ente_fecha_fin"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900">
                        @error('ente_fecha_fin') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button"
                            wire:click="limpiarEnte"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm">
                        Limpiar
                    </button>

                    <button type="button"
                            wire:click="actualizarPeriodoEnte"
                            class="px-4 py-2 bg-vino-900 hover:bg-vino-800 text-white rounded-md text-sm">
                        Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
