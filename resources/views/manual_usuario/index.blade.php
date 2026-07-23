<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Usuario - Repositorio Municipal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js para la interactividad del manual -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased" x-data="{ sidebarOpen: false, activeSection: 'introduccion' }">

    <!-- Mobile Header -->
    <div class="sticky top-0 z-40 flex items-center justify-between px-4 py-3 bg-vino-800 text-white md:hidden shadow-md">
        <span class="text-xl font-bold truncate">Manual de Usuario</span>
        <button @click="sidebarOpen = !sidebarOpen" class="focus:outline-none p-1 rounded hover:bg-vino-700 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar (Left Menu) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-72 bg-vino-900 text-white transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 md:flex-shrink-0 flex flex-col shadow-xl">
            
            <div class="flex items-center justify-center h-16 bg-vino-950 border-b border-vino-800 hidden md:flex shrink-0">
                <h1 class="text-lg font-bold truncate px-4">Repositorio Municipal</h1>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 space-y-1">
                <div class="px-6 pb-2 text-xs font-semibold text-vino-300 uppercase tracking-wider">Contenido</div>
                
                <button @click="activeSection = 'introduccion'; sidebarOpen = false; window.scrollTo(0,0);" 
                        :class="{'bg-vino-800 border-l-4 border-vino-300 text-white font-medium': activeSection === 'introduccion', 'text-vino-100': activeSection !== 'introduccion'}" 
                        class="w-full text-left px-6 py-2.5 hover:bg-vino-800 transition-colors flex items-center">
                    <span class="mr-3 text-lg opacity-70">📖</span> Introducción
                </button>
                
                <button @click="activeSection = 'periodos'; sidebarOpen = false; window.scrollTo(0,0);" 
                        :class="{'bg-vino-800 border-l-4 border-vino-300 text-white font-medium': activeSection === 'periodos', 'text-vino-100': activeSection !== 'periodos'}" 
                        class="w-full text-left px-6 py-2.5 hover:bg-vino-800 transition-colors flex items-center">
                    <span class="mr-3 text-lg opacity-70">📅</span> Gestión de Períodos
                </button>
                
                <button @click="activeSection = 'documentos'; sidebarOpen = false; window.scrollTo(0,0);" 
                        :class="{'bg-vino-800 border-l-4 border-vino-300 text-white font-medium': activeSection === 'documentos', 'text-vino-100': activeSection !== 'documentos'}" 
                        class="w-full text-left px-6 py-2.5 hover:bg-vino-800 transition-colors flex items-center">
                    <span class="mr-3 text-lg opacity-70">📁</span> Registro de Documentos
                </button>
                
                <button @click="activeSection = 'revision'; sidebarOpen = false; window.scrollTo(0,0);" 
                        :class="{'bg-vino-800 border-l-4 border-vino-300 text-white font-medium': activeSection === 'revision', 'text-vino-100': activeSection !== 'revision'}" 
                        class="w-full text-left px-6 py-2.5 hover:bg-vino-800 transition-colors flex items-center">
                    <span class="mr-3 text-lg opacity-70">✅</span> Revisión de Documentos
                </button>
                
                <button @click="activeSection = 'avisos'; sidebarOpen = false; window.scrollTo(0,0);" 
                        :class="{'bg-vino-800 border-l-4 border-vino-300 text-white font-medium': activeSection === 'avisos', 'text-vino-100': activeSection !== 'avisos'}" 
                        class="w-full text-left px-6 py-2.5 hover:bg-vino-800 transition-colors flex items-center">
                    <span class="mr-3 text-lg opacity-70">📢</span> Avisos
                </button>
                
                <button @click="activeSection = 'mensajeria'; sidebarOpen = false; window.scrollTo(0,0);" 
                        :class="{'bg-vino-800 border-l-4 border-vino-300 text-white font-medium': activeSection === 'mensajeria', 'text-vino-100': activeSection !== 'mensajeria'}" 
                        class="w-full text-left px-6 py-2.5 hover:bg-vino-800 transition-colors flex items-center">
                    <span class="mr-3 text-lg opacity-70">✉️</span> Mensajería
                </button>
                
                <button @click="activeSection = 'reportes'; sidebarOpen = false; window.scrollTo(0,0);" 
                        :class="{'bg-vino-800 border-l-4 border-vino-300 text-white font-medium': activeSection === 'reportes', 'text-vino-100': activeSection !== 'reportes'}" 
                        class="w-full text-left px-6 py-2.5 hover:bg-vino-800 transition-colors flex items-center">
                    <span class="mr-3 text-lg opacity-70">📊</span> Reportes
                </button>
                
                <!-- <button @click="activeSection = 'roles'; sidebarOpen = false; window.scrollTo(0,0);" 
                        :class="{'bg-vino-800 border-l-4 border-vino-300 text-white font-medium': activeSection === 'roles', 'text-vino-100': activeSection !== 'roles'}" 
                        class="w-full text-left px-6 py-2.5 hover:bg-vino-800 transition-colors flex items-center">
                    <span class="mr-3 text-lg opacity-70">🔐</span> Roles y Permisos
                </button> -->
            </nav>

            <div class="p-4 border-t border-vino-800 text-xs text-vino-300 text-center">
                &copy; {{ date('Y') }} Repositorio Municipal
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             class="fixed inset-0 bg-gray-900 bg-opacity-60 z-40 md:hidden backdrop-blur-sm transition-opacity" 
             x-transition.opacity 
             x-cloak></div>

        <!-- Main Content (Center & Right) -->
        <main class="flex-1 flex overflow-hidden bg-white relative">
            <!-- Center Content -->
            <div class="flex-1 overflow-y-auto p-6 md:p-10 lg:px-16 scroll-smooth" id="main-content">
                
                <!-- Content wrapper -->
                <div class="max-w-4xl mx-auto pb-20">
                    
                    <div x-show="activeSection === 'introduccion'" x-transition.opacity.duration.300ms x-cloak>
                        @include('manual_usuario.secciones.introduccion')
                    </div>
                    
                    <div x-show="activeSection === 'periodos'" x-transition.opacity.duration.300ms x-cloak>
                        @include('manual_usuario.secciones.periodos')
                    </div>

                    <div x-show="activeSection === 'documentos'" x-transition.opacity.duration.300ms x-cloak>
                        @include('manual_usuario.secciones.documentos')
                    </div>

                    <div x-show="activeSection === 'revision'" x-transition.opacity.duration.300ms x-cloak>
                        @include('manual_usuario.secciones.revision')
                    </div>

                    <div x-show="activeSection === 'avisos'" x-transition.opacity.duration.300ms x-cloak>
                        @include('manual_usuario.secciones.avisos')
                    </div>

                    <div x-show="activeSection === 'mensajeria'" x-transition.opacity.duration.300ms x-cloak>
                        @include('manual_usuario.secciones.mensajeria')
                    </div>

                    <div x-show="activeSection === 'reportes'" x-transition.opacity.duration.300ms x-cloak>
                        @include('manual_usuario.secciones.reportes')
                    </div>

                    <div x-show="activeSection === 'roles'" x-transition.opacity.duration.300ms x-cloak>
                        @include('manual_usuario.secciones.roles')
                    </div>

                </div>
            </div>

            <!-- Right Submenu (Info panel) - Visible on lg screens -->
            <aside class="hidden xl:flex w-72 flex-col overflow-y-auto border-l border-gray-100 bg-gray-50 px-6 py-10 shrink-0">
                <h3 class="text-xs font-bold text-vino-600 uppercase tracking-widest mb-4">Información Adicional</h3>
                
                <div class="prose prose-sm text-gray-600 mb-8">
                    <p>
                        Este manual interactivo describe paso a paso el uso de la plataforma. 
                        Navega por las diferentes secciones usando el menú de la izquierda.
                    </p>
                </div>
                
                <div class="mt-auto rounded-xl bg-vino-50 p-5 border border-vino-100 shadow-sm">
                    <h4 class="font-bold text-vino-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-vino-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        ¿Necesitas Ayuda?
                    </h4>
                    <p class="text-sm text-vino-800 leading-relaxed mb-4">
                        Si experimentas problemas técnicos o tienes dudas sobre tus permisos, contacta al administrador del sistema o al departamento de soporte.
                    </p>
                    <a href="{{ route('dashboard') }}" class="inline-flex w-full justify-center items-center px-4 py-2 bg-vino-700 hover:bg-vino-800 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
                        Volver al Sistema
                    </a>
                </div>
            </aside>
        </main>
    </div>
</body>
</html>
