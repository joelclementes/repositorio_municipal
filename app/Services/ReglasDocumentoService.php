<?php

namespace App\Services;

use App\Models\Documento;
use App\Models\Periodo;
use App\Models\ArchivoDocumentoRecibido;
use Carbon\Carbon;
use InvalidArgumentException;

class ReglasDocumentoService
{
    /**
     * Determina si un documento está en tiempo (true) o extemporáneo (false),
     * según su regla_presentacion, el periodo seleccionado y la fecha de recepción.
     */
    public function esOportuno(Documento $documento, Periodo $periodo, ?Carbon $fechaRecepcion = null): bool
    {
        $fechaRecepcion = $fechaRecepcion ? $fechaRecepcion->copy() : now();

        [$inicioPeriodo, $finPeriodo] = $this->rangoMesPeriodo($periodo);
        $anioActual = $fechaRecepcion->year;
        $mesPeriodo = $this->mesANumero($periodo->mes);
        $mesActual = $fechaRecepcion->month;
        $anioPeriodo = (int) $periodo->axo;

        $regla = $documento->regla_presentacion ?? 'todo_el_anio';



        $esOportuno = match ($regla) {
            'trimestral_ene_abr_jul_oct' =>
            in_array($mesActual, [1, 4, 7, 10], true),

            'dia_1_mes' =>
            $fechaRecepcion->between($inicioPeriodo, $finPeriodo)
                && $fechaRecepcion->day === 1,

            'dias_16_25_mes' =>
            $fechaRecepcion->between($inicioPeriodo, $finPeriodo)
                && $fechaRecepcion->day >= 16
                && $fechaRecepcion->day <= 25,

            'enero_1_31' =>
            $anioActual === $anioPeriodo
                && $fechaRecepcion->month === 1,

            'marzo_1_31' =>
            $anioActual === $anioPeriodo
                && $fechaRecepcion->month === 3,

            'abril_1_30' =>
            $anioActual === $anioPeriodo
                && $fechaRecepcion->month === 4,

            'enero_abril' =>
            $anioActual === $anioPeriodo
                && $fechaRecepcion->month >= 1
                && $fechaRecepcion->month <= 4,

            'septiembre_15_30' =>
            $anioActual === $anioPeriodo
                && $fechaRecepcion->month === 9
                && $fechaRecepcion->day >= 15
                && $fechaRecepcion->day <= 30,

            'enero_1_a_marzo_31' =>
            $anioActual === $anioPeriodo
                && $fechaRecepcion->month >= 1
                && $fechaRecepcion->month <= 3,

            'todo_el_anio' =>
            true,
            // $fechaRecepcion->between($inicioPeriodo, $finPeriodo),

            default => false,
        };

        /*         dd([
            'regla' => $regla,
            'mesActual' => $mesActual,
            'mesPeriodo' => $mesPeriodo,
            'diaActual' => $fechaRecepcion->day,
            'anioActual' => $fechaRecepcion->year,
            'anioPeriodo' => $anioPeriodo,
            'inicioPeriodo' => $inicioPeriodo?->toDateTimeString(),
            'finPeriodo' => $finPeriodo?->toDateTimeString(),
            'estaEnRango' => $fechaRecepcion->between($inicioPeriodo, $finPeriodo),
            'esOportuno' => $esOportuno,
            ]); */

        return $esOportuno;
    }

    /**
     * Devuelve el inicio y fin del mes correspondiente al periodo (año/mes del periodo).
     */
    private function rangoMesPeriodo(Periodo $periodo): array
    {
        $mesNumero = $this->mesANumero($periodo->mes);
        $anio = (int) $periodo->axo;

        $inicio = Carbon::create($anio, $mesNumero, 1)->startOfDay();
        $fin = Carbon::create($anio, $mesNumero, 1)->endOfMonth()->endOfDay();

        return [$inicio, $fin];
    }

    /**
     * Convierte nombre de mes en español a número de mes.
     */
    private function mesANumero(string $mes): int
    {
        $mapa = [
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'setiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12,
        ];

        $mesNormalizado = mb_strtolower(trim($mes), 'UTF-8');

        if (!isset($mapa[$mesNormalizado])) {
            throw new InvalidArgumentException("Mes inválido en período: {$mes}");
        }

        return $mapa[$mesNormalizado];
    }

    public function debeRegistrarDocumentoEnPeriodo(Documento $documento, Periodo $periodo): bool
    {
        $mesPeriodo = $this->normalizarMesPeriodo($periodo->mes);
        $regla = $documento->regla_presentacion;

        return match ($regla) {
            'trimestral_ene_abr_jul_oct' => in_array($mesPeriodo, ['enero', 'abril', 'julio', 'octubre'], true),
            'enero_1_31' => $mesPeriodo === 'enero',
            'marzo_1_31' => $mesPeriodo === 'marzo',
            'abril_1_30' => $mesPeriodo === 'abril',
            'enero_abril' => in_array($mesPeriodo, ['enero', 'febrero', 'marzo', 'abril'], true),
            'septiembre_15_30' => $mesPeriodo === 'septiembre',
            'enero_1_a_marzo_31' => in_array($mesPeriodo, ['enero', 'febrero', 'marzo'], true),
            default => true,
        };
    }

    private function normalizarMesPeriodo(string $mes): string
    {
        $mes = mb_strtolower(trim($mes), 'UTF-8');

        $meses = [
            '1' => 'enero',
            '2' => 'febrero',
            '3' => 'marzo',
            '4' => 'abril',
            '5' => 'mayo',
            '6' => 'junio',
            '7' => 'julio',
            '8' => 'agosto',
            '9' => 'septiembre',
            '10' => 'octubre',
            '11' => 'noviembre',
            '12' => 'diciembre',
            'setiembre' => 'septiembre',
        ];

        return $meses[$mes] ?? $mes;
    }

    /**
     * Evalúa si para reglas de "una sola entrega en rango" el documento ya fue subido
     * en el año del periodo y dentro del rango de meses definido por la regla.
     *
     * Retorna:
     * - habilitado: bool
     * - ya_subido: bool
     * - leyenda: ?string
     */
    public function evaluarBloqueoPorReglaYSubidaPrevia(
        Documento $documento,
        Periodo $periodo,
        int $enteId,
        ?string $tipoRecepcion = null
    ): array {
        $regla = $documento->regla_presentacion ?? 'todo_el_anio';
        $anio = (int) $periodo->axo;

        // Mes actual del periodo seleccionado
        $mesPeriodo = $this->mesANumero((string) $periodo->mes);

        // Ventana de meses por regla
        [$mesInicio, $mesFin] = match ($regla) {
            'enero_abril' => [1, 4],
            'enero_1_a_marzo_31' => [1, 3],
            'todo_el_anio' => [1, 12],
            default => [null, null],
        };

        // Si no es una de las reglas objetivo, no aplicar bloqueo transversal
        if ($mesInicio === null) {
            return [
                'habilitado' => true,
                'ya_subido' => false,
                'leyenda' => null,
            ];
        }

        // Si el periodo seleccionado está fuera de la ventana de la regla, no bloquea aquí
        // (normalmente ni siquiera debería aparecer por debeRegistrarDocumentoEnPeriodo)
        if ($mesPeriodo < $mesInicio || $mesPeriodo > $mesFin) {
            return [
                'habilitado' => true,
                'ya_subido' => false,
                'leyenda' => null,
            ];
        }

        $query = ArchivoDocumentoRecibido::query()
            ->where('ente_id', $enteId)
            ->whereHas('documentoRecibido', function ($q) use ($documento, $anio, $mesInicio, $mesFin) {
                $q->where('documentos_id', $documento->id)
                    ->whereHas('periodo', function ($qp) use ($anio, $mesInicio, $mesFin) {
                        $qp->where('axo', $anio)
                            ->whereBetween('mes', [$mesInicio, $mesFin]);
                    });
            });

        if (!empty($tipoRecepcion)) {
            $query->where('tipo_recepcion', $tipoRecepcion);
        }

        // Respeta reenvío autorizado (si lo manejas)
        $query->where(function ($q) {
            $q->whereNull('autorizado_reenviar')
                ->orWhere('autorizado_reenviar', 0);
        });

        $yaSubido = $query->exists();

        return [
            'habilitado' => !$yaSubido,
            'ya_subido' => $yaSubido,
            'leyenda' => $yaSubido ? 'Ya se subió' : null,
        ];
    }
}
