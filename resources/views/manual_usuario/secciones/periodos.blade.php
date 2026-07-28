<div class="prose prose-vino max-w-none">
    <h1 class="text-3xl md:text-4xl font-extrabold text-vino-900 mb-6 tracking-tight">Gestión de Períodos</h1>

    <p class="text-gray-700 text-lg leading-relaxed mb-6">
        Los <strong>Períodos</strong> definen los rangos de tiempo (fechas de inicio y fin) durante los cuales los entes están autorizados para registrar y enviar su documentación en el sistema.
    </p>

    <h2 class="text-2xl font-bold text-vino-800 mt-10 mb-4 border-b border-gray-200 pb-3">Creación y Edición</h2>
    <p class="text-gray-700 mb-4">
        Al crear o editar un período, el administrador deberá proporcionar la siguiente información clave:
    </p>
    
    <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden mb-8">
        <ul class="divide-y divide-gray-100">
            <li class="p-4 hover:bg-gray-50 flex items-start">
                <span class="text-vino-600 mt-1 mr-3 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <div>
                    <strong class="text-gray-900 block mb-1">Mes y Año</strong>
                    <span class="text-sm text-gray-600">Identificador principal del período correspondiente a la entrega requerida.</span>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50 flex items-start">
                <span class="text-vino-600 mt-1 mr-3 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <div>
                    <strong class="text-gray-900 block mb-1">Fechas de Vigencia</strong>
                    <span class="text-sm text-gray-600">Fecha de inicio y cierre. Solo dentro de este rango estricto se podrán subir documentos.</span>
                </div>
            </li>
            <li class="p-4 hover:bg-gray-50 flex items-start">
                <span class="text-vino-600 mt-1 mr-3 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <div>
                    <strong class="text-gray-900 block mb-1">Estado (Activo / Inactivo)</strong>
                    <span class="text-sm text-gray-600">Permite habilitar o deshabilitar temporalmente el período sin tener que cambiar sus fechas o borrarlo.</span>
                </div>
            </li>
        </ul>
    </div>

    <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-r-xl shadow-sm my-8 flex flex-col md:flex-row items-start md:items-center">
        <div class="text-amber-500 mb-3 md:mb-0 md:mr-4 bg-amber-100 p-2 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div>
            <h3 class="text-amber-900 font-bold mt-0 mb-1">Restricciones de Entrega</h3>
            <p class="text-amber-800 text-sm mb-0">Un período debe estar configurado como "Activo" <strong>y</strong> la fecha actual debe estar dentro de sus fechas de vigencia para que los entes puedan realizar y enviar sus entregas.</p>
        </div>
    </div>

    <h2 class="text-2xl font-bold text-vino-800 mt-10 mb-4 border-b border-gray-200 pb-3">Registro de Actividad (Logs)</h2>
    <p class="text-gray-700">
        El sistema cuenta con un historial detallado que guarda de forma automática un registro (log) de qué usuario crea, modifica o elimina un período. Esto garantiza la total trazabilidad y transparencia en la configuración de las ventanas de entrega, registrando valores anteriores y nuevos en caso de modificaciones.
    </p>
</div>
