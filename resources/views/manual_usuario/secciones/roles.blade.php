<div class="prose prose-vino max-w-none">
    <h1 class="text-3xl md:text-4xl font-extrabold text-vino-900 mb-6 tracking-tight">Roles y Permisos</h1>

    <p class="text-gray-700 text-lg leading-relaxed mb-6">
        La seguridad y privacidad del sistema están basadas estrictamente en <strong>Roles y Permisos</strong>. Este modelo de arquitectura garantiza que cada usuario pueda ver, editar e interactuar únicamente con la información para la que está autorizado.
    </p>

    <h2 class="text-2xl font-bold text-vino-800 mt-10 mb-6 border-b border-gray-200 pb-3">Roles Comunes del Sistema</h2>
    
    <div class="space-y-6 mb-10">
        
        <!-- Rol Administrador -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-vino-800 px-5 py-3 border-b border-vino-900 flex items-center justify-between">
                <h3 class="text-white font-bold m-0 text-lg">Administrador (Super Usuario)</h3>
                <span class="bg-vino-600 text-white text-xs px-2 py-1 rounded font-semibold">Nivel Alto</span>
            </div>
            <div class="p-5">
                <p class="text-gray-700 mb-3 text-sm">Tiene acceso total e irrestricto al sistema. Es el encargado de la configuración inicial y el mantenimiento operativo de la plataforma.</p>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Permisos Clave:</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Gestión de Períodos</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Creación de Usuarios</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Asignación de Roles</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Emisión de Avisos</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Reportes Globales</span>
                </div>
            </div>
        </div>
        
        <!-- Rol Revisor -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-slate-700 px-5 py-3 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-white font-bold m-0 text-lg">Revisor (Auditor)</h3>
                <span class="bg-slate-500 text-white text-xs px-2 py-1 rounded font-semibold">Nivel Medio</span>
            </div>
            <div class="p-5">
                <p class="text-gray-700 mb-3 text-sm">Personal del congreso o entidad auditora encargado de verificar la información recibida por parte de los entes. Su alcance puede estar limitado a ciertos entes que se le asignen.</p>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Permisos Clave:</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Módulo de Revisión</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Aprobar/Rechazar Documentos</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Mensajería con Entes asignados</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Reportes de Avance</span>
                </div>
            </div>
        </div>
        
        <!-- Rol Ente -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-amber-600 px-5 py-3 border-b border-amber-700 flex items-center justify-between">
                <h3 class="text-white font-bold m-0 text-lg">Ente (Usuario Final)</h3>
                <span class="bg-amber-500 text-white text-xs px-2 py-1 rounded font-semibold">Nivel Básico</span>
            </div>
            <div class="p-5">
                <p class="text-gray-700 mb-3 text-sm">Representante de un municipio o departamento gubernamental obligado a reportar. Solo puede ver su propia información y de su entidad (sandbox).</p>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Permisos Clave:</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Visualizar Períodos Activos</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Subir Documentación</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Consultar Estado de Revisión</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Responder Observaciones</span>
                </div>
            </div>
        </div>
        
    </div>

    <div class="bg-vino-50 border-l-4 border-vino-300 p-4 rounded-r-lg shadow-sm">
        <p class="text-sm text-vino-900 mb-0 font-medium">
            <strong>Nota:</strong> Si usted considera que le hace falta algún permiso para realizar sus actividades o que su rol no es el adecuado, por favor repórtelo al administrador a través del módulo de mensajería interna.
        </p>
    </div>
</div>
