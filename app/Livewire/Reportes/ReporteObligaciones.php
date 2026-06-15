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

    public function updatedEnteSeleccionado()
    {
        // Reset when ente changes
    }

    public function updatedAxoSeleccionado()
    {
        // Reset when year changes
    }

    /**
     * Determina si un documento aplica en un mes dado según su regla_presentacion
     */
    private function documentoAplicaEnMes(Documento $documento, int $mesNumero): bool
    {
        $regla = $documento->regla_presentacion ?? 'todo_el_anio';

        return match ($regla) {
            'todo_el_anio' => true,
            'trimestral_ene_abr_jul_oct' => in_array($mesNumero, [1, 4, 7, 10]),
            'dia_1_mes' => true, // Aplica todos los meses, pero solo el día 1
            'dias_16_25_mes' => true, // Aplica todos los meses, pero solo días 16-25
            'enero_abril' => $mesNumero >= 1 && $mesNumero <= 4,
            'septiembre_15_30' => $mesNumero === 9,
            'enero_1_a_marzo_31' => $mesNumero >= 1 && $mesNumero <= 3,
            'enero_1_31' => $mesNumero === 1,
            'marzo_1_31' => $mesNumero === 3,
            'abril_1_30' => $mesNumero === 4,
            default => true,
        };
    }

    /**
     * Determina el tipo de periodo para mostrar encabezados apropiados
     * 'mensual' = ene-dic, 'trimestral' = 1er-4to trim, etc.
     */
    private function getTipoPeriodoSubcategoria(int $subcategoriaId, $documentos): string
    {
        // Verificar si todos los documentos de la subcategoría son trimestrales
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
        // Si el documento no aplica en este mes, marcar como gris
        if (!$this->documentoAplicaEnMes($documento, $mesNumero)) {
            return [
                'estado' => '',
                'clase' => 'no-aplica',
            ];
        }

        if (!$periodoId) {
            return [
                'estado' => '',
                'clase' => 'no-aplica',
            ];
        }

        // Buscar el documento_recibido
        $documentoRecibido = DocumentosRecibido::where('ente_id', $enteId)
            ->where('documento_id', $documentoId)
            ->where('periodo_id', $periodoId)
            ->first();

        if (!$documentoRecibido) {
            return [
                'estado' => 'NP',
                'clase' => 'no-presentado',
            ];
        }

        // Contar archivos totales y aprobados
        $archivos = ArchivoDocumentoRecibido::where('documento_recibido_id', $documentoRecibido->id)->get();
        $totalArchivos = $archivos->count();

        if ($totalArchivos === 0) {
            return [
                'estado' => 'NP',
                'clase' => 'no-presentado',
            ];
        }

        // estado_id = 3 es "Aprobado" según el seeder
        $aprobados = $archivos->where('estado_id', 3)->count();
        $porcentaje = ($aprobados / $totalArchivos) * 100;

        if ($porcentaje >= 80) {
            return [
                'estado' => 'P',
                'clase' => 'presentado',
            ];
        }

        return [
            'estado' => 'NP',
            'clase' => 'no-presentado',
        ];
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

            // Obtener archivos rechazados con sus causas
            $archivosRechazados = ArchivoDocumentoRecibido::where('documento_recibido_id', $documentoRecibido->id)
                ->where('estado_id', 4) // Rechazado
                ->with('causaRechazo')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($archivosRechazados as $archivo) {
                $texto = '';
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
                        'mes' => $mesNombre,
                    ];
                }
            }
        }

        // Ordenar por fecha
        usort($observaciones, function ($a, $b) {
            return $a['fecha'] <=> $b['fecha'];
        });

        return $observaciones;
    }

    #[Computed]
    public function datosReporte()
    {
        if (!$this->enteSeleccionado || !$this->axoSeleccionado) {
            return null;
        }

        $ente = Ente::find($this->enteSeleccionado);
        $periodos = $this->periodosDelAxo;

        if (!$ente || $periodos->isEmpty()) {
            return null;
        }

        // Mapear periodos por mes_numero para acceso rápido
        $periodosPorMes = $periodos->keyBy('mes_numero');

        // Obtener todas las categorías con sus subcategorías y documentos
        $categorias = CategoriasDocumento::with([
            'subcategorias' => function ($query) {
                $query->orderBy('id');
            },
            'subcategorias.categoria'
        ])->orderBy('id')->get();

        $resultado = [];

        foreach ($categorias as $categoria) {
            $subcategoriasData = [];

            foreach ($categoria->subcategorias as $subcategoria) {
                $documentos = Documento::where('subcategoria_id', $subcategoria->id)
                    ->orderBy('id')
                    ->get();

                if ($documentos->isEmpty()) {
                    continue;
                }

                $tipoPeriodo = $this->getTipoPeriodoSubcategoria($subcategoria->id, $documentos);
                $documentosData = [];

                foreach ($documentos as $documento) {
                    $meses = [];

                    if ($tipoPeriodo === 'trimestral') {
                        // Para documentos trimestrales, mostrar 4 trimestres
                        $trimestres = [
                            1 => [1],    // 1er Trimestre -> enero
                            2 => [4],    // 2do Trimestre -> abril
                            3 => [7],    // 3er Trimestre -> julio
                            4 => [10],   // 4to Trimestre -> octubre
                        ];

                        foreach ($trimestres as $numTrim => $mesesTrim) {
                            $mesRef = $mesesTrim[0];
                            $periodoId = $periodosPorMes->get($mesRef)?->id;

                            $meses[$numTrim] = $this->calcularEstadoDocumento(
                                $ente->id,
                                $documento->id,
                                $periodoId,
                                $documento,
                                $mesRef
                            );
                        }
                    } else {
                        // Para documentos mensuales, mostrar 12 meses
                        for ($mes = 1; $mes <= 12; $mes++) {
                            $periodoId = $periodosPorMes->get($mes)?->id;

                            $meses[$mes] = $this->calcularEstadoDocumento(
                                $ente->id,
                                $documento->id,
                                $periodoId,
                                $documento,
                                $mes
                            );
                        }
                    }

                    // Obtener observaciones acumuladas
                    $observaciones = $this->obtenerObservaciones($ente->id, $documento->id, $periodos);

                    $documentosData[] = [
                        'id' => $documento->id,
                        'clave' => $documento->clave,
                        'nombre' => $documento->nombre,
                        'regla' => $documento->regla_presentacion,
                        'meses' => $meses,
                        'observaciones' => $observaciones,
                    ];
                }

                if (!empty($documentosData)) {
                    $subcategoriasData[] = [
                        'id' => $subcategoria->id,
                        'nombre' => $subcategoria->nombre,
                        'tipo_periodo' => $tipoPeriodo,
                        'documentos' => $documentosData,
                    ];
                }
            }

            if (!empty($subcategoriasData)) {
                $resultado[] = [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre,
                    'clave' => $categoria->clave,
                    'subcategorias' => $subcategoriasData,
                ];
            }
        }

        return [
            'ente' => $ente,
            'axo' => $this->axoSeleccionado,
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
