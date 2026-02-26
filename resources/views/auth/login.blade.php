<x-guest-layout>
    @push('styles')
        <!-- Incluir FontAwesome si no está en el layout principal -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            /* Definir colores personalizados si no existen en tailwind.config.js */
            .bg-red-50 {
                background-color: #fef2f2 !important;
            }

            .border-red-200 {
                border-color: #fecaca !important;
            }

            .text-red-700 {
                color: #b91c1c !important;
            }

            .bg-vino-900 {
                background-color: #4a0e0e !important;
            }

            .hover\:bg-vino-800:hover {
                background-color: #5a1e1e !important;
            }

            .focus\:bg-vino-800:focus {
                background-color: #5a1e1e !important;
            }

            .border-vino-900 {
                border-color: #4a0e0e !important;
            }

            .focus\:border-vino-900:focus {
                border-color: #4a0e0e !important;
            }

            .focus\:ring-vino-900:focus {
                --tw-ring-color: rgba(74, 14, 14, 0.5) !important;
            }
        </style>
    @endpush

    <x-authentication-card>
        <x-slot name="logo">
            {{-- <x-authentication-card-logo /> --}}
        </x-slot>

        {{-- SOLUCIÓN: Mostrar solo UN componente de errores --}}
        {{-- Opción A: Usar solo x-validation-errors --}}
        <x-validation-errors class="mb-4" />

        {{-- Opción B: Personalizado para usuario desactivado --}}

        {{-- @if ($errors->has('email') && $errors->first('email') == 'Tu cuenta ha sido desactivada. Contacta al administrador.')
            <div class="mb-4 p-3 rounded-md bg-red-50 border border-red-200">
                <p class="text-sm font-medium text-red-700">
                    <i class="fas fa-user-slash mr-2"></i>
                    {{ $errors->first('email') }}
                </p>
            </div>
        @elseif($errors->any())
            <x-validation-errors class="mb-4" />
        @endif --}}


        @if (session('status'))
            <div class="mb-4 p-3 rounded-md bg-green-50 border border-green-200">
                <p class="text-sm font-medium text-green-700">
                    {{ session('status') }}
                </p>
            </div>
        @endif


        <img src="{{ env('APP_LOGO_LEGISLATURA') }}" alt="Logo" class="w-45 h-45">

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Usuario') }}" />
                <x-input id="email"
                    class="block mt-1 w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 {{ $errors->has('email') ? 'border-red-300 focus:border-red-300 focus:ring-red-200' : '' }}"
                    type="text" name="email" :value="old('email')" required autofocus autocomplete="username" />
                {{-- Mostrar error específico para email si existe --}}
                @error('email')
                    <p id="email-error" class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password"
                    class="block mt-1 w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 {{ $errors->has('password') ? 'border-red-300 focus:border-red-300 focus:ring-red-200' : '' }}"
                    type="password" name="password" required autocomplete="current-password" />
                @error('password')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div> --}}

            <div class="flex items-center justify-end mt-4">
                {{-- @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif --}}

                <x-button class="ms-4 bg-vino-900 hover:bg-vino-800 focus:bg-vino-800 active:bg-vino-900">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
