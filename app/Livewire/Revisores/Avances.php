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

        // Obtener TODOS los usuarios que tienen el rol "Revisor"
        return User::whereHas('roles', function($query) {
                $query->where('name', 'Revisor');
            })
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
            return [
                'total_esperados' => 0, 
                'total_recibidos' => 0, 
                'completados' => 0, 
                'porcentaje_recibidos' => 0, 
                'porcentaje_completados' => 0,
                'porcentaje_general' => 0
            ];
        }

        // Obtener progreso global sumando todos los entes
        $progresoPorEnte = $this->progresoPorEnte;
        
        $totalEsperados = $progresoPorEnte->sum('total_esperados');
        $totalRecibidos = $progresoPorEnte->sum('total_recibidos');
        $archivosCompletados = $progresoPorEnte->sum('completados');
        
        $porcentajeRecibidos = $totalEsperados > 0 ? round(($totalRecibidos / $totalEsperados) * 100, 2) : 0;
        $porcentajeCompletados = $totalRecibidos > 0 ? round(($archivosCompletados / $totalRecibidos) * 100, 2) : 0;
        $porcentajeGeneral = $totalEsperados > 0 ? round(($archivosCompletados / $totalEsperados) * 100, 2) : 0;

        return [
            'total_esperados' => $totalEsperados,
            'total_recibidos' => $totalRecibidos,
            'completados' => $archivosCompletados,
            'porcentaje_recibidos' => $porcentajeRecibidos,
            'porcentaje_completados' => $porcentajeCompletados,
            'porcentaje_general' => $porcentajeGeneral,
            // Mantener compatibilidad con código existente
            'total' => $totalEsperados,
            'porcentaje' => $porcentajeGeneral
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

            // Total de documentos que se DEBERÍAN recibir (documentos_recibidos)
            $totalEsperados = DocumentosRecibido::where('periodo_id', $this->periodosSeleccionados)
                ->where('ente_id', $ente->id)
                ->count();

            // Total de documentos REALMENTE recibidos (archivo_documento_recibidos)
            $totalRecibidos = ArchivoDocumentoRecibido::whereHas('documentoRecibido', function ($query) use ($ente) {
                    $query->where('periodo_id', $this->periodosSeleccionados)
                        ->where('ente_id', $ente->id);
                })
                ->count();

            // Archivos completados/revisados (estado_id = 2, 3)
            $archivosCompletados = ArchivoDocumentoRecibido::whereIn('estado_id', [2, 3])
                ->whereHas('documentoRecibido', function ($query) use ($ente) {
                    $query->where('periodo_id', $this->periodosSeleccionados)
                        ->where('ente_id', $ente->id);
                })
                ->count();

            // Calcular porcentajes
            $porcentajeRecibidos = $totalEsperados > 0 ? round(($totalRecibidos / $totalEsperados) * 100, 2) : 0;
            $porcentajeCompletados = $totalRecibidos > 0 ? round(($archivosCompletados / $totalRecibidos) * 100, 2) : 0;
            $porcentajeGeneral = $totalEsperados > 0 ? round(($archivosCompletados / $totalEsperados) * 100, 2) : 0;

            $progresoEntes->push([
                'ente_id' => $ente->id,
                'ente_nombre' => $ente->nombre,
                'total_esperados' => $totalEsperados,
                'total_recibidos' => $totalRecibidos,
                'completados' => $archivosCompletados,
                'porcentaje_recibidos' => $porcentajeRecibidos,
                'porcentaje_completados' => $porcentajeCompletados,
                'porcentaje_general' => $porcentajeGeneral,
                // Mantener compatibilidad con el código existente
                'total' => $totalEsperados,
                'porcentaje' => $porcentajeGeneral
            ]);
        }

        return $progresoEntes->sortBy('ente_nombre')->values();
    }

    public function render()
    {
        return view('livewire.revisores.avances');
    }
}