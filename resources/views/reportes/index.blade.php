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
