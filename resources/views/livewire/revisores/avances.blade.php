<div>
    <div class="w-1/3 mx-auto mb-6">
        {{-- Select de Periodos --}}
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
            required wire:model.live="periodosSeleccionados">
            <option value="" class="text-gray-500">📅 Seleccione un periodo</option>
            @foreach ($this->periodos as $periodo)
                <option value="{{ $periodo->id }}" class="py-1" {{ !$periodo->is_active ? 'disabled' : '' }}>
                    {{ ucfirst($periodo->descripcion) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="w-1/3 mx-auto mb-6">
        
    </div>
    {{-- Columna izquierda: Lista de Revisores --}}
    <div class="lg:col-span-1">
        <label class="block text-sm font-medium text-gray-700 mb-3">
            Lista de Revisores <span class="text-red-500">*</span>
        </label>
        {{-- Select de Revisores --}}
        <select
            class="w-full px-3 py-2 text-sm border border-vino-900 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-vino-900 focus:border-vino-900 bg-white"
            required wire:model.live="revisoresSeleccionados">
            <option value="" class="text-gray-500">👤 Seleccione un revisor</option>
            @foreach ($this->revisores as $revisor)
                <option value="{{ $revisor->id }}" class="py-1">
                    {{ ucfirst($revisor->name) }}
                </option>
            @endforeach
        </select>
    </div>
    {{-- Columna derecha: Avance por ente --}}
    <div class="lg:col-span-2">

    </div>

</div>
