<?php

namespace App\Services;

use App\Models\Documento;
use App\Models\Periodo;
use App\Models\ArchivoDocumentoRecibido;
use Carbon\Carbon;

class ReglasDocumentoService
{
    public function esOportuno(Documento $documento, Periodo $periodo, ?Carbon $fechaRecepcion = null): bool
    {
        $fechaRecepcion = $fechaRecepcion ? $fechaRecepcion->copy() : now();

        $anioActual = $fechaRecepcion->year;
        $anioPeriodo = (int) $periodo->axo;
        $mesActual = $fechaRecepcion->month;

        $regla = $documento->regla_presentacion ?? 'todo_el_anio';

        return match ($regla) {
            'trimestral_ene_abr_jul_oct' =>
            $anioActual === $anioPeriodo
                && in_array($mesActual, [1, 4, 7, 10], true),

            'dia_1_mes' =>
            $anioActual === $anioPeriodo
                && $mesActual === (int) $periodo->mes_numero
                && $fechaRecepcion->day === 1,

            'dias_16_25_mes' =>
            $anioActual === $anioPeriodo
                && $mesActual === (int) $periodo->mes_numero
                && $fechaRecepcion->day >= 16
                && $fechaRecepcion->day <= 25,

            'enero_1_31' =>
            $anioActual === $anioPeriodo
                && $mesActual === 1,

            'marzo_1_31' =>
            $anioActual === $anioPeriodo
                && $mesActual === 3,

            'abril_1_30' =>
            $anioActual === $anioPeriodo
                && $mesActual === 4,

            'enero_abril' =>
            $anioActual === $anioPeriodo
                && $mesActual >= 1
                && $mesActual <= 4,

            'septiembre_15_30' =>
            $anioActual === $anioPeriodo
                && $mesActual === 9
                && $fechaRecepcion->day >= 15
                && $fechaRecepcion->day <= 30,

            'enero_1_a_marzo_31' =>
            $anioActual === $anioPeriodo
                && $mesActual >= 1
                && $mesActual <= 3,

            'todo_el_anio' =>
            $anioActual === $anioPeriodo,

            default => false,
        };
    }

/*     public function debeRegistrarDocumentoEnPeriodo(Documento $documento, Periodo $periodo): bool
    {
        $mesPeriodo = (int) $periodo->mes_numero;
        $regla = $documento->regla_presentacion ?? 'todo_el_anio';

        return match ($regla) {
            'trimestral_ene_abr_jul_oct' => in_array($mesPeriodo, [1, 4, 7, 10], true),
            'enero_1_31' => $mesPeriodo === 1,
            'marzo_1_31' => $mesPeriodo === 3,
            'abril_1_30' => $mesPeriodo === 4,
            'enero_abril' => $mesPeriodo >= 1 && $mesPeriodo <= 4,
            'septiembre_15_30' => $mesPeriodo === 9,
            'enero_1_a_marzo_31' => $mesPeriodo >= 1 && $mesPeriodo <= 3,
            'todo_el_anio' => true,
            default => true,
        };
    } */

        public function debeRegistrarDocumentoEnPeriodo(Documento $documento, Periodo $periodo): bool
{
    $mesPeriodo = (int) $periodo->mes_numero;
    $anioPeriodo = (int) $periodo->axo;

    $hoy = now();

    $regla = $documento->regla_presentacion ?? 'todo_el_anio';

    return match ($regla) {
        'trimestral_ene_abr_jul_oct' =>
            in_array($mesPeriodo, [1, 4, 7, 10], true),

        'dia_1_mes' =>
            $hoy->year === $anioPeriodo
            && $hoy->month === $mesPeriodo
            && $hoy->day === 1,

        'dias_16_25_mes' =>
            $hoy->year === $anioPeriodo
            && $hoy->month === $mesPeriodo
            && $hoy->day >= 16
            && $hoy->day <= 25,

        'enero_1_31' =>
            $mesPeriodo === 1,

        'marzo_1_31' =>
            $mesPeriodo === 3,

        'abril_1_30' =>
            $mesPeriodo === 4,

        'enero_abril' =>
            $mesPeriodo >= 1 && $mesPeriodo <= 4,

        'septiembre_15_30' =>
            $mesPeriodo === 9,

        'enero_1_a_marzo_31' =>
            $mesPeriodo >= 1 && $mesPeriodo <= 3,

        'todo_el_anio' =>
            true,

        default => true,
    };
}

    public function evaluarBloqueoPorReglaYSubidaPrevia(
        Documento $documento,
        Periodo $periodo,
        int $enteId,
        ?string $tipoRecepcion = null
    ): array {
        $regla = $documento->regla_presentacion ?? 'todo_el_anio';
        $anio = (int) $periodo->axo;
        $mesPeriodo = (int) $periodo->mes_numero;

        [$mesInicio, $mesFin] = match ($regla) {
            'enero_abril' => [1, 4],
            'enero_1_a_marzo_31' => [1, 3],
            default => [null, null],
        };

        if ($mesInicio === null) {
            return [
                'habilitado' => true,
                'ya_subido' => false,
                'leyenda' => null,
            ];
        }

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
                $q->where('documento_id', $documento->id)
                    ->whereHas('periodo', function ($qp) use ($anio, $mesInicio, $mesFin) {
                        $qp->where('axo', $anio)
                            ->whereBetween('mes_numero', [$mesInicio, $mesFin]);
                    });
            });

        if (!empty($tipoRecepcion)) {
            if ($tipoRecepcion === 'XLSX') {
                $query->whereIn('tipo_recepcion', ['XLSX', 'XLS']);
            } else {
                $query->where('tipo_recepcion', $tipoRecepcion);
            }
        }

        $query->where(function ($q) {
            $q->whereNull('autorizado_reenviar')
                ->orWhere('autorizado_reenviar', false);
        });

        $yaSubido = $query->exists();

        return [
            'habilitado' => !$yaSubido,
            'ya_subido' => $yaSubido,
            'leyenda' => $yaSubido ? 'Ya se subió en este ejercicio' : null,
        ];
    }
}
