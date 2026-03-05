<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Documentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> --}}
        <div class="px-6">
            {{-- Aquí va un select con las categorías que recibe de DocumentoRegistroController --}}
            <select class=" border-vino-900 focus:border-vino-900 focus:ring-vino-900 rounded-md shadow-sm"
                required name="categoria" id="categoria">
                <option value="">Seleccione una categoría</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                @endforeach
            </select>

            {{-- Aquí va un select con las subcategorías que recibe de DocumentoRegistroController --}}
            <select class=" border-vino-900 focus:border-vino-900 focus:ring-vino-900 rounded-md shadow-sm" name="subcategoria" id="subcategoria">
                <option value="">Seleccione una subcategoría</option>
                @foreach ($subcategorias as $subcategoria)
                    <option value="{{ $subcategoria->id }}">{{ $subcategoria->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>
</x-app-layout>
