<?php

namespace App\Livewire\Revisores;

use Livewire\Component;
use App\Models\Periodo;
use App\Models\User;
use App\Models\DocumentosRecibido;
use App\Models\EnteRevisor;

class Avances extends Component
{
    public $periodosSeleccionados = '';
    public $revisoresSeleccionados = '';

    public function getPeriodosProperty()
    {
        return Periodo::orderBy('id', 'desc')->get();
    }

    public function getRevisoresProperty()
    {
        // Solo obtener revisores si hay un periodo seleccionado
        if (empty($this->periodosSeleccionados)) {
            return collect(); // Retorna colección vacía si no hay periodo seleccionado
        }

        // Obtener los ente_id que tienen documentos recibidos en el periodo seleccionado
        $entesConDocumentos = DocumentosRecibido::where('periodo_id', $this->periodosSeleccionados)
            ->distinct()
            ->pluck('ente_id');

        // Obtener los revisores asignados a esos entes
        $revisoresIds = EnteRevisor::whereIn('ente_id', $entesConDocumentos)
            ->distinct()
            ->pluck('revisor_id');

        // Devolver los usuarios que son revisores
        return User::whereIn('id', $revisoresIds)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.revisores.avances');
    }
}