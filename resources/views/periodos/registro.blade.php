<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Periodos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-4 lg:px-6">
            <div class="p-6">
                <livewire:periodos.registro-periodos />
            </div>
        </div>
    </div>
</x-app-layout>
