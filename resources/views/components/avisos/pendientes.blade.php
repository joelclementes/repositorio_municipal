{{-- resources/views/avisos/pendientes.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Todos mis avisos pendientes') }}
            </h2>

            {{-- Botón para marcar todos como leídos (solo si hay pendientes) --}}
            @if ($avisosPendientes->total() > 0)
                <form action="{{ route('avisos.marcar-todos-leidos') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-[#6C143B] text-white px-4 py-2 rounded-md hover:bg-[#4a0e29] transition-colors text-sm font-medium"
                        onclick="return confirm('¿Estás seguro de marcar TODOS los avisos como leídos?')">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Marcar todos como leídos
                        </span>
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de éxito --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Contenido principal --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">

                    {{-- Resumen rápido --}}
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <p class="text-xs text-blue-600 uppercase font-bold">Totales</p>
                            <p class="text-2xl font-bold text-blue-800">{{ $avisosPendientes->total() }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <p class="text-xs text-yellow-600 uppercase font-bold">Pendientes</p>
                            <p class="text-2xl font-bold text-yellow-800">
                                {{ $avisosPendientes->where('estado_envio', 'pendiente')->count() }}
                            </p>
                        </div>
                        {{-- <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <p class="text-xs text-blue-600 uppercase font-bold">Enviados</p>
                            <p class="text-2xl font-bold text-blue-800">
                                {{ $avisosPendientes->where('estado_envio', 'enviado')->count() }}
                            </p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <p class="text-xs text-green-600 uppercase font-bold">Entregados</p>
                            <p class="text-2xl font-bold text-green-800">
                                {{ $avisosPendientes->where('estado_envio', 'entregado')->count() }}
                            </p>
                        </div> --}}
                    </div>

                    {{-- Lista de avisos --}}
                    @forelse($avisosPendientes as $avisoEnte)
                        <div class="border rounded-lg mb-4 last:mb-0 hover:shadow-md transition-shadow"
                            x-data="{ expanded: false }">

                            {{-- Cabecera del aviso (siempre visible) --}}
                            <div class="p-4 bg-gray-50 rounded-t-lg border-b flex justify-between items-center cursor-pointer"
                                @click="expanded = !expanded">

                                <div class="flex items-center space-x-4 flex-1">
                                    {{-- Indicador de estado --}}
                                    <span class="flex-shrink-0 w-3 h-3 rounded-full"
                                        style="background-color: {{ $avisoEnte->estado_envio === 'pendiente'
                                            ? '#FBBF24'
                                            : ($avisoEnte->estado_envio === 'enviado'
                                                ? '#3B82F6'
                                                : ($avisoEnte->estado_envio === 'entregado'
                                                    ? '#10B981'
                                                    : '#6B7280')) }}"></span>

                                    {{-- Tipo de aviso con color --}}
                                    <span
                                        class="px-2 py-1 text-xs rounded-full 
                                        @if ($avisoEnte->aviso->tipo_aviso === 'Aviso') bg-blue-100 text-blue-800
                                        @elseif($avisoEnte->aviso->tipo_aviso === 'Invitación') bg-green-100 text-green-800
                                        @elseif($avisoEnte->aviso->tipo_aviso === 'Exhorto') bg-yellow-100 text-yellow-800
                                        @elseif($avisoEnte->aviso->tipo_aviso === 'Convocatoria') bg-purple-100 text-purple-800
                                        @elseif($avisoEnte->aviso->tipo_aviso === 'Circular') bg-purple-100 text-orange-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $avisoEnte->aviso->tipo_aviso }}
                                    </span>

                                    {{-- Título --}}
                                    <h3 class="text-lg font-semibold text-gray-900 flex-1">
                                        {{ $avisoEnte->aviso->titulo }}
                                    </h3>

                                    {{-- Fecha --}}
                                    <span class="text-sm text-gray-500">
                                        {{ $avisoEnte->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                {{-- Icono expandir --}}
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            {{-- Contenido detallado (expandible) --}}
                            <div x-show="expanded" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100" class="p-6 bg-white">

                                {{-- Metadatos --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-xs text-gray-500">Estado de envío</p>
                                        <p class="text-sm font-medium">
                                            @switch($avisoEnte->estado_envio)
                                                @case('pendiente')
                                                    <span class="text-yellow-600">⏳ Pendiente de envío</span>
                                                @break

                                                @case('enviado')
                                                    <span class="text-blue-600">📤 Enviado</span>
                                                @break

                                                @case('entregado')
                                                    <span class="text-green-600">✅ Entregado</span>
                                                @break

                                                @case('leido')
                                                    <span class="text-purple-600">👁️ Leído</span>
                                                @break

                                                @case('vencido')
                                                    <span class="text-red-600">⚠️ Vencido</span>
                                                @break

                                                @default
                                                    <span>{{ $avisoEnte->estado_envio }}</span>
                                            @endswitch
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500">Fecha de envío</p>
                                        <p class="text-sm font-medium">
                                            {{ $avisoEnte->fecha_envio ? $avisoEnte->fecha_envio->format('d/m/Y H:i') : 'No enviado' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500">Intentos de envío</p>
                                        <p class="text-sm font-medium">{{ $avisoEnte->intentos_envio }}</p>
                                    </div>

                                    @if ($avisoEnte->fecha_lectura)
                                        <div>
                                            <p class="text-xs text-gray-500">Fecha de lectura</p>
                                            <p class="text-sm font-medium">
                                                {{ $avisoEnte->fecha_lectura->format('d/m/Y H:i') }}</p>
                                        </div>
                                    @endif

                                    <div>
                                        <p class="text-xs text-gray-500">Creado por</p>
                                        <p class="text-sm font-medium">
                                            {{ $avisoEnte->aviso->creador->name ?? 'Sistema' }}</p>
                                    </div>

                                    @if ($avisoEnte->aviso->fecha_expiracion)
                                        <div>
                                            <p class="text-xs text-gray-500">Fecha de expiración</p>
                                            <p
                                                class="text-sm font-medium 
                                            @if ($avisoEnte->aviso->fecha_expiracion < now()) text-red-600 @endif">
                                                {{ $avisoEnte->aviso->fecha_expiracion->format('d/m/Y') }}
                                                @if ($avisoEnte->aviso->fecha_expiracion < now())
                                                    (Expirado)
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Contenido del aviso --}}
                                <div class="prose max-w-none mb-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Contenido:</h4>
                                    <div class="text-gray-700 whitespace-pre-line bg-gray-50 p-4 rounded-lg">
                                        {{ $avisoEnte->aviso->texto }}
                                    </div>
                                </div>

                                {{-- Archivo adjunto --}}
                                @if ($avisoEnte->aviso->archivo)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <a href="{{ route('avisos.descargar', $avisoEnte->id) }}" target="_blank"
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

                                {{-- Acciones --}}
                                @if ($avisoEnte->estado_envio !== 'leido')
                                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end">
                                        <form action="{{ route('avisos.marcar-leido', $avisoEnte->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="bg-[#6C143B] text-white px-4 py-2 rounded-md hover:bg-[#4a0e29] transition-colors flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Marcar como leído
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @empty
                            {{-- Mensaje cuando no hay avisos --}}
                            <div class="text-center py-12">
                                <div class="mb-4">
                                    <svg class="w-24 h-24 mx-auto text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-medium text-gray-900 mb-2">¡Todo al día!</h3>
                                <p class="text-gray-500 mb-6">No tienes avisos pendientes por leer en este momento.</p>
                                <a href="{{ route('dashboard') }}"
                                    class="inline-flex items-center px-4 py-2 bg-[#6C143B] text-white rounded-md hover:bg-[#4a0e29] transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Volver al inicio
                                </a>
                            </div>
                        @endforelse

                        {{-- Paginación --}}
                        @if ($avisosPendientes->hasPages())
                            <div class="mt-6">
                                {{ $avisosPendientes->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
