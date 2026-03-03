<div class="container mx-auto px-4 py-8">

    {{-- <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200"> --}}
    <div>
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                <div class="flex-shrink-0">
                    <img src="{{ env('APP_LOGO_LEGISLATURA') }}" class="h-24 w-auto object-contain">
                </div>

                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-[#6c143a] mb-3">
                        Bienvenido al Sistema de Información Financiera y Obras Municipales (SIFOM)
                    </h1>
                    <p class="text-gray-700 text-sm md:text-base leading-relaxed">
                        Esta plataforma permite registrar y administrar de forma organizada las visitas realizadas al
                        interior del Congreso del Estado de Veracruz, con información detallada sobre el visitante, la
                        persona a quien visita, el motivo de la visita. Desde este panel podrás consultar, agregar y dar
                        seguimiento a los registros, contribuyendo al control y seguridad institucional.
                    </p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @can('configurar')
                    <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transform hover:scale-105 transition-transform duration-200">
                        <div class="px-4 py-3" style="background-color:#ffa933 ">
                            <h2 class="text-white font-bold text-lg flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Configurar
                            </h2>
                        </div>
                        <div class="p-4">
                            <ul class="space-y-2">
                                <li class="flex items-center text-gray-700 hover:text-[#ffa933] cursor-pointer">
                                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: #ffa933;"></span>
                                    Gestión de usuarios
                                </li>
                            </ul>
                        </div>
                    </div>
                @endcan

                {{-- @can('administrar')
                <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transform hover:scale-105 transition-transform duration-200">
                    <div class="px-4 py-3" style="background-color: #2C5A8C;">
                        <h2 class="text-white font-bold text-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            Administrar
                        </h2>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-3">Administración de catálogos y supervisión</p>
                        <ul class="space-y-2">
                            <li class="flex items-center text-gray-700 hover:text-[#2C5A8C] cursor-pointer">
                                <span class="w-2 h-2 rounded-full mr-2" style="background-color: #2C5A8C;"></span>
                                Catálogos
                            </li>
                            <li class="flex items-center text-gray-700 hover:text-[#2C5A8C] cursor-pointer">
                                <span class="w-2 h-2 rounded-full mr-2" style="background-color: #2C5A8C;"></span>
                                Asignación de entes
                            </li>
                            <li class="flex items-center text-gray-700 hover:text-[#2C5A8C] cursor-pointer">
                                <span class="w-2 h-2 rounded-full mr-2" style="background-color: #2C5A8C;"></span>
                                Supervisar revisores
                            </li>
                            <li class="flex items-center text-gray-700 hover:text-[#2C5A8C] cursor-pointer">
                                <span class="w-2 h-2 rounded-full mr-2" style="background-color: #2C5A8C;"></span>
                                Crear avisos
                            </li>
                        </ul>
                    </div>
                </div>
                @endcan --}}

                @can('registrar')
                    <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transform hover:scale-105 transition-transform duration-200">
                        <div class="px-4 py-3" style="background-color: #b24280;">
                            <h2 class="text-white font-bold text-lg flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Registrar
                            </h2>
                        </div>
                        <div class="p-4">
                            <ul class="space-y-2">
                                <li class="flex items-center text-gray-700 hover:text-[#b24280] cursor-pointer">
                                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: #b24280;"></span>
                                    <a href="{{ route('documentos.registrar') }}">Nuevo registro</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endcan

                @can('revisar-documentos')
                    <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transform hover:scale-105 transition-transform duration-200">
                        <div class="px-4 py-3" style="background-color: #5eb2c6;">
                            <h2 class="text-white font-bold text-lg flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Revisar Documentos
                            </h2>
                        </div>
                        <div class="p-4">
                            <ul class="space-y-2">
                                <li class="flex items-center text-gray-700 hover:text-[#5eb2c6] cursor-pointer">
                                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: #5eb2c6;"></span>
                                    Revisión de documentos
                                </li>
                            </ul>
                        </div>
                    </div>
                @endcan

                @can('generar-reportes')
                    <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transform hover:scale-105 transition-transform duration-200">
                        <div class="px-4 py-3" style="background-color: #b64747;">
                            <h2 class="text-white font-bold text-lg flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                Generar Reportes
                            </h2>
                        </div>
                        <div class="p-4">
                            <ul class="space-y-2">
                                <li class="flex items-center text-gray-700 hover:text-[#b64747] cursor-pointer">
                                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: #b64747;"></span>
                                    Reportes
                                </li>
                            </ul>
                        </div>
                    </div>
                @endcan
                @can('administrar')
                    <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transform hover:scale-105 transition-transform duration-200">
                        <div class="px-4 py-3" style="background-color: #8974bf;">
                            <h2 class="text-white font-bold text-lg flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 8h2m0 0h2m-2 0V6m0 2v2" />
                                </svg>
                                Generar Notificaciones
                            </h2>
                        </div>
                        <div class="p-4">
                            <ul class="space-y-2">
                                <li class="flex items-center text-gray-700 hover:text-[#b64747] cursor-pointer">
                                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: #8974bf;"></span>
                                    Notificaciones
                                </li>
                            </ul>
                        </div>
                    </div>
                @endcan

            </div>
        </div>
    </div>

    @auth
        @php
            $userPermissions = auth()->user()->getPermissionsViaRoles();
        @endphp
        @if ($userPermissions->isEmpty())
            <div class="text-center py-12 mt-6 bg-white rounded-lg shadow-lg border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Sin permisos</h3>
                <p class="mt-1 text-sm text-gray-500">No tienes permisos asignados actualmente.</p>
            </div>
        @endif
    @endauth

    @guest
        <div class="text-center py-12 bg-white rounded-lg shadow-lg border border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Bienvenido al Sistema</h2>
            <p class="text-gray-600 mb-6">Por favor inicia sesión para acceder a las funcionalidades</p>
            <a href="{{ route('login') }}" class="text-white px-6 py-3 rounded-lg hover:opacity-90 transition-colors"
                style="background-color: #1E4B7A;">
                Iniciar Sesión
            </a>
        </div>
    @endguest
</div>
