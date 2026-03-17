<?php
// app/Livewire/Documentos/Revision.php

namespace App\Livewire\Documentos;

use App\Models\CategoriasDocumento;
use App\Models\SubcategoriasDocumento;
use App\Models\DocumentosRecibido;
use App\Models\ArchivoDocumentoRecibido;
use App\Models\Periodo;
use App\Models\CausaRechazo;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

class Revision extends Component
{
    public $periodosSeleccionados = '';
    public $enteSeleccionado = '';
    public $categoriaSeleccionada = '';
    public $subcategoriaSeleccionada = '';

    // Propiedades para la revisión
    public $mostrarPanelRechazo = false;
    public $archivoSeleccionado = null;
    public $causaRechazoId = '';
    public $observacionesRevisor = '';
    public $archivoEnRevision = null;

    public $causasRechazo = [];

    public function mount()
    {
        $this->causasRechazo = CausaRechazo::orderBy('descripcion')->get();
    }

    #[Computed]
    public function periodos()
    {
        return Periodo::orderBy('id', 'desc')->get();
    }

    #[Computed]
    public function entesAsignados()
    {
        // Asegúrate de tener este método en el modelo User
        return auth()->user()->entesAsignados()->orderBy('nombre')->get();
    }

    #[Computed]
    public function categorias()
    {
        return CategoriasDocumento::orderBy('nombre')->get();
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
        if (!$this->periodosSeleccionados || !$this->enteSeleccionado || !$this->subcategoriaSeleccionada) {
            return collect();
        }

        return DocumentosRecibido::with(['documento', 'archivos' => function($query) {
                $query->latest();
            }])
            ->where('ente_id', $this->enteSeleccionado)
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

    public function verArchivo($archivoId)
    {
        $this->archivoEnRevision = ArchivoDocumentoRecibido::with([
            'documentoRecibido.documento',
            'documentoRecibido.periodo',
            'ente'
        ])->find($archivoId);
        
        $this->dispatch('actualizar-visorpdf');
    }

    public function aprobarArchivo($archivoId)
    {
        try {
            $archivo = ArchivoDocumentoRecibido::find($archivoId);
            
            if ($archivo) {
                $archivo->update([
                    'usuario_revisor' => auth()->id(),
                    'observaciones_revisor' => null,
                    'causas_rechazo_id' => null,
                    'fecha_cambio_estatus' => now(),
                ]);

                $this->dispatch('notificacion', 'Archivo aprobado correctamente', 'success');
                $this->archivoEnRevision = null;
                $this->reset(['mostrarPanelRechazo', 'archivoSeleccionado', 'causaRechazoId', 'observacionesRevisor']);
            }
        } catch (\Exception $e) {
            $this->dispatch('notificacion', 'Error al aprobar el archivo', 'error');
        }
    }

    public function mostrarPanelRechazo($archivoId)
    {
        $this->archivoSeleccionado = ArchivoDocumentoRecibido::find($archivoId);
        $this->mostrarPanelRechazo = true;
        $this->causaRechazoId = '';
        $this->observacionesRevisor = '';
    }

    public function rechazarArchivo()
    {
        $this->validate([
            'causaRechazoId' => 'required|exists:causas_rechazo,id',
            'observacionesRevisor' => 'nullable|string|max:500',
        ]);

        try {
            if ($this->archivoSeleccionado) {
                $this->archivoSeleccionado->update([
                    'usuario_revisor' => auth()->id(),
                    'observaciones_revisor' => $this->observacionesRevisor,
                    'causas_rechazo_id' => $this->causaRechazoId,
                    'fecha_cambio_estatus' => now(),
                ]);

                $this->dispatch('notificacion', 'Archivo rechazado', 'warning');
                $this->reset(['mostrarPanelRechazo', 'archivoSeleccionado', 'causaRechazoId', 'observacionesRevisor']);
                $this->archivoEnRevision = null;
            }
        } catch (\Exception $e) {
            $this->dispatch('notificacion', 'Error al rechazar el archivo', 'error');
        }
    }

    public function cancelarRechazo()
    {
        $this->reset(['mostrarPanelRechazo', 'archivoSeleccionado', 'causaRechazoId', 'observacionesRevisor']);
    }

    public function render()
    {
        return view('livewire.documentos.revision');
    }
}