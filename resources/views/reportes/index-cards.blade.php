<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reportes') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    {{-- Card: Reporte de Obligaciones Municipales --}}
                    @can('generar-reportes')
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transform hover:scale-105 transition-transform duration-200">
                            <a href="{{ route('reportes.obligaciones.index') }}">
                                <div class="px-4 py-3" style="background-color: #b64747;">
                                    <h2 class="text-white font-bold text-lg flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Obligaciones Municipales
                                    </h2>
                                </div>
                                <div class="p-4">
                                    <ul class="space-y-2">
                                        <li class="flex items-start text-gray-700 hover:text-[#b64747] cursor-pointer">
                                            <span class="w-2 h-2 rounded-full mr-2 mt-1 flex-shrink-0" style="background-color: #b64747;"></span>
                                            <span>Reporte detallado de obligaciones municipales con filtros por ente y año. Permite visualizar el estado de presentación de documentos por periodo.</span>
                                        </li>
                                    </ul>
                                </div>
                            </a>
                        </div>
                    @endcan

                    {{-- Card: Reporte General --}}
                    @can('generar-reportes')
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transform hover:scale-105 transition-transform duration-200">
                            <a href="{{ route('reportes.general') }}">
                                <div class="px-4 py-3" style="background-color: #2C5A8C;">
                                    <h2 class="text-white font-bold text-lg flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Reporte General
                                    </h2>
                                </div>
                                <div class="p-4">
                                    <ul class="space-y-2">
                                        <li class="flex items-start text-gray-700 hover:text-[#2C5A8C] cursor-pointer">
                                            <span class="w-2 h-2 rounded-full mr-2 mt-1 flex-shrink-0" style="background-color: #2C5A8C;"></span>
                                            <span>Genera un reporte general consolidado de toda la información del sistema. Selecciona el año y exporta a Excel.</span>
                                        </li>
                                    </ul>
                                </div>
                            </a>
                        </div>
                    @endcan

                </div>
            </div>
        </div>
    </div>
</x-app-layout>