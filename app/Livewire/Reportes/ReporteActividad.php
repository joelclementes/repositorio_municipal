<?php

namespace App\Livewire\Reportes;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Ente;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class ReporteActividad extends Component
{
    use WithPagination;

    // --- Filtros ---
    public string $terminoBusqueda = '';
    public array  $tipoUsuario     = []; // ['congreso', 'municipio']
    public array  $entesSeleccionados = []; // IDs de entes
    public array  $estatusUsuarios  = []; // ['activos', 'inactivos']
    public string $fechaDesde      = '';
    public string $fechaHasta      = '';
    public string $filtroEnte       = ''; // Buscador interno para el checklist de municipios

    // Estado del panel de filtros
    public bool $mostrarFiltros = false;

    protected $queryString = [
        'terminoBusqueda' => ['except' => ''],
        'tipoUsuario' => ['except' => []],
        'entesSeleccionados' => ['except' => []],
        'estatusUsuarios' => ['except' => []],
        'fechaDesde' => ['except' => ''],
        'fechaHasta' => ['except' => ''],
    ];

    public function updatingTerminoBusqueda()
    {
        $this->resetPage();
    }

    public function updatingTipoUsuario()
    {
        $this->resetPage();
    }

    public function updatingEntesSeleccionados()
    {
        $this->resetPage();
    }

    public function updatingEstatusUsuarios()
    {
        $this->resetPage();
    }

    public function updatingFechaDesde()
    {
        $this->resetPage();
    }

    public function updatingFechaHasta()
    {
        $this->resetPage();
    }

    public function updatingFiltroEnte()
    {
        $this->resetPage();
    }

    /**
     * Resetea todos los filtros de búsqueda
     */
    public function limpiarFiltros()
    {
        $this->reset([
            'terminoBusqueda',
            'tipoUsuario',
            'entesSeleccionados',
            'estatusUsuarios',
            'fechaDesde',
            'fechaHasta',
            'filtroEnte'
        ]);
        $this->resetPage();
    }

    /**
     * Determina si hay filtros activos
     */
    #[Computed]
    public function tieneFiltrosActivos(): bool
    {
        return !empty($this->terminoBusqueda)
            || !empty($this->tipoUsuario)
            || !empty($this->entesSeleccionados)
            || !empty($this->estatusUsuarios)
            || !empty($this->fechaDesde)
            || !empty($this->fechaHasta);
    }

    /**
     * Lista de Entes para el filtro
     */
    #[Computed]
    public function entes()
    {
        $query = Ente::orderBy('nombre');
        if (!empty($this->filtroEnte)) {
            $query->where('nombre', 'like', '%' . $this->filtroEnte . '%');
        }
        return $query->get();
    }

    /**
     * Selecciona todos los entes del listado actual
     */
    public function seleccionarTodosEntes()
    {
        $this->entesSeleccionados = $this->entes->pluck('id')->toArray();
        $this->resetPage();
    }

    /**
     * Limpia la selección de entes
     */
    public function limpiarTodosEntes()
    {
        $this->entesSeleccionados = [];
        $this->resetPage();
    }

    /**
     * Consulta paginada a la bitácora de Spatie
     */
    public function obtenerActividades()
    {
        $query = Activity::query()
            ->select('activity_log.*')
            ->leftJoin('users', function ($join) {
                $join->on('activity_log.causer_id', '=', 'users.id')
                     ->where('activity_log.causer_type', '=', 'App\\Models\\User');
            })
            ->with(['causer', 'causer.ente', 'causer.roles'])
            ->latest('activity_log.created_at');

        // Búsqueda por término
        if (!empty($this->terminoBusqueda)) {
            $termino = '%' . $this->terminoBusqueda . '%';
            $query->where(function ($q) use ($termino) {
                $q->where('activity_log.log_name', 'like', $termino)
                  ->orWhere('activity_log.description', 'like', $termino)
                  ->orWhere('users.name', 'like', $termino)
                  ->orWhere('users.email', 'like', $termino);
            });
        }

        // Filtro por tipo de usuario (Roles Spatie)
        if (!empty($this->tipoUsuario)) {
            $query->where(function ($q) {
                $incluyeCongreso = in_array('congreso', $this->tipoUsuario);
                $incluyeMunicipio = in_array('municipio', $this->tipoUsuario);

                if ($incluyeCongreso && !$incluyeMunicipio) {
                    $q->whereHas('causer.roles', function ($sq) {
                        $sq->whereIn('name', ['SuperUsuario', 'Administrador', 'Revisor']);
                    });
                } elseif ($incluyeMunicipio && !$incluyeCongreso) {
                    $q->whereHas('causer.roles', function ($sq) {
                        $sq->whereIn('name', ['Tesorero', 'Tesorero Organo Descentralizado', 'Director Obras Publicas', 'Contralor']);
                    });
                }
            });
        }

        // Filtro por estatus de usuario
        if (!empty($this->estatusUsuarios)) {
            $query->where(function ($q) {
                $incluyeActivos = in_array('activos', $this->estatusUsuarios);
                $incluyeInactivos = in_array('inactivos', $this->estatusUsuarios);

                if ($incluyeActivos && !$incluyeInactivos) {
                    $q->where('users.is_active', true);
                } elseif ($incluyeInactivos && !$incluyeActivos) {
                    $q->where('users.is_active', false);
                }
            });
        }

        // Filtro por Entes específicos
        if (!empty($this->entesSeleccionados)) {
            $query->whereIn('users.ente_id', $this->entesSeleccionados);
        }

        // Rango de fechas
        if (!empty($this->fechaDesde)) {
            $query->whereDate('activity_log.created_at', '>=', $this->fechaDesde);
        }
        if (!empty($this->fechaHasta)) {
            $query->whereDate('activity_log.created_at', '<=', $this->fechaHasta);
        }

        return $query->paginate(15);
    }

    /**
     * URL para descarga en PDF
     */
    #[Computed]
    public function urlPdf(): string
    {
        return route('reportes.actividad.pdf') . '?' . http_build_query($this->obtenerParametrosFiltros());
    }

    /**
     * URL para descarga en Excel
     */
    #[Computed]
    public function urlExcel(): string
    {
        return route('reportes.actividad.excel') . '?' . http_build_query($this->obtenerParametrosFiltros());
    }

    /**
     * Parametros limpios para pasarlos a las rutas de descarga
     */
    private function obtenerParametrosFiltros(): array
    {
        return array_filter([
            'busqueda' => $this->terminoBusqueda,
            'tipo'     => $this->tipoUsuario,
            'entes'    => $this->entesSeleccionados,
            'estatus'  => $this->estatusUsuarios,
            'desde'    => $this->fechaDesde,
            'hasta'    => $this->fechaHasta,
        ]);
    }

    public function render()
    {
        return view('livewire.reportes.reporte-actividad', [
            'actividades' => $this->obtenerActividades(),
        ]);
    }
}
