<?php
// app/Services/Documento/ClasificadorOportunidadService.php

namespace App\Services\Documento;

use App\Models\Documento;
use Carbon\Carbon;

class ClasificadorOportunidadService
{
    /**
     * Determina si un documento es oportuno o extemporáneo
     * 
     * @param Documento $documento El documento a evaluar
     * @param int $mes Mes que se está reportando (1-12)
     * @param int|null $trimestre Trimestre que se reporta (1-4, solo para periodicidad trimestral)
     * @return array ['estatus' => 'oportuno|extemporaneo', 'fecha_limite' => Carbon, 'dias_restantes' => int, 'mensaje' => string]
     */
    public function clasificar(Documento $documento, int $mes, ?int $trimestre = null): array
    {
        $fechaLimite = $this->calcularFechaLimite($documento, $mes, $trimestre);
        $hoy = Carbon::now()->startOfDay();

        $diasRestantes = $hoy->diffInDays($fechaLimite, false); // Negativo si ya pasó

        $esOportuno = $diasRestantes >= 0;

        return [
            'estatus' => $esOportuno ? 'oportuno' : 'extemporáneo',
            'fecha_limite' => $fechaLimite,
            'dias_restantes' => $diasRestantes,
            'mensaje' => $this->generarMensaje($esOportuno, $fechaLimite, $diasRestantes, $documento->periodicidad),
            'puede_subir' => $this->puedeSubir($documento, $mes, $trimestre),
        ];
    }

    /**
     * Calcula la fecha límite según la periodicidad
     */
    private function calcularFechaLimite(Documento $documento, int $mes, ?int $trimestre = null): Carbon
    {
        $año = Carbon::now()->year;
        $diaLimite = $documento->fecha_limite;

        if ($documento->periodicidad === 'mensual') {
            return $this->calcularFechaLimiteMensual($mes, $diaLimite, $año);
        }

        if ($documento->periodicidad === 'trimestral') {
            return $this->calcularFechaLimiteTrimestral($trimestre, $diaLimite, $año);
        }

        throw new \InvalidArgumentException("Periodicidad no soportada: {$documento->periodicidad}");
    }

    /**
     * Calcula fecha límite para documentos mensuales
     */
    private function calcularFechaLimiteMensual(int $mes, int $diaLimite, int $año): Carbon
    {
        // Si es diciembre, el reporte se sube en enero del próximo año
        if ($mes === 12) {
            return Carbon::create($año + 1, 1, $diaLimite);
        }

        // Para los demás meses, el reporte del mes X se sube en mes X+1
        return Carbon::create($año, $mes + 1, $diaLimite);
    }

    /**
     * Calcula fecha límite para documentos trimestrales
     */
    private function calcularFechaLimiteTrimestral(int $trimestre, int $diaLimite, int $año): Carbon
    {
        // Trimestre 1 (Ene-Mar) → límite Abril
        // Trimestre 2 (Abr-Jun) → límite Julio
        // Trimestre 3 (Jul-Sep) → límite Octubre
        // Trimestre 4 (Oct-Dic) → límite Enero del próximo año

        return match ($trimestre) {
            1 => Carbon::create($año, 4, $diaLimite),
            2 => Carbon::create($año, 7, $diaLimite),
            3 => Carbon::create($año, 10, $diaLimite),
            4 => Carbon::create($año + 1, 1, $diaLimite),
            default => throw new \InvalidArgumentException("Trimestre inválido: {$trimestre}"),
        };
    }

    /**
     * Genera mensaje descriptivo
     */
    private function generarMensaje(bool $esOportuno, Carbon $fechaLimite, int $diasRestantes, string $periodicidad): string
    {
        if ($esOportuno) {
            return "Documento {$periodicidad} OPORTUNO. " .
                "Fecha límite: {$fechaLimite->format('d/m/Y')}. " .
                "Días restantes: {$diasRestantes}.";
        }

        $diasAtraso = abs($diasRestantes);
        return "Documento {$periodicidad} EXTEMPORÁNEO. " .
            "Fecha límite era: {$fechaLimite->format('d/m/Y')}. " .
            "Días de atraso: {$diasAtraso}.";
    }

    /**
     * Valida si el mes/trimestre que se quiere subir es correcto
     */
    public function puedeSubir(Documento $documento, int $mes, ?int $trimestre = null): bool
    {
        $fechaLimite = $this->calcularFechaLimite($documento, $mes, $trimestre);
        $hoy = Carbon::now()->startOfDay();

        // Validar que no esté subiendo un período futuro
        if ($fechaLimite->greaterThan($hoy)) {
            return false;
        }

        return true;
    }
}
