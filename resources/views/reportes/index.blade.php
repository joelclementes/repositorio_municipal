<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reportes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="text-2xl">
                        Módulo de Reportes
                    </div>

                    <div class="mt-6 text-gray-500">
                        Bienvenido al módulo de reportes. Desde aquí podrás generar y visualizar reportes personalizados a partir de los datos capturados en el sistema.
                    </div>

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative transition-opacity duration-500" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <span @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer">
                                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Cerrar</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                            </span>
                        </div>
                    @endif

                    <div class="mt-8">
                        <div class="text-gray-600 text-sm mb-4">
                            Selecciona el año para generar el reporte general en formato Excel.
                        </div>
                        <form action="{{ route('reportes.export') }}" method="GET" class="flex items-center space-x-4">
                            <div>
                                <select name="anio" id="anio" class="form-select rounded-md shadow-sm border-gray-300 focus:border-[#6c143a] focus:ring focus:ring-[#6c143a] focus:ring-opacity-50" required>
                                    @foreach($anios as $anio)
                                        <option value="{{ $anio }}">{{ $anio }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#6c143a] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#520f2c] focus:bg-[#520f2c] active:bg-[#3d0b21] focus:outline-none focus:ring-2 focus:ring-[#6c143a] focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                </svg>
                                Exportar Reporte a Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
