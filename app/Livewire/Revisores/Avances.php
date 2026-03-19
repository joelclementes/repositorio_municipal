<?php

namespace App\Livewire\Revisores;

use Livewire\Component;
use App\Models\Periodo;
use App\Models\User;
use App\Models\DocumentosRecibido;
use App\Models\EnteRevisor;
use App\Models\ArchivoDocumentoRecibido;
use App\Models\Ente;

class Avances extends Component
{
    public $periodosSeleccionados = '';
    public $revisoresSeleccionados = '';
    public $revisorSeleccionado = null;

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

    public function seleccionarRevisor($revisorId)
    {
        $this->revisorSeleccionado = $revisorId;
    }

    public function getProgresoRevisorProperty()
    {
        if (!$this->revisorSeleccionado || !$this->periodosSeleccionados) {
            return ['total' => 0, 'completados' => 0, 'porcentaje' => 0];
        }

        // Obtener progreso global sumando todos los entes
        $progresoPorEnte = $this->progresoPorEnte;
        
        $totalDocumentos = $progresoPorEnte->sum('total');
        $archivosCompletados = $progresoPorEnte->sum('completados');
        $porcentaje = $totalDocumentos > 0 ? round(($archivosCompletados / $totalDocumentos) * 100, 2) : 0;

        return [
            'total' => $totalDocumentos,
            'completados' => $archivosCompletados,
            'porcentaje' => $porcentaje
        ];
    }

    public function getProgresoPorEnteProperty()
    {
        if (!$this->revisorSeleccionado || !$this->periodosSeleccionados) {
            return collect();
        }

        // Obtener los entes asignados al revisor con información completa
        $entesDelRevisor = EnteRevisor::where('revisor_id', $this->revisorSeleccionado)
            ->with('ente')
            ->get();

        $progresoEntes = collect();

        foreach ($entesDelRevisor as $enteRevisor) {
            $ente = $enteRevisor->ente;
            
            if (!$ente) continue;

            // Total de documentos recibidos para este ente en el período
            $totalDocumentos = DocumentosRecibido::where('periodo_id', $this->periodosSeleccionados)
                ->where('ente_id', $ente->id)
                ->count();

            // Archivos completados (estado_id = 2, 3) para este ente
            $archivosCompletados = ArchivoDocumentoRecibido::whereIn('estado_id', [2, 3])
                ->whereHas('documentoRecibido', function ($query) use ($ente) {
                    $query->where('periodo_id', $this->periodosSeleccionados)
                        ->where('ente_id', $ente->id);
                })
                ->count();

            $porcentaje = $totalDocumentos > 0 ? round(($archivosCompletados / $totalDocumentos) * 100, 2) : 0;

            $progresoEntes->push([
                'ente_id' => $ente->id,
                'ente_nombre' => $ente->nombre,
                'total' => $totalDocumentos,
                'completados' => $archivosCompletados,
                'porcentaje' => $porcentaje
            ]);
        }

        return $progresoEntes->sortBy('ente_nombre')->values();
    }

    public function render()
    {
        return view('livewire.revisores.avances');
    }
}