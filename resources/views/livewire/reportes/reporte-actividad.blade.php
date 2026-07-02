<div class="space-y-6">
    <!-- Encabezado del Reporte -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-200 pb-5">
        <div>
            <h3 class="text-lg font-bold text-vino-800 leading-6">Bitácora de Actividades (Auditoría)</h3>
            <p class="text-sm text-gray-500 mt-1">Supervisión en tiempo real de accesos, registros y acciones de usuarios del congreso y municipios usando Spatie Activitylog.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Botón PDF -->
            <a href="{{ $this->urlPdf }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-vino-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-vino-700 active:bg-vino-950 focus:outline-none focus:border-vino-950 focus:ring focus:ring-vino-300 disabled:opacity-25 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Exportar PDF
            </a>
            <!-- Botón Excel -->
            <a href="{{ $this->urlExcel }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring focus:ring-green-200 disabled:opacity-25 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exportar Excel
            </a>
        </div>
    </div>

    <!-- Barra de Filtros Principales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-sm">
        <!-- Buscador -->
        <div class="md:col-span-2">
            <label for="buscar" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Buscar por usuario, acción o descripción</label>
            <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="terminoBusqueda" type="text" id="buscar" class="focus:ring-vino-500 focus:border-vino-500 block w-full pl-9 sm:text-sm border-gray-300 rounded-md" placeholder="Buscar acción, usuario, correo...">
            </div>
        </div>

        <!-- Fecha Desde -->
        <div>
            <label for="desde" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Desde</label>
            <input wire:model.live="fechaDesde" type="date" id="desde" class="focus:ring-vino-500 focus:border-vino-500 block w-full sm:text-sm border-gray-300 rounded-md">
        </div>

        <!-- Fecha Hasta -->
        <div>
            <label for="hasta" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Hasta</label>
            <input wire:model.live="fechaHasta" type="date" id="hasta" class="focus:ring-vino-500 focus:border-vino-500 block w-full sm:text-sm border-gray-300 rounded-md">
        </div>

        <!-- Botones de Acción de Filtro -->
        <div class="md:col-span-4 flex items-center justify-between mt-2 pt-2 border-t border-gray-200">
            <button wire:click="$toggle('mostrarFiltros')" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-vino-500">
                <svg class="w-4 h-4 mr-2 {{ $mostrarFiltros ? 'text-vino-800' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filtros Avanzados
                @if(!empty($this->tipoUsuario) || !empty($this->entesSeleccionados) || !empty($this->estatusUsuarios))
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-vino-100 text-vino-850">
                        Activos
                    </span>
                @endif
            </button>

            @if($this->tieneFiltrosActivos)
                <button wire:click="limpiarFiltros" type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Limpiar todos los filtros
                </button>
            @endif
        </div>
    </div>

    <!-- Panel de Filtros Avanzados -->
    @if($mostrarFiltros)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-white p-6 rounded-xl border border-gray-200 shadow-md transition-all duration-300" wire:key="advanced-filter-panel">
            <!-- Columna 1: Tipo de Usuario (Spatie Roles) -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-vino-800 border-b border-vino-100 pb-2">Tipo de Usuario</h4>
                <div class="space-y-2">
                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-55 cursor-pointer">
                        <input wire:model.live="tipoUsuario" type="checkbox" value="congreso" class="rounded border-gray-300 text-vino-800 focus:ring-vino-500 w-5 h-5">
                        <span class="ml-3 text-sm text-gray-700 font-medium">Congreso (Administradores, Revisores)</span>
                    </label>
                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-55 cursor-pointer">
                        <input wire:model.live="tipoUsuario" type="checkbox" value="municipio" class="rounded border-gray-300 text-vino-800 focus:ring-vino-500 w-5 h-5">
                        <span class="ml-3 text-sm text-gray-700 font-medium">Municipio (Tesoreros, Contralores)</span>
                    </label>
                </div>
            </div>

            <!-- Columna 2: Estatus del Usuario -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-vino-800 border-b border-vino-100 pb-2">Estatus del Usuario</h4>
                <div class="space-y-2">
                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-55 cursor-pointer">
                        <input wire:model.live="estatusUsuarios" type="checkbox" value="activos" class="rounded border-gray-300 text-vino-800 focus:ring-vino-500 w-5 h-5">
                        <span class="ml-3 text-sm text-gray-700 font-medium">Activos</span>
                    </label>
                    <label class="flex items-center p-2 rounded-lg hover:bg-gray-55 cursor-pointer">
                        <input wire:model.live="estatusUsuarios" type="checkbox" value="inactivos" class="rounded border-gray-300 text-vino-800 focus:ring-vino-500 w-5 h-5">
                        <span class="ml-3 text-sm text-gray-700 font-medium">Inactivos</span>
                    </label>
                </div>
            </div>

            <!-- Columna 3: Entes Municipales (desplegable si se selecciona Municipio) -->
            <div class="space-y-4 md:col-span-1 border-t md:border-t-0 pt-4 md:pt-0">
                <div class="flex items-center justify-between border-b border-vino-100 pb-2">
                    <h4 class="text-sm font-bold text-vino-800">Filtrar por Entes Municipales</h4>
                    <span class="text-xs text-gray-500 font-semibold">Seleccionados: {{ count($entesSeleccionados) }}</span>
                </div>

                @if(in_array('municipio', $tipoUsuario))
                    <div class="space-y-3">
                        <!-- Buscador de Entes -->
                        <div class="relative rounded-md shadow-sm">
                            <input wire:model.live.debounce.250ms="filtroEnte" type="text" class="focus:ring-vino-500 focus:border-vino-500 block w-full pr-10 sm:text-sm border-gray-300 rounded-md" placeholder="Buscar municipio...">
                            @if(!empty($filtroEnte))
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" wire:click="$set('filtroEnte', '')">
                                    <svg class="h-4 w-4 text-red-500 hover:text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Botones de selección rápida -->
                        <div class="flex gap-2">
                            <button wire:click="seleccionarTodosEntes" type="button" class="w-1/2 py-1 px-2 border border-gray-300 text-xs font-semibold rounded bg-gray-50 hover:bg-gray-100 text-gray-700">
                                Todos
                            </button>
                            <button wire:click="limpiarTodosEntes" type="button" class="w-1/2 py-1 px-2 border border-gray-300 text-xs font-semibold rounded bg-gray-50 hover:bg-gray-100 text-gray-700">
                                Ninguno
                            </button>
                        </div>

                        <!-- Listado de Entes con Scroll -->
                        <div class="border border-gray-200 rounded-lg overflow-y-auto max-h-60 p-2 space-y-1 bg-gray-50">
                            @forelse($this->entes as $ente)
                                <label class="flex items-center p-1.5 rounded hover:bg-white cursor-pointer transition-colors" wire:key="ente-item-{{ $ente->id }}">
                                    <input wire:model.live="entesSeleccionados" type="checkbox" value="{{ $ente->id }}" class="rounded border-gray-300 text-vino-800 focus:ring-vino-500 w-5 h-5">
                                    <span class="ml-3 text-sm text-gray-750 font-normal">{{ $ente->nombre }}</span>
                                </label>
                            @empty
                                <div class="text-center py-4 text-sm text-gray-500">No se encontraron entes.</div>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-sm text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                        Selecciona el tipo "Municipio" para habilitar el filtrado por entes.
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Tabla de Bitácora -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider font-sans">Fecha / Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider font-sans">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider font-sans">Origen / Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider font-sans">Acción</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider font-sans">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider font-sans">Dirección IP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($actividades as $actividad)
                        <tr class="hover:bg-gray-50 transition-colors" wire:key="log-row-{{ $actividad->id }}">
                            <!-- Fecha / Hora -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-sans">
                                {{ $actividad->created_at->format('d/m/Y H:i:s') }}
                                <span class="block text-xs text-gray-400 mt-0.5">{{ $actividad->created_at->diffForHumans() }}</span>
                            </td>

                            <!-- Usuario -->
                            <td class="px-6 py-4 whitespace-nowrap font-sans">
                                <div class="text-sm font-semibold text-gray-900">{{ $actividad->causer?->name ?? 'Sistema / Proceso' }}</div>
                                <div class="text-xs text-gray-500">{{ $actividad->causer?->email }}</div>
                            </td>

                            <!-- Origen / Rol (Spatie Role Check) -->
                            <td class="px-6 py-4 whitespace-nowrap font-sans">
                                @if($actividad->causer)
                                    @if($actividad->causer->hasAnyRole(['SuperUsuario', 'Administrador', 'Revisor']))
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#6c143a]/10 text-[#6c143a] block w-fit">
                                            {{ $actividad->causer->roles->first()?->name ?? 'Congreso' }}
                                        </span>
                                        <span class="block text-[10px] text-gray-400 mt-1 uppercase font-semibold">Congreso</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#b24280]/10 text-[#b24280] block w-fit">
                                            {{ $actividad->causer->ente?->nombre ?? 'N/A' }}
                                        </span>
                                        <span class="block text-[10px] text-gray-400 mt-1 uppercase font-semibold">Municipio</span>
                                    @endif
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 block w-fit">
                                        Sistema
                                    </span>
                                @endif
                            </td>

                            <!-- Acción (Log Name) -->
                            <td class="px-6 py-4 whitespace-nowrap font-sans">
                                @php
                                    $action = $actividad->log_name;
                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                    if (str_contains($action, 'Inicio de sesión')) {
                                        $badgeClass = 'bg-blue-100 text-blue-800';
                                    } elseif (str_contains($action, 'Aprobación') || str_contains($action, 'Creación')) {
                                        $badgeClass = 'bg-green-100 text-green-800';
                                    } elseif (str_contains($action, 'Rechazo') || str_contains($action, 'Eliminación')) {
                                        $badgeClass = 'bg-red-100 text-red-800';
                                    } elseif (str_contains($action, 'Carga')) {
                                        $badgeClass = 'bg-teal-100 text-teal-800';
                                    } elseif (str_contains($action, 'Actualización')) {
                                        $badgeClass = 'bg-amber-100 text-amber-800';
                                    }
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider {{ $badgeClass }}">
                                    {{ $action }}
                                </span>
                            </td>

                            <!-- Descripción -->
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs break-words font-sans">
                                {{ $actividad->description }}
                            </td>

                            <!-- IP / Dispositivo (extraído del json properties) -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-sans">
                                @php
                                    $ip = $actividad->properties ? $actividad->getExtraProperty('ip') : null;
                                    $userAgent = $actividad->properties ? $actividad->getExtraProperty('user_agent') : null;
                                @endphp
                                <span class="font-mono text-xs bg-gray-50 px-2 py-1 rounded border border-gray-150">
                                    {{ $ip ?? 'N/A' }}
                                </span>
                                @if($userAgent)
                                    <span class="block text-[10px] text-gray-400 mt-1 max-w-[150px] truncate" title="{{ $userAgent }}">
                                        {{ $userAgent }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center font-sans">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-900">Sin registros</h3>
                                <p class="text-xs text-gray-500 mt-1">No se encontraron actividades registradas con los filtros seleccionados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($actividades->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $actividades->links() }}
            </div>
        @endif
    </div>
</div>
