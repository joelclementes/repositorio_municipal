<div class="p-6">
    @php
        $puedeVerColumnaLeido = auth()->user()?->hasAnyRole(['Administrador', 'SuperUsuario']) ?? false;
    @endphp

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <button wire:click="abrirRedactar" class="rounded bg-vino-900 px-4 py-2 text-white hover:bg-vino-800">
            Redactar
        </button>

        <input type="text" wire:model.live.debounce.300ms="buscar" placeholder="Buscar por asunto o cuerpo..."
            class="w-full rounded border-gray-300 md:w-80">

        <select wire:model.live="perPage" class="rounded border-gray-300">
            <option value="10">10</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>

    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-4">
        <select wire:model.live="tipo" class="rounded border-gray-300">
            <option value="recibidos">Recibidos</option>
            <option value="enviados">Enviados</option>
        </select>

        <select wire:model.live="estado" class="rounded border-gray-300">
            <option value="todos">Todos</option>
            <option value="leidos">Leídos</option>
            <option value="no_leidos">No leídos</option>
        </select>

        <input type="date" wire:model.live="fechaInicio" class="rounded border-gray-300">
        <input type="date" wire:model.live="fechaFin" class="rounded border-gray-300">
    </div>

    <div class="overflow-x-auto rounded bg-white shadow">
        <table class="min-w-full border-0">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Quién envía</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Asunto</th>
                    @if ($puedeVerColumnaLeido)
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Destinatario(s)</th>
                    @endif
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Cuerpo</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-500">Adj.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Enviado</th>
                    @if ($puedeVerColumnaLeido)
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Leído</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @forelse ($mensajes as $mensaje)
                    @php
                        $destinatarioActual = $mensaje->destinatarios->firstWhere('destinatario_id', auth()->id());
                        $noLeido = $tipo === 'recibidos' && $destinatarioActual?->estado === 'no_leido';
                        $lecturas = $mensaje->destinatarios
                            ->where('estado', 'leido')
                            ->sortByDesc('leido_at')
                            ->filter(fn ($destinatario) => $destinatario->leido_at);
                    @endphp

                    <tr wire:click="abrirMensaje({{ $mensaje->id }})"
                        class="cursor-pointer {{ $noLeido ? 'bg-white font-bold text-black' : 'bg-gray-100 font-normal text-black' }} hover:bg-gray-200">
                        <td class="px-4 py-3">
                            {{ $mensaje->remitente->name }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $mensaje->asunto }}
                        </td>

                        @if ($puedeVerColumnaLeido)
                            <td class="px-4 py-3">
                                {{ $this->formatearDestinatariosParaBandeja($mensaje) }}
                            </td>
                        @endif

                        <td class="px-4 py-3">
                            {{ \Illuminate\Support\Str::limit($mensaje->cuerpo, 120) }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            @if ($mensaje->archivos->count())
                                📎
                            {{-- @else
                                📭 --}}
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            {{ $mensaje->created_at->format('d/m/y H:i') }}
                        </td>
                        @if ($puedeVerColumnaLeido)
                            <td class="px-4 py-3">
                                @forelse ($lecturas as $lectura)
                                    <div>
                                        {{ ($lectura->destinatario?->name ?? 'Usuario').' - '.$lectura->leido_at->format('d/m/y H:i') }}
                                    </div>
                                @empty
                                    No leído
                                @endforelse
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $puedeVerColumnaLeido ? 7 : 5 }}" class="px-4 py-8 text-center text-gray-500">
                            No hay mensajes para mostrar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $mensajes->links() }}
    </div>

    {{-- Modal redactar --}}
    @if ($modalRedactar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-2xl rounded bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-xl font-bold">Redactar mensaje</h2>

                <div class="space-y-4">
                    <div class="relative">
                        <label class="block text-sm font-medium">Destinatario(s)</label>

                        @if ($this->puedeSeleccionarGrupos)
                            <div class="rounded border border-gray-200 bg-gray-50 p-4">
                                <p class="mb-3 text-sm font-semibold text-gray-700">
                                    Selección rápida de destinatarios
                                </p>

                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                        wire:click="seleccionarGrupoDestinatarios('todos_roles_ente')"
                                        class="rounded-full bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-700">
                                        Todos los roles Ente
                                    </button>

                                    @foreach ($this->rolesEnte as $rol)
                                        <button type="button"
                                            wire:click="seleccionarGrupoDestinatarios('{{ $rol }}')"
                                            class="rounded-full bg-vino-900 px-3 py-1 text-sm text-white hover:bg-vino-800">
                                            {{ $rol }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <input type="text" wire:model.live.debounce.300ms="buscarDestinatario"
                            placeholder="Escribe el nombre del destinatario..."
                            class="mt-1 w-full rounded border-gray-300" autocomplete="off">

                        @if ($this->usuariosFiltrados->isNotEmpty())
                            <div
                                class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto rounded border border-gray-200 bg-white shadow-lg">
                                @foreach ($this->usuariosFiltrados as $usuario)
                                    <button type="button" wire:click="seleccionarDestinatario({{ $usuario->id }})"
                                        class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100">
                                        {{ $usuario->name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        {{-- @if ($this->destinatariosSeleccionadosModel->isNotEmpty())
                            <div class="mt-3 rounded border border-gray-200 bg-gray-50 p-3">
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($this->destinatariosSeleccionadosModel as $usuario)
                                        <span
                                            class="inline-flex items-center gap-2 rounded-full bg-vino-900 px-3 py-1 text-sm text-white">
                                            {{ $usuario->name }}

                                            <button type="button" wire:click="quitarDestinatario({{ $usuario->id }})"
                                                class="font-bold text-white hover:text-gray-200">
                                                ×
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif --}}

                        @if ($this->resumenDestinatarios['total'] > 0)
                            <div class="mt-3 rounded border border-gray-200 bg-gray-50 p-3">
                                <div class="mb-2 text-sm font-semibold text-gray-700">
                                    Destinatarios seleccionados:
                                    {{ $this->resumenDestinatarios['total'] }}
                                </div>

                                @if (count($this->resumenDestinatarios['grupos']))
                                    <div class="mb-3 flex flex-wrap gap-2">
                                        @foreach ($this->resumenDestinatarios['grupos'] as $clave => $nombreGrupo)
                                            <span
                                                class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-900">
                                                {{ $nombreGrupo }}

                                                <button type="button"
                                                    wire:click="quitarGrupoDestinatarios('{{ $clave }}')"
                                                    class="font-bold text-blue-900 hover:text-blue-700">
                                                    ×
                                                </button>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($this->resumenDestinatarios['individuales']->isNotEmpty())
                                    <div class="flex max-h-24 flex-wrap gap-2 overflow-y-auto">
                                        @foreach ($this->resumenDestinatarios['individuales'] as $usuario)
                                            <span
                                                class="inline-flex items-center gap-2 rounded-full bg-vino-900 px-3 py-1 text-sm text-white">
                                                {{ $usuario->name }}

                                                <button type="button"
                                                    wire:click="quitarDestinatario({{ $usuario->id }})"
                                                    class="font-bold text-white hover:text-gray-200">
                                                    ×
                                                </button>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        @error('destinatariosSeleccionados')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Asunto</label>
                        <input type="text" wire:model="asunto" class="mt-1 w-full rounded border-gray-300">
                        @error('asunto')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Cuerpo</label>
                        <textarea wire:model="cuerpo" rows="6" class="mt-1 w-full rounded border-gray-300"></textarea>
                        @error('cuerpo')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Archivo(s)</label>
                        <input type="file" wire:model="archivos" multiple accept=".pdf,.xlsx" class="mt-1 w-full">
                        @error('archivos.*')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="cerrarRedactar" class="rounded bg-gray-200 px-4 py-2">
                        Cerrar
                    </button>

                    <button wire:click="enviar" class="rounded bg-vino-900 px-4 py-2 text-white">
                        Enviar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal leer --}}
    @if ($modalLeer && $this->mensajeSeleccionado)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded bg-white p-6 shadow-lg">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold">{{ $this->mensajeSeleccionado->asunto }}</h2>

                    <button wire:click="cerrarLeer" class="text-2xl leading-none">
                        &times;
                    </button>
                </div>

                <div class="space-y-5">
                    @foreach ($this->hilo as $mensajeHilo)
                        <div class="rounded border border-gray-200 p-4">
                            <div class="mb-2 text-sm text-gray-500">
                                <strong>{{ $mensajeHilo->remitente->name }}</strong>
                                — {{ $mensajeHilo->created_at->format('d/m/Y H:i') }}
                            </div>

                            <div class="whitespace-pre-line text-gray-900">
                                {{ $mensajeHilo->cuerpo }}
                            </div>

                            @if ($mensajeHilo->archivos->count())
                                <div class="mt-3">
                                    <p class="text-sm font-semibold">Adjuntos:</p>

                                    <ul class="list-inside list-disc text-sm">
                                        @foreach ($mensajeHilo->archivos as $archivo)
                                            <li>
                                                <a href="{{ Storage::url($archivo->ruta) }}" target="_blank"
                                                    class="text-vino-900 underline">
                                                    {{ $archivo->nombre_original }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    @unless ($mostrarRespuesta)
                        <button wire:click="mostrarFormularioRespuesta" class="rounded bg-vino-900 px-4 py-2 text-white">
                            Responder
                        </button>
                    @endunless

                    @if ($mostrarRespuesta)
                        <div class="mt-4 space-y-4">
                            <textarea wire:model="respuestaCuerpo" rows="5" class="w-full rounded border-gray-300"
                                placeholder="Escribe tu respuesta..."></textarea>

                            @error('respuestaCuerpo')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <input type="file" wire:model="respuestaArchivos" multiple accept=".pdf,.xlsx">

                            @error('respuestaArchivos.*')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <button wire:click="responder" class="rounded bg-vino-900 px-4 py-2 text-white">
                                Enviar
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
