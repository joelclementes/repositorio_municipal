<?php
// app/Livewire/Documentos/Registro.php

namespace App\Livewire\Documentos;

use App\Models\CategoriasDocumento;
use App\Models\SubcategoriasDocumento;
use App\Models\DocumentosRecibido;
use App\Models\ArchivoDocumentoRecibido;
use App\Models\Periodo;
use App\Models\Documento;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

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
        return Periodo::orderBy('id', 'desc')->get();
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

    public function updatedCategoriaSeleccionada()
    {
        $this->subcategoriaSeleccionada = '';
    }

    /**
     * Cuando se selecciona un período, generar los registros en documentos_recibidos
     */
    public function updatedPeriodosSeleccionados($periodoId)
    {
        if (!$periodoId) {
            return;
        }

        $enteId = auth()->user()->ente_id;

        if (!$enteId) {
            $this->dispatch('notificacion', 'El usuario no tiene un ente asociado', 'error');
            return;
        }

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
                    'documentos_id' => $documento->id,
                ])->exists();

                if (!$existe) {
                    DocumentosRecibido::create([
                        'ente_id' => $enteId,
                        'user_id' => auth()->id(),
                        'documentos_id' => $documento->id,
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
        // Asegurar que no hay datos previos
        $this->archivo = null;
        $this->descripcion = '';

        $documentoRecibido = DocumentosRecibido::with('documento')->find($documentoRecibidoId);

        if ($documentoRecibido) {
            $this->documentoRecibidoSeleccionado = $documentoRecibido;
            $this->documentoSeleccionado = $documentoRecibido->documento;
            $this->tipoSubida = $tipo;
            $this->mostrarModal = true;
        }
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
                throw new \Exception('El usuario no tiene un ente asociado');
            }

            if (!$this->documentoRecibidoSeleccionado) {
                throw new \Exception('No se encontró el registro base del documento');
            }

            // Obtener datos necesarios para el nombre del archivo
            $ente = auth()->user()->ente;
            $documento = $this->documentoSeleccionado;
            $periodo = Periodo::find($this->periodosSeleccionados);

            if (!$ente || !$documento || !$periodo) {
                throw new \Exception('No se pudieron obtener los datos necesarios');
            }

            // Extraer los 10 primeros caracteres del nombre del ente
            $nombreEnte = substr($ente->nombre, 0, 10);
            $nombreEnte = preg_replace('/[^a-zA-Z0-9]/', '', $nombreEnte);

            // Obtener clave del documento
            $claveDocumento = $documento->clave;

            // Obtener año y mes del periodo
            $anio = $periodo->axo;
            $mes = str_pad($periodo->mes, 2, '0', STR_PAD_LEFT);

            // Fecha del sistema
            $fechaSistema = now()->format('Ymd_His');

            // Extensión del archivo
            $extension = $this->archivo->getClientOriginalExtension();

            // Construir el nombre del archivo
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


            // Obtenemos el nombre base (hasta el cuarto guión bajo)
            // El formato es: ente_clave_anio_mes_fecha.extension
            $partes = explode('_', $nombreArchivo);
            $nombreBase = implode('_', array_slice($partes, 0, 4));

            // Buscamos archivo existente con el mismo nombre base y tipo_recepcion
            $archivoExistente = ArchivoDocumentoRecibido::where('nombre', 'like', $nombreBase . '_%')
                ->where('tipo_recepcion', $this->tipoSubida)
                ->where('documento_recibido_id', $this->documentoRecibidoSeleccionado->id)
                ->first();

            // Si existe y tiene autorizado_reenviar = 1, actualizar a 0
            if ($archivoExistente && $archivoExistente->autorizado_reenviar == 1) {
                $archivoExistente->update([
                    'autorizado_reenviar' => 0
                ]);
            }

            $rutaBase = 'documentos/' . $anio . '/' . $nombreEnte . '/' . $mes;

            $this->archivo->storeAs($rutaBase, $nombreArchivo, 'public');

            ArchivoDocumentoRecibido::create([
                'nombre' => $nombreArchivo,
                'observaciones_ente' => $this->descripcion,
                'documento_recibido_id' => $this->documentoRecibidoSeleccionado->id,
                'ente_id' => auth()->user()->ente_id,
                'user_id' => auth()->id(),
                'tipo_recepcion' => $this->tipoSubida,
                'fecha_cambio_estatus' => null,
                'usuario_revisor' => null,
                'estado_id' => 1,
                'observaciones_revisor' => null,
                'causas_rechazo_id' => null,
            ]);

            // IMPORTANTE: Resetear todas las propiedades del formulario
            $this->reset([
                'mostrarModal',
                'documentoRecibidoSeleccionado',
                'documentoSeleccionado',
                'tipoSubida',
                'descripcion'
            ]);
            $this->archivo = null; // Limpiar el archivo

            $this->dispatch('archivo-subido', 'Archivo subido correctamente', 'success');

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

    public function render()
    {
        return view('livewire.documentos.registro');
    }
}
