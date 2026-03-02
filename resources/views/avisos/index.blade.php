<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Avisos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto px-4 py-8">


<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="space-y-4">
        <div>
            <x-label for="titulo" value="{{ __('Título') }}" />
            <x-input id="titulo"
                class="block mt-1 w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 "
                type="text" name="titulo" required autofocus autocomplete="titulo" />
        </div>
        <div class="mt-4">
            <x-label for="tipo_aviso" value="{{ __('Tipo de aviso') }}" />
            <x-input id="tipo_aviso"
                class="block mt-1 w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 "
                type="text" name="tipo_aviso" required autofocus autocomplete="tipo_aviso" />
        </div>
        <div class="mt-4">
            <x-label for="texto" value="{{ __('Mensaje') }}" />
            <x-input id="texto"
                class="block mt-1 w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 "
                type="text" name="texto" required autofocus autocomplete="texto" />
        </div>
        <div class="mt-4">
            <x-label for="url" value="{{ __('Url (opcional)') }}" />
            <x-input id="url"
                class="block mt-1 w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 "
                type="text" name="url" required autofocus autocomplete="url" />
        </div>
    <div>
    <div class="space-y-4">
    <div>
<div>




                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ms-4 bg-vino-900 hover:bg-vino-800 focus:bg-vino-800 active:bg-vino-900">
                            {{ __('Enviar') }}
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
