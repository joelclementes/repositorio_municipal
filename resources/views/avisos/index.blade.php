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
                    <livewire:avisos.avisos-panel />
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-4">
                <div class="container mx-auto px-4 py-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Aviso nuevo</h3>
                    <form method="POST" action="{{ route('avisos.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <x-label class="text-red-600" for="titulo" value="Título *" />
                                    <x-input id="titulo"
                                        class="block mt-1 w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 "
                                        type="text" name="titulo" required autofocus autocomplete="titulo" />
                                </div>

                                <div class="mt-4">
                                    <x-label class="text-red-600" for="tipo_visita_id" value="Tipo de aviso *" />
                                    <select id="tipo_visita_id" name="tipo_visita_id"
                                        class="w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 rounded-md shadow-sm"
                                        required>
                                        <option value="" disabled selected>Seleccione un tipo</option>
                                        <option value="Aviso">Aviso</option>
                                        <option value="Invitación">Invitación</option>
                                        <option value="Exhorto">Exhorto</option>
                                        <option value="Convocatoria">Convocatoria</option>
                                        <option value="Circular">Circular</option>
                                    </select>
                                </div>


                                <div class="mt-4">
                                    <x-label class="text-red-600" for="texto" value="Mensaje *" />
                                    <x-input id="texto"
                                        class="block mt-1 w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 "
                                        type="text" name="texto" required autofocus autocomplete="texto" />
                                </div>

                                <div class="mt-4">
                                    <x-label for="url" value="{{ __('Url (opcional)') }}" />
                                    <x-input id="url"
                                        class="block mt-1 w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 "
                                        type="text" name="url" autofocus autocomplete="url" />
                                </div>

                            </div>

                            <div class="space-y-4">
                                <div class="mt-4">
                                    <x-label for="destinatarios" value="Destinatarios" />
                                    <div class="flex gap-6 mt-2">
                                        <!-- Radio button para Administrativa -->
                                        <label class="inline-flex items-center">
                                            <input type="radio" id="destinoTodos" name="destinatarios" value="todos"
                                                class="border-vino-900 text-vino-900 focus:border-vino-900 focus:ring-vino-900"
                                                autofocus />
                                            <span class="ml-2 text-gray-700">Todos</span>
                                        </label>

                                        <!-- Radio button para Diputados -->
                                        <label class="inline-flex items-center">
                                            <input type="radio" id="destinoSelec" name="destinatarios"
                                                value="seleccionados"
                                                class="border-vino-900 text-vino-900 focus:border-vino-900 focus:ring-vino-900" />
                                            <span class="ml-2 text-gray-700">Selección</span>
                                        </label>
                                    </div>
                                </div>

                                <div id="divSeleccion" hidden>
                                    <div class="mt-4">
                                        <div class="flex-1">
                                            <div class="relative">
                                                <x-label class="text-red-600" for="inputEnte" value="Ente *" />
                                                <input type="text" id="inputEnte"
                                                    class="w-full border-vino-900 focus:border-vino-900 focus:ring-vino-900 rounded-md shadow-sm"
                                                    autocomplete="off" placeholder="Escribe para buscar...">
                                                <ul id="listaEntes"
                                                    class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-auto">
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4" id="entesDestinatarios">
                                        <div
                                            class="text-center p-8 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50">
                                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="text-gray-400 text-sm">No hay entes seleccionados</p>
                                            <p class="text-gray-300 text-xs mt-1">Busca y selecciona entes usando el
                                                buscador</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-end mt-4">
                                <x-button
                                    class="ms-4 bg-vino-900 hover:bg-vino-800 focus:bg-vino-800 active:bg-vino-900">
                                    {{ __('Enviar') }}
                                </x-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const destinoTodos = document.getElementById('destinoTodos');
            const destinoSelec = document.getElementById('destinoSelec');
            const divSeleccion = document.getElementById('divSeleccion');
            const entesDestinatarios = document.getElementById('entesDestinatarios');

            let entesSeleccionados = [];

            function toggleSelectors() {
                if (destinoTodos.checked) {
                    divSeleccion.style.display = 'none';
                    entesSeleccionados = [];
                    actualizarListaEntesSeleccionados();
                } else if (destinoSelec.checked) {
                    divSeleccion.style.display = 'block';
                } else {
                    divSeleccion.style.display = 'none';
                }
            }

            function agregarEnteSeleccionado(ente) {
                const existe = entesSeleccionados.some(e => e.id === ente.id);

                if (!existe) {
                    entesSeleccionados.push(ente);
                    actualizarListaEntesSeleccionados();

                    // Mostrar notificación temporal
                    mostrarNotificacion(`"${ente.nombre}" agregado`, 'success');
                } else {
                    mostrarNotificacion('Este ente ya está seleccionado', 'warning');
                }
            }

            function eliminarEnteSeleccionado(enteId) {
                const enteEliminado = entesSeleccionados.find(e => e.id === enteId);
                entesSeleccionados = entesSeleccionados.filter(e => e.id !== enteId);
                actualizarListaEntesSeleccionados();

                if (enteEliminado) {
                    mostrarNotificacion(`"${enteEliminado.nombre}" eliminado`, 'info');
                }
            }

            function eliminarTodosEntes() {
                if (entesSeleccionados.length > 0) {
                    if (confirm('¿Estás seguro de eliminar todos los entes seleccionados?')) {
                        entesSeleccionados = [];
                        actualizarListaEntesSeleccionados();
                        mostrarNotificacion('Todos los entes han sido eliminados', 'info');
                    }
                }
            }

            function mostrarNotificacion(mensaje, tipo = 'success') {
                // Crear elemento de notificación
                const notificacion = document.createElement('div');
                notificacion.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in-down ${
                tipo === 'success' ? 'bg-green-500' : 
                tipo === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            } text-white`;
                notificacion.textContent = mensaje;

                document.body.appendChild(notificacion);

                // Eliminar después de 2 segundos
                setTimeout(() => {
                    notificacion.remove();
                }, 2000);
            }

            function actualizarListaEntesSeleccionados() {
                if (entesSeleccionados.length === 0) {
                    entesDestinatarios.innerHTML = `
                    <div class="text-center p-8 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-gray-400 text-sm">No hay entes seleccionados</p>
                        <p class="text-gray-300 text-xs mt-1">Busca y selecciona entes usando el buscador</p>
                    </div>
                `;
                    return;
                }

                let html = `
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg flex justify-between items-center">
                        <div class="flex items-center">
                            <h4 class="text-sm font-semibold text-gray-700">Entes seleccionados</h4>
                            <span class="ml-2 bg-vino-900 text-white text-xs px-2 py-1 rounded-full">
                                ${entesSeleccionados.length}
                            </span>
                        </div>
                        ${entesSeleccionados.length > 0 ? `
                                            <button type="button" 
                                                    onclick="eliminarTodosEntes()"
                                                    class="text-xs text-red-600 hover:text-red-800 hover:underline transition-colors">
                                                Eliminar todos
                                            </button>
                                        ` : ''}
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <div class="space-y-2">
            `;

                entesSeleccionados.forEach(ente => {
                    html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all group border border-gray-200 hover:border-vino-200" 
                         data-ente-id="${ente.id}">
                        <div class="flex items-center flex-1">
                            <div class="w-8 h-8 bg-vino-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-vino-900 font-semibold text-sm">
                                    ${ente.nombre.charAt(0).toUpperCase()}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-700">${ente.nombre}</span>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" 
                                    onclick="eliminarEnteSeleccionado(${ente.id})"
                                    class="text-gray-400 hover:text-red-600 transition-colors p-1 rounded-full hover:bg-red-50"
                                    title="Eliminar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                });

                html += `
                        </div>
                    </div>
                    <!-- Campos ocultos para enviar al servidor -->
                    <div class="p-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                        <p class="text-xs text-gray-500">
                            ${entesSeleccionados.length} ente(s) seleccionado(s) para enviar el aviso
                        </p>
                        ${entesSeleccionados.map(ente => 
                            `<input type="hidden" name="entes_seleccionados[]" value="${ente.id}">`
                        ).join('')}
                    </div>
                </div>
            `;

                entesDestinatarios.innerHTML = html;
            }

            // Event listeners
            destinoTodos.addEventListener('change', toggleSelectors);
            destinoSelec.addEventListener('change', toggleSelectors);
            toggleSelectors();

            // Búsqueda de entes
            const inputEnte = document.getElementById('inputEnte');
            const listaEntes = document.getElementById('listaEntes');

            let timeoutId;
            inputEnte.addEventListener('input', function() {
                const texto = this.value.trim();
                listaEntes.innerHTML = '';

                if (texto.length < 2) { // Mínimo 2 caracteres
                    listaEntes.style.display = 'none';
                    return;
                }

                // Debounce para no hacer peticiones en cada tecla
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    $.ajax({
                        url: '{{ route('avisos.buscarEnte') }}',
                        data: {
                            q: texto
                        },
                        beforeSend: function() {
                            listaEntes.innerHTML =
                                '<li class="px-4 py-3 text-gray-400">Buscando...</li>';
                            listaEntes.style.display = 'block';
                        },
                        success: function(entesEncontrados) {
                            listaEntes.innerHTML = '';

                            if (entesEncontrados.length === 0) {
                                listaEntes.innerHTML =
                                    '<li class="px-4 py-3 text-gray-400">No se encontraron resultados</li>';
                                listaEntes.style.display = 'block';
                                return;
                            }

                            entesEncontrados.forEach(function(ente) {
                                console.log(ente.tipo_ente_nombre);
                                const li = document.createElement('li');
                                li.className =
                                    'px-4 py-3 cursor-pointer hover:bg-vino-50 hover:text-vino-900 transition-all border-b border-gray-100 last:border-0';
                                li.innerHTML = `
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                        </svg>
                                        <span class="font-medium">${ente.nombre}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">Tipo de ente: ${ente.tipo_ente_nombre}</span>
                                </div>
                            `;

                                li.onclick = () => {
                                    agregarEnteSeleccionado({
                                        id: ente.id,
                                        nombre: ente.nombre
                                    });

                                    inputEnte.value = '';
                                    listaEntes.innerHTML = '';
                                    listaEntes.style.display = 'none';
                                };

                                listaEntes.appendChild(li);
                            });

                            listaEntes.style.display = 'block';
                        },
                        error: function() {
                            listaEntes.innerHTML =
                                '<li class="px-4 py-3 text-red-500">Error en la búsqueda</li>';
                        }
                    });
                }, 300); // Esperar 300ms después de la última tecla
            });

            // Cerrar lista al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!inputEnte.contains(e.target) && !listaEntes.contains(e.target)) {
                    listaEntes.style.display = 'none';
                }
            });

            // Exponer funciones globalmente
            window.eliminarEnteSeleccionado = eliminarEnteSeleccionado;
            window.eliminarTodosEntes = eliminarTodosEntes;
            window.agregarEnteSeleccionado = agregarEnteSeleccionado;
        });
    </script>

    <style>
        @keyframes fade-in-down {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out;
        }
    </style>

</x-app-layout>
