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
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

class Registro extends Component
{
    public $periodosSeleccionados = '';
    public $categoriaSeleccionada = '';
    public $subcategoriaSeleccionada = '';

    // Propiedades para el modal
    public $mostrarModal = false;
    public $documentoSeleccionado = null;
    public $tipoSubida = ''; // 'PDF' o 'XLSX'
    public $archivo = null;
    public $descripcion = '';

    #[Computed]
    public function periodos()
    {

        $periodosSeleccionados = Periodo::orderBy('id', 'desc')->get();
        return $periodosSeleccionados;
    }

    #[Computed]
    public function categorias()
    {
        $rolesUsuario = auth()->user()->roles->pluck('name')->toArray();

        $categorias = CategoriasDocumento::where(function ($query) use ($rolesUsuario) {
            foreach ($rolesUsuario as $rol) {
                $query->orWhereRaw("FIND_IN_SET(?, roles_permitidos)", [$rol]);
            }
        })->get();

        return $categorias;
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
    public function documentos()
    {
        if (!$this->subcategoriaSeleccionada) {
            return collect();
        }

        return Documento::where('subcategoria_id', $this->subcategoriaSeleccionada)
            ->orderBy('clave')
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

        // Obtener el ente del usuario autenticado
        $enteId = auth()->user()->ente_id;

        if (!$enteId) {
            $this->dispatch('notificacion', 'El usuario no tiene un ente asociado', 'error');
            return;
        }

        // Obtener los roles del usuario
        $rolesUsuario = auth()->user()->roles->pluck('name')->toArray();

        // Obtener todas las categorías permitidas para el rol
        $categoriasPermitidas = CategoriasDocumento::where(function ($query) use ($rolesUsuario) {
            foreach ($rolesUsuario as $rol) {
                $query->orWhereRaw("FIND_IN_SET(?, roles_permitidos)", [$rol]);
            }
        })->pluck('id');

        if ($categoriasPermitidas->isEmpty()) {
            return;
        }

        // Obtener todas las subcategorías de esas categorías
        $subcategorias = SubcategoriasDocumento::whereIn('categoria_id', $categoriasPermitidas)->pluck('id');

        if ($subcategorias->isEmpty()) {
            return;
        }

        // Obtener todos los documentos de esas subcategorías
        $documentos = Documento::whereIn('subcategoria_id', $subcategorias)->get();

        DB::beginTransaction();

        try {
            foreach ($documentos as $documento) {
                // Verificar si ya existe un registro para este ente, período y documento
                $existe = DocumentosRecibido::where([
                    'ente_id' => $enteId,
                    'periodo_id' => $periodoId,
                    'documentos_id' => $documento->id,
                ])->exists();

                // Si no existe, crear el registro
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

            $this->dispatch('notificacion', 'Registros generados correctamente', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notificacion', 'Error al generar registros: ' . $e->getMessage(), 'error');
        }
    }

    public function abrirModalSubida($documentoId, $tipo)
    {
        $this->documentoSeleccionado = Documento::find($documentoId);
        $this->tipoSubida = $tipo;
        $this->mostrarModal = true;
        $this->archivo = null;
        $this->descripcion = '';
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->documentoSeleccionado = null;
        $this->tipoSubida = '';
        $this->archivo = null;
        $this->descripcion = '';
    }

    public function guardarArchivo()
    {
        $this->validate([
            'archivo' => 'required|file|max:10240', // Máximo 10MB
            'descripcion' => 'nullable|string|max:500',
        ]);

        // Validar tipo de archivo según el botón presionado
        if ($this->tipoSubida === 'PDF') {
            $this->validate([
                'archivo' => 'mimes:pdf',
            ]);
        } elseif ($this->tipoSubida === 'XLSX' || $this->tipoSubida === 'XLS') {
            $this->validate([
                'archivo' => 'mimes:xlsx,xls,csv',
            ]);
        }

        try {
            // Verificar que el usuario tenga un ente asociado
            if (!auth()->user()->ente_id) {
                throw new \Exception('El usuario no tiene un ente asociado');
            }

            // Buscar el registro en documentos_recibidos para este período y documento
            $documentoRecibido = DocumentosRecibido::where([
                'ente_id' => auth()->user()->ente_id,
                'periodo_id' => $this->periodosSeleccionados,
                'documentos_id' => $this->documentoSeleccionado->id,
            ])->first();

            if (!$documentoRecibido) {
                throw new \Exception('No se encontró el registro base del documento');
            }

            // Generar nombre único para el archivo
            $extension = $this->archivo->getClientOriginalExtension();
            $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;

            // Construir ruta: periodo_id/ente_id/documento_id/
            $rutaBase = 'documentos/' . $this->periodosSeleccionados . '/' . auth()->user()->ente_id . '/' . $this->documentoSeleccionado->id;

            // Guardar el archivo
            $rutaCompleta = $this->archivo->storeAs(
                $rutaBase,
                $nombreArchivo,
                'public'
            );

            // Crear registro en archivo_documento_recibidos
            $archivo = ArchivoDocumentoRecibido::create([
                'nombre' => $nombreArchivo,
                'observaciones' => $this->descripcion,
                'documento_recibido_id' => $documentoRecibido->id,
                'ente_id' => auth()->user()->ente_id,
                'user_id' => auth()->id(),
                'tipo_recepcion' => $this->tipoSubida,
                'fecha_cambio_estatus' => null,
                'usuario_revisor' => null,
                'observaciones_revisor' => null,
                'causas_rechazo_id' => null,
            ]);

            // Actualizar el documento_recibido para reflejar que ya tiene un archivo
            $documentoRecibido->update([
                'fecha_cambio_estatus' => now(),
                'estados_id' => 2, // Por ejemplo: "Archivo subido" (ajusta según tu catálogo)
            ]);

            $this->cerrarModal();

            // Disparar evento para actualizar la lista
            $this->dispatch(
                'archivo-subido',
                mensaje: 'Archivo subido correctamente',
                tipo: 'success',
                archivoId: $archivo->id
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'archivo-subido',
                mensaje: 'Error al subir el archivo: ' . $e->getMessage(),
                tipo: 'error'
            );
        }
    }

    public function render()
    {
        return view('livewire.documentos.registro');
    }
}
