<?php

namespace App\Livewire\AsignacionRevisores;

use Livewire\Component;
use App\Models\User;
use App\Models\Ente;
use App\Models\EnteRevisor;

class AsignacionRevisores extends Component
{
    public $revisor_id = '';
    public $entes = [];
    public $entesSeleccionados = [];
    public $asignacionesActuales = [];
    public $entesAsignadosOtros = [];
    
    protected $listeners = ['revisorChanged' => 'cargarEntes'];
    
    public function mount()
    {
        $this->entes = collect();
        $this->asignacionesActuales = collect();
        $this->entesAsignadosOtros = collect();
    }
    
    public function updatedRevisorId($value)
    {
        $this->cargarEntes($value);
    }
    
    /**
     * Este método se ejecuta automáticamente cuando cambia $entesSeleccionados
     * Guarda las asignaciones inmediatamente
     */
    public function updatedEntesSeleccionados($value)
    {
        // Si no hay revisor seleccionado, no hacer nada
        if (empty($this->revisor_id)) {
            return;
        }
        
        $this->guardarAsignaciones();
    }
    
    public function cargarEntes($revisorId)
    {
        if (empty($revisorId)) {
            $this->reset(['entes', 'asignacionesActuales', 'entesSeleccionados', 'entesAsignadosOtros']);
            return;
        }
        
        try {
            // Obtener IDs de entes ya asignados a OTROS revisores
            $this->entesAsignadosOtros = EnteRevisor::where('revisor_id', '!=', $revisorId)
                ->with('ente')
                ->with('revisor')
                ->get();
            
            $entesAsignadosOtrosIds = $this->entesAsignadosOtros->pluck('ente_id')->toArray();
            
            // Obtener todos los entes
            $todosLosEntes = Ente::with('tipoEnte')
                ->orderBy('nombre')
                ->get();
            
            // Marcar entes como disponibles o asignados a otros
            $this->entes = $todosLosEntes->map(function($ente) use ($entesAsignadosOtrosIds) {
                $ente->asignado_a_otro = in_array($ente->id, $entesAsignadosOtrosIds);
                return $ente;
            });
            
            // Obtener asignaciones actuales de ESTE revisor
            $this->asignacionesActuales = EnteRevisor::where('revisor_id', $revisorId)
                ->with('ente')
                ->get();
            
            // IDs de entes ya asignados a este revisor
            $this->entesSeleccionados = $this->asignacionesActuales
                ->pluck('ente_id')
                ->map(fn($id) => (string) $id)
                ->toArray();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar los municipios: ' . $e->getMessage());
            $this->reset(['entes', 'asignacionesActuales', 'entesSeleccionados']);
        }
    }
    
    public function seleccionarTodos()
    {
        // Solo seleccionar entes que NO están asignados a otros revisores
        $this->entesSeleccionados = $this->entes
            ->filter(function($ente) {
                return !$ente->asignado_a_otro;
            })
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
        
        // El guardado se hace automáticamente por updatedEntesSeleccionados
    }
    
    public function deseleccionarTodos()
    {
        $this->entesSeleccionados = [];
        // El guardado se hace automáticamente por updatedEntesSeleccionados
    }
    
    /**
     * Método separado para guardar las asignaciones
     */
    protected function guardarAsignaciones()
    {
        try {
            // Verificar que no se estén asignando entes que ya están asignados a otros
            $entesAsignadosOtrosIds = EnteRevisor::where('revisor_id', '!=', $this->revisor_id)
                ->pluck('ente_id')
                ->toArray();
            
            $conflictos = array_intersect($this->entesSeleccionados, $entesAsignadosOtrosIds);
            
            if (!empty($conflictos)) {
                // Si hay conflictos, revertir la selección
                $this->entesSeleccionados = array_diff($this->entesSeleccionados, $conflictos);
                
                $nombresConflictos = Ente::whereIn('id', $conflictos)->pluck('nombre')->implode(', ');
                session()->flash('error', "No se pueden asignar los siguientes municipios porque ya están asignados a otros revisores: {$nombresConflictos}");
                return;
            }
            
            // Eliminar asignaciones actuales
            EnteRevisor::where('revisor_id', $this->revisor_id)->delete();
            
            // Crear nuevas asignaciones
            foreach ($this->entesSeleccionados as $enteId) {
                EnteRevisor::create([
                    'revisor_id' => $this->revisor_id,
                    'ente_id' => $enteId,
                ]);
            }
            
            // Actualizar la lista de asignaciones actuales
            $this->asignacionesActuales = EnteRevisor::where('revisor_id', $this->revisor_id)
                ->with('ente')
                ->get();
            
            // Mensaje de éxito temporal (opcional)
            $this->dispatch('asignacion-guardada');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar las asignaciones: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        // Obtener solo usuarios que son revisores
        $revisores = User::whereHas('roles', function($query) {
                $query->where('name', 'Revisor');
            })
            ->orderBy('name')
            ->get();
        
        return view('livewire.asignacion_revisores.asignacion-revisores', [
            'revisores' => $revisores,
        ]);
    }
}