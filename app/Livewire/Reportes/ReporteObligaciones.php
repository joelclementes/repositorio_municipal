<?php

namespace App\Livewire\Reportes;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Ente;
use App\Models\Periodo;
use App\Models\CategoriasDocumento;
use App\Models\SubcategoriasDocumento;
use App\Models\Documento;
use App\Models\DocumentosRecibido;
use App\Models\ArchivoDocumentoRecibido;

class ReporteObligaciones extends Component
{
    public $enteSeleccionado = '';
    public $axoSeleccionado = '';

    // --- Estado de filtros ---
    public array $categoriasSeleccionadas    = [];
    public array $subcategoriasSeleccionadas = [];
    public array $documentosSeleccionados    = [];
    public bool  $mostrarFiltros             = false;
    public bool  $tieneFiltrosActivos        = false;

    // -------------------------------------------------------------------------
    // Datos básicos del formulario
    // -------------------------------------------------------------------------

    #[Computed]
    public function entes()
    {
        return Ente::orderBy('nombre')->get();
    }

    #[Computed]
    public function axosDisponibles()
    {
        return Periodo::select('axo')
            ->distinct()
            ->orderBy('axo', 'desc')
            ->pluck('axo');
    }

    #[Computed]
    public function periodosDelAxo()
    {
        if (!$this->axoSeleccionado) {
            return collect();
        }
        return Periodo::where('axo', $this->axoSeleccionado)
            ->orderBy('mes_numero')
            ->get();
    }

    // -------------------------------------------------------------------------
    // Datos para el panel de filtros
    // -------------------------------------------------------------------------

    #[Computed]
    public function todasLasCategorias()
    {
        return CategoriasDocumento::orderBy('nombre')->get();
    }

    /** Subcategorías disponibles según categorías actualmente seleccionadas */
    #[Computed]
    public function subcategoriasDisponibles()
    {
        $q = SubcategoriasDocumento::orderBy('nombre');
        if (!empty($this->categoriasSeleccionadas)) {
            $q->whereIn('categoria_id', $this->categoriasSeleccionadas);
        }
        return $q->get();
    }

    /** Documentos disponibles según subcategorías actualmente seleccionadas */
    #[Computed]
    public function documentosDisponibles()
    {
        $q = Documento::orderBy('nombre');
        if (!empty($this->subcategoriasSeleccionadas)) {
            $q->whereIn('subcategoria_id', $this->subcategoriasSeleccionadas);
        } elseif (!empty($this->categoriasSeleccionadas)) {
            // Si hay categorías pero ninguna subcategoría seleccionada, no hay documentos
            return collect();
        }
        return $q->get();
    }

    // -------------------------------------------------------------------------
    // URLs de exportación con filtros
    // -------------------------------------------------------------------------

    #[Computed]
    public function urlPdf(): string
    {
        $params = ['ente' => $this->enteSeleccionado, 'axo' => $this->axoSeleccionado];
        if ($this->tieneFiltrosActivos) {
            $params['categorias']    = $this->categoriasSeleccionadas;
            $params['subcategorias'] = $this->subcategoriasSeleccionadas;
            $params['documentos']    = $this->documentosSeleccionados;
        }
        return route('reportes.obligaciones.pdf') . '?' . http_build_query($params);
    }

    #[Computed]
    public function urlExcel(): string
    {
        $params = ['ente' => $this->enteSeleccionado, 'axo' => $this->axoSeleccionado];
        if ($this->tieneFiltrosActivos) {
            $params['categorias']    = $this->categoriasSeleccionadas;
            $params['subcategorias'] = $this->subcategoriasSeleccionadas;
            $params['documentos']    = $this->documentosSeleccionados;
        }
        return route('reportes.obligaciones.excel') . '?' . http_build_query($params);
    }

    // -------------------------------------------------------------------------
    // Contadores para el panel
    // -------------------------------------------------------------------------

    #[Computed]
    public function totalCategorias(): int
    {
        return CategoriasDocumento::count();
    }

    #[Computed]
    public function totalSubcategorias(): int
    {
        if (!empty($this->categoriasSeleccionadas)) {
            return SubcategoriasDocumento::whereIn('categoria_id', $this->categoriasSeleccionadas)->count();
        }
        return SubcategoriasDocumento::count();
    }

    #[Computed]
    public function totalDocumentos(): int
    {
        if (!empty($this->subcategoriasSeleccionadas)) {
            return Documento::whereIn('subcategoria_id', $this->subcategoriasSeleccionadas)->count();
        }
        return Documento::count();
    }

    // -------------------------------------------------------------------------
    // Lifecycle hooks
    // -------------------------------------------------------------------------

    public function updatedEnteSeleccionado(): void
    {
        $this->inicializarFiltros();
    }

    public function updatedAxoSeleccionado(): void
    {
        $this->inicializarFiltros();
    }

    private function inicializarFiltros(): void
    {
        $this->categoriasSeleccionadas    = CategoriasDocumento::pluck('id')->map(fn($id) => (int) $id)->toArray();
        $this->subcategoriasSeleccionadas = SubcategoriasDocumento::pluck('id')->map(fn($id) => (int) $id)->toArray();
        $this->documentosSeleccionados    = Documento::pluck('id')->map(fn($id) => (int) $id)->toArray();
        $this->tieneFiltrosActivos        = false;
        $this->mostrarFiltros             = false;
    }

    // -------------------------------------------------------------------------
    // Acciones del panel de filtros
    // -------------------------------------------------------------------------

    public function toggleFiltros(): void
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }

    // --- Categorías ---

    public function toggleCategoria(int $id): void
    {
        if (in_array($id, $this->categoriasSeleccionadas)) {
            // Desmarcar + cascada hacia abajo
            $this->categoriasSeleccionadas = array_values(
                array_filter($this->categoriasSeleccionadas, fn($c) => $c !== $id)
            );
            $subs = SubcategoriasDocumento::where('categoria_id', $id)
                ->pluck('id')->map(fn($i) => (int) $i)->toArray();
            $this->subcategoriasSeleccionadas = array_values(
                array_filter($this->subcategoriasSeleccionadas, fn($s) => !in_array($s, $subs))
            );
            if (!empty($subs)) {
                $docs = Documento::whereIn('subcategoria_id', $subs)
                    ->pluck('id')->map(fn($i) => (int) $i)->toArray();
                $this->documentosSeleccionados = array_values(
                    array_filter($this->documentosSeleccionados, fn($d) => !in_array($d, $docs))
                );
            }
        } else {
            // Marcar + cascada hacia abajo (agrega sus hijos)
            $this->categoriasSeleccionadas[] = $id;
            $subs = SubcategoriasDocumento::where('categoria_id', $id)
                ->pluck('id')->map(fn($i) => (int) $i)->toArray();
            $this->subcategoriasSeleccionadas = array_values(
                array_unique(array_merge($this->subcategoriasSeleccionadas, $subs))
            );
            if (!empty($subs)) {
                $docs = Documento::whereIn('subcategoria_id', $subs)
                    ->pluck('id')->map(fn($i) => (int) $i)->toArray();
                $this->documentosSeleccionados = array_values(
                    array_unique(array_merge($this->documentosSeleccionados, $docs))
                );
            }
        }
        $this->tieneFiltrosActivos = true;
    }

    public function seleccionarTodasCategorias(): void
    {
        $this->inicializarFiltros();
        $this->tieneFiltrosActivos = false;
    }

    public function limpiarCategorias(): void
    {
        $this->categoriasSeleccionadas    = [];
        $this->subcategoriasSeleccionadas = [];
        $this->documentosSeleccionados    = [];
        $this->tieneFiltrosActivos        = true;
    }

    // --- Subcategorías ---

    public function toggleSubcategoria(int $id): void
    {
        if (in_array($id, $this->subcategoriasSeleccionadas)) {
            $this->subcategoriasSeleccionadas = array_values(
                array_filter($this->subcategoriasSeleccionadas, fn($s) => $s !== $id)
            );
            $docs = Documento::where('subcategoria_id', $id)
                ->pluck('id')->map(fn($i) => (int) $i)->toArray();
            $this->documentosSeleccionados = array_values(
                array_filter($this->documentosSeleccionados, fn($d) => !in_array($d, $docs))
            );
        } else {
            $this->subcategoriasSeleccionadas[] = $id;
            $docs = Documento::where('subcategoria_id', $id)
                ->pluck('id')->map(fn($i) => (int) $i)->toArray();
            $this->documentosSeleccionados = array_values(
                array_unique(array_merge($this->documentosSeleccionados, $docs))
            );
        }
        $this->tieneFiltrosActivos = true;
    }

    public function seleccionarTodasSubcategorias(): void
    {
        $q = SubcategoriasDocumento::query();
        if (!empty($this->categoriasSeleccionadas)) {
            $q->whereIn('categoria_id', $this->categoriasSeleccionadas);
        }
        $subs = $q->pluck('id')->map(fn($i) => (int) $i)->toArray();
        $this->subcategoriasSeleccionadas = $subs;

        $docs = empty($subs)
            ? []
            : Documento::whereIn('subcategoria_id', $subs)->pluck('id')->map(fn($i) => (int) $i)->toArray();
        $this->documentosSeleccionados = $docs;
        $this->tieneFiltrosActivos     = true;
    }

    public function limpiarSubcategorias(): void
    {
        $this->subcategoriasSeleccionadas = [];
        $this->documentosSeleccionados    = [];
        $this->tieneFiltrosActivos        = true;
    }

    // --- Documentos ---

    public function toggleDocumento(int $id): void
    {
        if (in_array($id, $this->documentosSeleccionados)) {
            $this->documentosSeleccionados = array_values(
                array_filter($this->documentosSeleccionados, fn($d) => $d !== $id)
            );
        } else {
            $this->documentosSeleccionados[] = $id;
        }
        $this->tieneFiltrosActivos = true;
    }

    public function seleccionarTodosDocumentos(): void
    {
        $q = Documento::query();
        if (!empty($this->subcategoriasSeleccionadas)) {
            $q->whereIn('subcategoria_id', $this->subcategoriasSeleccionadas);
        }
        $this->documentosSeleccionados = $q->pluck('id')->map(fn($i) => (int) $i)->toArray();
        $this->tieneFiltrosActivos     = true;
    }

    public function limpiarDocumentos(): void
    {
        $this->documentosSeleccionados = [];
        $this->tieneFiltrosActivos     = true;
    }

    // --- Reset global ---

    public function limpiarTodosFiltros(): void
    {
        $this->inicializarFiltros();
    }

    // -------------------------------------------------------------------------
    // Lógica de cálculo (sin cambios respecto al original)
    // -------------------------------------------------------------------------

    /**
     * Determina si un documento aplica en un mes dado según su regla_presentacion
     */
    private function documentoAplicaEnMes(Documento $documento, int $mesNumero): bool
    {
        $regla = $documento->regla_presentacion ?? 'todo_el_anio';

        return match ($regla) {
            'todo_el_anio'               => true,
            'trimestral_ene_abr_jul_oct' => in_array($mesNumero, [1, 4, 7, 10]),
            'dia_1_mes'                  => true,
            'dias_16_25_mes'             => true,
            'enero_abril'                => $mesNumero >= 1 && $mesNumero <= 4,
            'septiembre_15_30'           => $mesNumero === 9,
            'enero_1_a_marzo_31'         => $mesNumero >= 1 && $mesNumero <= 3,
            'enero_1_31'                 => $mesNumero === 1,
            'marzo_1_31'                 => $mesNumero === 3,
            'abril_1_30'                 => $mesNumero === 4,
            default                      => true,
        };
    }

    /**
     * Determina el tipo de periodo para mostrar encabezados apropiados
     */
    private function getTipoPeriodoSubcategoria(int $subcategoriaId, $documentos): string
    {
        $reglas = $documentos->pluck('regla_presentacion')->unique();

        if ($reglas->count() === 1 && $reglas->first() === 'trimestral_ene_abr_jul_oct') {
            return 'trimestral';
        }

        return 'mensual';
    }

    /**
     * Calcula el estado (P/NP/vacío) de un documento en un periodo dado
     */
    private function calcularEstadoDocumento(int $enteId, int $documentoId, ?int $periodoId, Documento $documento, int $mesNumero): array
    {
        if (!$this->documentoAplicaEnMes($documento, $mesNumero)) {
            return ['estado' => '', 'clase' => 'no-aplica'];
        }

        if (!$periodoId) {
            return ['estado' => '', 'clase' => 'no-aplica'];
        }

        $documentoRecibido = DocumentosRecibido::where('ente_id', $enteId)
            ->where('documento_id', $documentoId)
            ->where('periodo_id', $periodoId)
            ->first();

        if (!$documentoRecibido) {
            return ['estado' => 'NP', 'clase' => 'no-presentado'];
        }

        $archivos      = ArchivoDocumentoRecibido::where('documento_recibido_id', $documentoRecibido->id)->get();
        $totalArchivos = $archivos->count();

        if ($totalArchivos === 0) {
            return ['estado' => 'NP', 'clase' => 'no-presentado'];
        }

        $aprobados  = $archivos->where('estado_id', 3)->count();
        $porcentaje = ($aprobados / $totalArchivos) * 100;

        if ($porcentaje >= 80) {
            return ['estado' => 'P', 'clase' => 'presentado'];
        }

        return ['estado' => 'NP', 'clase' => 'no-presentado'];
    }

    /**
     * Obtiene las observaciones (causas de rechazo) de un documento en todos los periodos
     */
    private function obtenerObservaciones(int $enteId, int $documentoId, $periodos): array
    {
        $observaciones = [];

        foreach ($periodos as $periodo) {
            $documentoRecibido = DocumentosRecibido::where('ente_id', $enteId)
                ->where('documento_id', $documentoId)
                ->where('periodo_id', $periodo->id)
                ->first();

            if (!$documentoRecibido) {
                continue;
            }

            $archivosRechazados = ArchivoDocumentoRecibido::where('documento_recibido_id', $documentoRecibido->id)
                ->where('estado_id', 4)
                ->with('causaRechazo')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($archivosRechazados as $archivo) {
                $texto     = '';
                $mesNombre = $periodo->mes ?? 'Mes ' . $periodo->mes_numero;

                if ($archivo->causaRechazo) {
                    $texto = "Rechazado {$mesNombre}: {$archivo->causaRechazo->descripcion}";
                }

                if ($archivo->observaciones_revisor) {
                    $texto .= ($texto ? '; ' : "Rechazado {$mesNombre}: ") . $archivo->observaciones_revisor;
                }

                if ($texto) {
                    $observaciones[] = [
                        'texto' => $texto,
                        'fecha' => $archivo->created_at,
                        'mes'   => $mesNombre,
                    ];
                }
            }
        }

        usort($observaciones, fn($a, $b) => $a['fecha'] <=> $b['fecha']);

        return $observaciones;
    }

    // -------------------------------------------------------------------------
    // Computed: Datos del reporte (con filtros aplicados)
    // -------------------------------------------------------------------------

    #[Computed]
    public function datosReporte()
    {
        if (!$this->enteSeleccionado || !$this->axoSeleccionado) {
            return null;
        }

        $ente    = Ente::find($this->enteSeleccionado);
        $periodos = $this->periodosDelAxo;

        if (!$ente || $periodos->isEmpty()) {
            return null;
        }

        $periodosPorMes = $periodos->keyBy('mes_numero');

        // --- Categorías (con filtro) ---
        $categoriasQuery = CategoriasDocumento::with([
            'subcategorias' => function ($query) {
                $query->orderBy('id');
            },
            'subcategorias.categoria',
        ])->orderBy('id');

        if ($this->tieneFiltrosActivos) {
            if (empty($this->categoriasSeleccionadas)) {
                return ['ente' => $ente, 'axo' => $this->axoSeleccionado, 'categorias' => []];
            }
            $categoriasQuery->whereIn('id', $this->categoriasSeleccionadas);
        }

        $categorias = $categoriasQuery->get();
        $resultado  = [];

        foreach ($categorias as $categoria) {
            $subcategoriasData = [];
            $subcategorias     = $categoria->subcategorias;

            // Filtro de subcategorías
            if ($this->tieneFiltrosActivos) {
                if (empty($this->subcategoriasSeleccionadas)) {
                    continue;
                }
                $subcategorias = $subcategorias->whereIn('id', $this->subcategoriasSeleccionadas);
            }

            foreach ($subcategorias as $subcategoria) {
                $documentosQuery = Documento::where('subcategoria_id', $subcategoria->id)->orderBy('id');

                // Filtro de documentos
                if ($this->tieneFiltrosActivos) {
                    if (empty($this->documentosSeleccionados)) {
                        continue;
                    }
                    $documentosQuery->whereIn('id', $this->documentosSeleccionados);
                }

                $documentos = $documentosQuery->get();

                if ($documentos->isEmpty()) {
                    continue;
                }

                $tipoPeriodo  = $this->getTipoPeriodoSubcategoria($subcategoria->id, $documentos);
                $documentosData = [];

                foreach ($documentos as $documento) {
                    $meses = [];

                    if ($tipoPeriodo === 'trimestral') {
                        $trimestres = [1 => [1], 2 => [4], 3 => [7], 4 => [10]];
                        foreach ($trimestres as $numTrim => $mesesTrim) {
                            $mesRef    = $mesesTrim[0];
                            $periodoId = $periodosPorMes->get($mesRef)?->id;
                            $meses[$numTrim] = $this->calcularEstadoDocumento(
                                $ente->id, $documento->id, $periodoId, $documento, $mesRef
                            );
                        }
                    } else {
                        for ($mes = 1; $mes <= 12; $mes++) {
                            $periodoId = $periodosPorMes->get($mes)?->id;
                            $meses[$mes] = $this->calcularEstadoDocumento(
                                $ente->id, $documento->id, $periodoId, $documento, $mes
                            );
                        }
                    }

                    $observaciones = $this->obtenerObservaciones($ente->id, $documento->id, $periodos);

                    $documentosData[] = [
                        'id'           => $documento->id,
                        'clave'        => $documento->clave,
                        'nombre'       => $documento->nombre,
                        'regla'        => $documento->regla_presentacion,
                        'meses'        => $meses,
                        'observaciones'=> $observaciones,
                    ];
                }

                if (!empty($documentosData)) {
                    $subcategoriasData[] = [
                        'id'          => $subcategoria->id,
                        'nombre'      => $subcategoria->nombre,
                        'tipo_periodo'=> $tipoPeriodo,
                        'documentos'  => $documentosData,
                    ];
                }
            }

            if (!empty($subcategoriasData)) {
                $resultado[] = [
                    'id'           => $categoria->id,
                    'nombre'       => $categoria->nombre,
                    'clave'        => $categoria->clave,
                    'subcategorias'=> $subcategoriasData,
                ];
            }
        }

        return [
            'ente'       => $ente,
            'axo'        => $this->axoSeleccionado,
            'categorias' => $resultado,
        ];
    }

    /**
     * Obtiene el nombre del ente seleccionado
     */
    #[Computed]
    public function nombreEnte()
    {
        if (!$this->enteSeleccionado) {
            return '';
        }
        return Ente::find($this->enteSeleccionado)?->nombre ?? '';
    }

    public function render()
    {
        return view('livewire.reportes.reporte-obligaciones');
    }
}
