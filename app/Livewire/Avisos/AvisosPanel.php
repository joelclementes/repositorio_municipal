<?php
// app/Livewire/Avisos/AvisosPanel.php

namespace App\Livewire\Avisos;

use App\Models\Aviso;
use App\Models\AvisoEnte;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class AvisosPanel extends Component
{
    public $avisoSeleccionadoId = null;
    public $filtroEstado = 'todos'; // 'todos', 'leido', 'no_leido'
    public $searchAviso = '';

    #[Computed]
    public function avisos()
    {
        return Aviso::withCount(['avisoEntes as total_entes'])
            ->when($this->searchAviso, function ($query) {
                $query->where(function ($q) {
                    $q->where('titulo', 'like', '%' . $this->searchAviso . '%')
                      ->orWhere('tipo_aviso', 'like', '%' . $this->searchAviso . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function avisoSeleccionado()
    {
        if (!$this->avisoSeleccionadoId) {
            return null;
        }

        return Aviso::with(['avisoEntes.ente'])
            ->find($this->avisoSeleccionadoId);
    }

    #[Computed]
    public function entesFiltrados()
    {
        if (!$this->avisoSeleccionadoId) {
            return collect();
        }

        $query = AvisoEnte::with('ente')
            ->where('aviso_id', $this->avisoSeleccionadoId);

        if ($this->filtroEstado === 'leido') {
            $query->where('estado_envio', 'leido');
        } elseif ($this->filtroEstado === 'no_leido') {
            $query->where('estado_envio', '!=', 'leido');
        }

        return $query->orderBy('created_at')->get();
    }

    public function seleccionarAviso($avisoId)
    {
        $this->avisoSeleccionadoId = $avisoId;
        $this->filtroEstado = 'todos'; // Resetear filtro al cambiar aviso
    }

    public function actualizarEstado($avisoEnteId, $nuevoEstado)
    {
        $avisoEnte = AvisoEnte::find($avisoEnteId);
        
        if ($avisoEnte) {
            $avisoEnte->estado_envio = $nuevoEstado;
            
            if ($nuevoEstado === 'leido') {
                $avisoEnte->fecha_lectura = now();
            }
            
            $avisoEnte->save();
            
            $this->dispatch('estado-actualizado', message: 'Estado actualizado correctamente');
        }
    }

    public function updatedFiltroEstado()
    {
        // Este método se llama automáticamente cuando cambia el filtro
        // Solo necesitamos que se refresque la propiedad computada
    }

    public function render()
    {
        return view('livewire.avisos.avisos-panel');
    }
}