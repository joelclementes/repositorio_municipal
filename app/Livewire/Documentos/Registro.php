<?php
namespace App\Livewire\Documentos;

use App\Models\CategoriasDocumento;
use App\Models\SubcategoriasDocumento;
use App\Models\DocumentosRecibido;
use App\Models\ArchivoDocumentoRecibido;
use App\Models\Periodo;
use App\Models\PeriodoEnte;
use App\Models\Documento;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use App\Models\Estado;
use App\Services\ReglasDocumentoService;

class Registro extends Component
{
    use WithFileUploads;

    public $periodosSeleccionados = '';
    public $categoriaSeleccionada = '';
    public $subcategoriaSeleccionada = '';

    // Propiedades para el modal
    public $mostrarModal = false;
    public $documentoSeleccionado = null;
    public $documentoRecibidoSeleccionado = null;
    public $tipoSubida = ''; // 'PDF' o 'XLSX'
    public $archivo = null;
    public $descripcion = '';

    #[Computed]
    public function periodos()
    {
        $enteId = auth()->user()?->ente_id;

        if (!$enteId) {
            return collect();
        }

        return Periodo::whereHas('periodosEntes', function ($query) use ($enteId) {
            $query->where('ente_id', $enteId)
                ->where('is_active', true);
        })
            ->orderBy('id', 'desc')
            ->get();
    }

    #[Computed]
    public function categorias()
    {
        $rolesUsuario = auth()->user()->roles->pluck('name')->toArray();

        return CategoriasDocumento::where(function ($query) use ($rolesUsuario) {
            foreach ($rolesUsuario as $rol) {
                $query->orWhereRaw("FIND_IN_SET(?, roles_permitidos)", [$rol]);
            }
        })->get();
    }

    #[Computed]
    public function subcategorias()
    {
        if (!$this->categoriaSeleccionada) {
            return collect();
        }

        return SubcategoriasDocumento::where('categoria_id', $this->categoriaSeleccionada)->get();
    }

    #[Computed]
    public function documentosRecibidos()
    {
        if (!$this->periodosSeleccionados || !$this->subcategoriaSeleccionada) {
            return collect();
        }

        $enteId = auth()->user()->ente_id;

        if (!$enteId) {
            return collect();
        }

        return DocumentosRecibido::with(['documento', 'archivos'])
            ->where('ente_id', $enteId)
            ->where('periodo_id', $this->periodosSeleccionados)
            ->whereHas('documento', function ($query) {
                $query->where('subcategoria_id', $this->subcategoriaSeleccionada);
            })
            ->orderBy('created_at')
            ->get();
    }

    #[Computed]
    public function tieneEntregasEnPeriodo()
    {
        if (!$this->periodosSeleccionados) {
            return false;
        }

        $enteId = auth()->user()->ente_id;
        if (!$enteId) {
            return false;
        }

        return DocumentosRecibido::where('ente_id', $enteId)
            ->where('periodo_id', $this->periodosSeleccionados)
            ->has('archivos')
            ->exists();
    }

    public function updatedCategoriaSeleccionada()
    {
        $this->subcategoriaSeleccionada = '';
    }

    /**
     * Cuando se selecciona un período, generar los registros en documentos_recibidos
     */
    public function updatedPeriodosSeleccionados($periodoId)
    {
        // Si el usuario deselecciona el periodo (selecciona la opción vacía), limpiamos la sesión
        if (!$periodoId) {
            session()->forget('periodo_acuse');
            return;
        }

        // 👇 LA MAGIA OCURRE AQUÍ: Guardamos el ID en la sesión silenciosamente
        session(['periodo_acuse' => $periodoId]);

        $enteId = auth()->user()->ente_id;

        if (!$enteId) {
            $this->dispatch('notificacion', 'El usuario no tiene un ente asociado', 'error');
            return;
        }



        // Validación al seleccionar período para cargar el Periodo y detener el flujo si no existe.
        $periodo = Periodo::find($periodoId);

        if (!$periodo) {
            $this->dispatch('notificacion', 'No se encontró el período seleccionado', 'error');
            return;
        }
        // Fin validación


        $periodoEnte = PeriodoEnte::where('ente_id', $enteId)
            ->where('periodo_id', $periodoId)
            ->where('is_active', true)
            ->first();

        if (!$periodoEnte) {
            $this->dispatch('notificacion', 'El período seleccionado no está habilitado para el ente asociado al usuario', 'error');
            return;
        }

        $reglasDocumentoService = app(ReglasDocumentoService::class);
        $rolesUsuario = auth()->user()->roles->pluck('name')->toArray();

        $categoriasPermitidas = CategoriasDocumento::where(function ($query) use ($rolesUsuario) {
            foreach ($rolesUsuario as $rol) {
                $query->orWhereRaw("FIND_IN_SET(?, roles_permitidos)", [$rol]);
            }
        })->pluck('id');

        if ($categoriasPermitidas->isEmpty()) {
            return;
        }

        $subcategorias = SubcategoriasDocumento::whereIn('categoria_id', $categoriasPermitidas)->pluck('id');

        if ($subcategorias->isEmpty()) {
            return;
        }

        $documentos = Documento::whereIn('subcategoria_id', $subcategorias)->get();

        DB::beginTransaction();

        try {
            foreach ($documentos as $documento) {
                $existe = DocumentosRecibido::where([
                    'ente_id' => $enteId,
                    'periodo_id' => $periodoId,
                    'documento_id' => $documento->id,
                ])->exists();

                // Si no cumple la regla, no se agrega el registro.
                if (!$reglasDocumentoService->debeRegistrarDocumentoEnPeriodo($documento, $periodo)) {
                    continue;
                }

                if (!$existe) {
                    DocumentosRecibido::create([
                        'ente_id' => $enteId,
                        'user_id' => auth()->id(),
                        'documento_id' => $documento->id,
                        'periodo_id' => $periodoId,
                    ]);
                }
            }

            DB::commit();
            // $this->dispatch('notificacion', 'Registros generados correctamente', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notificacion', 'Error al generar registros: ' . $e->getMessage(), 'error');
        }
    }

    public function abrirModalSubida($documentoRecibidoId, $tipo)
    {
        $this->archivo = null;
        $this->descripcion = '';

        $documentoRecibido = DocumentosRecibido::with('documento')->find($documentoRecibidoId);

        if (!$documentoRecibido) {
            $this->dispatch('notificacion', 'No se encontró el registro del documento.', 'error');
            return;
        }

        $periodoEnte = $this->obtenerPeriodoEnteSeleccionado();
        $vigencia = $this->evaluarVigenciaPeriodoEnte($periodoEnte);

        if (!$vigencia['habilitado']) {
            $this->dispatch('notificacion', $vigencia['mensaje'], 'error');
            return;
        }

        $this->documentoRecibidoSeleccionado = $documentoRecibido;
        $this->documentoSeleccionado = $documentoRecibido->documento;
        $this->tipoSubida = $tipo;
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->documentoRecibidoSeleccionado = null;
        $this->documentoSeleccionado = null;
        $this->tipoSubida = '';
        $this->archivo = null;
        $this->descripcion = '';

        $this->reset(['mostrarModal', 'documentoRecibidoSeleccionado', 'documentoSeleccionado', 'tipoSubida', 'descripcion']);
        $this->archivo = null;
    }

    public function guardarArchivo()
    {
        $this->validate([
            'archivo' => 'required|file|max:10240',
            'descripcion' => 'nullable|string|max:500',
        ]);

        if ($this->tipoSubida === 'PDF') {
            $this->validate(['archivo' => 'mimes:pdf']);
        } elseif ($this->tipoSubida === 'XLSX' || $this->tipoSubida === 'XLS') {
            $this->validate(['archivo' => 'mimes:xlsx,xls,csv']);
        }

        try {
            if (!auth()->user()->ente_id) {
                throw new \Exception('El usuario no tiene un ente asociado.');
            }

            if (!$this->documentoRecibidoSeleccionado) {
                throw new \Exception('No se encontró el registro base del documento.');
            }

            $ente = auth()->user()->ente;
            $documento = $this->documentoSeleccionado;

            if (!$ente || !$documento) {
                throw new \Exception('No se pudieron obtener los datos necesarios.');
            }

            $periodoEnte = $this->obtenerPeriodoEnteSeleccionado();
            $vigencia = $this->evaluarVigenciaPeriodoEnte($periodoEnte);

            if (!$vigencia['habilitado']) {
                throw new \Exception($vigencia['mensaje']);
            }

            $periodo = $periodoEnte->periodo;

            if (!$periodo) {
                throw new \Exception('No se pudo obtener el periodo relacionado.');
            }

            $nombreEnte = substr($ente->nombre, 0, 10);
            $nombreEnte = preg_replace('/[^a-zA-Z0-9]/', '', $nombreEnte);

            $claveDocumento = $documento->clave;

            $anio = $periodo->axo;
            $mes = str_pad($periodo->mes, 2, '0', STR_PAD_LEFT);

            $fechaSistema = now()->format('Ymd_His');

            $extension = $this->archivo->getClientOriginalExtension();

            $nombreArchivo = sprintf(
                '%s_%s_%s_%s_%s.%s',
                $nombreEnte,
                $claveDocumento,
                $anio,
                $mes,
                $fechaSistema,
                $extension
            );

            $nombreArchivo = preg_replace('/[^a-zA-Z0-9_.-]/', '', $nombreArchivo);

            $partes = explode('_', $nombreArchivo);
            $nombreBase = implode('_', array_slice($partes, 0, 4));

            $archivoExistente = ArchivoDocumentoRecibido::where('nombre', 'like', $nombreBase . '_%')
                ->where('tipo_recepcion', $this->tipoSubida)
                ->where('documento_recibido_id', $this->documentoRecibidoSeleccionado->id)
                ->first();

            if ($archivoExistente && $archivoExistente->autorizado_reenviar == 1) {
                $archivoExistente->update([
                    'autorizado_reenviar' => 2,
                ]);
            }

            $rutaBase = 'documentos/' . $anio . '/' . $nombreEnte . '/' . $mes;

            $this->archivo->storeAs($rutaBase, $nombreArchivo, 'public');

            $estadoRecibidoId = Estado::where('nombre', 'Recibido')->value('id');

            if (!$estadoRecibidoId) {
                throw new \Exception('No se encontró el estado "Recibido".');
            }

            ArchivoDocumentoRecibido::create([
                'nombre' => $nombreArchivo,
                'observaciones_ente' => $this->descripcion,
                'documento_recibido_id' => $this->documentoRecibidoSeleccionado->id,
                'ente_id' => auth()->user()->ente_id,
                'user_id' => auth()->id(),
                'tipo_recepcion' => $this->tipoSubida,
                'fecha_cambio_estatus' => null,
                'usuario_revisor' => null,
                'estado_id' => $estadoRecibidoId,
                'observaciones_revisor' => null,
                'causas_rechazo_id' => null,
            ]);

            $this->reset([
                'mostrarModal',
                'documentoRecibidoSeleccionado',
                'documentoSeleccionado',
                'tipoSubida',
                'descripcion',
            ]);

            $this->archivo = null;

            $this->dispatch('archivo-subido', 'Archivo subido correctamente.', 'success');

            $this->limpiarFormulario();
        } catch (\Exception $e) {
            $this->dispatch('archivo-subido', 'Error al subir el archivo: ' . $e->getMessage(), 'error');
        }
    }

    // En app/Livewire/Documentos/Registro.php

    /**
     * Hook que se ejecuta antes de cada actualización de propiedad
     */
    public function updating($property, $value)
    {
        if ($property === 'mostrarModal' && $value === false) {
            $this->limpiarFormulario();
        }
    }

    /**
     * Limpiar completamente el formulario
     */
    private function limpiarFormulario()
    {
        $this->reset([
            'archivo',
            'descripcion',
            'documentoRecibidoSeleccionado',
            'documentoSeleccionado',
            'tipoSubida'
        ]);
    }

    public function estadoSubidaPorRegla(DocumentosRecibido $documentoRecibido, string $tipoRecepcion): array
    {
        $periodoEnte = $this->obtenerPeriodoEnteSeleccionado();
        $vigencia = $this->evaluarVigenciaPeriodoEnte($periodoEnte);

        if (!$vigencia['habilitado']) {
            return [
                'habilitado' => false,
                'ya_subido' => false,
                'leyenda' => $vigencia['mensaje'],
            ];
        }

        $periodo = $periodoEnte->periodo;

        if (!$periodo || !$documentoRecibido->documento || !auth()->user()?->ente_id) {
            return [
                'habilitado' => false,
                'ya_subido' => false,
                'leyenda' => 'No se pudo validar la vigencia del periodo.',
            ];
        }

        $reglas = app(ReglasDocumentoService::class);

        return $reglas->evaluarBloqueoPorReglaYSubidaPrevia(
            $documentoRecibido->documento,
            $periodo,
            (int) auth()->user()->ente_id,
            $tipoRecepcion
        );
    }

    public function render()
    {
        return view('livewire.documentos.registro');
    }

private function obtenerPeriodoEnteSeleccionado(): ?PeriodoEnte
{
    $enteId = auth()->user()?->ente_id;

    if (!$enteId || !$this->periodosSeleccionados) {
        return null;
    }

    return PeriodoEnte::with('periodo')
        ->where('ente_id', $enteId)
        ->where('periodo_id', $this->periodosSeleccionados)
        ->where('is_active', true)
        ->first();
}

private function evaluarVigenciaPeriodoEnte(?PeriodoEnte $periodoEnte): array
{
    if (!$periodoEnte) {
        return [
            'habilitado' => false,
            'mensaje' => 'El periodo seleccionado no está habilitado para este ente.',
        ];
    }

    if (!$periodoEnte->fecha_inicio || !$periodoEnte->fecha_fin) {
        return [
            'habilitado' => false,
            'mensaje' => 'El periodo no tiene fechas configuradas para este ente.',
        ];
    }

    $hoy = now()->startOfDay();
    $fechaInicio = $periodoEnte->fecha_inicio->copy()->startOfDay();
    $fechaFin = $periodoEnte->fecha_fin->copy()->startOfDay();

    if ($hoy->lt($fechaInicio)) {
        return [
            'habilitado' => false,
            'mensaje' => 'El periodo todavía no inicia. Podrá subir archivos a partir del ' . $fechaInicio->format('d/m/Y') . '.',
        ];
    }

    if ($hoy->gt($fechaFin)) {
        return [
            'habilitado' => false,
            'mensaje' => 'El periodo ya cerró. La fecha límite fue ' . $fechaFin->format('d/m/Y') . '. No se permiten entregas extemporáneas.',
        ];
    }

    return [
        'habilitado' => true,
        'mensaje' => null,
    ];
}
}
