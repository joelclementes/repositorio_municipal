<?php

namespace App\Services;

use App\Models\Documento;
use App\Models\Periodo;
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

        dd([
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
            ]);

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
}
