<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Asignación de Entes a Revisores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-4 lg:px-6">
            <div class="p-6">
                <livewire:asignacion_revisores.asignacion-revisores />
            </div>
        </div>
    </div>
</x-app-layout>