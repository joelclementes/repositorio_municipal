<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Models\PeriodoEnte;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class Periodo extends Model
{
    use LogsActivity;

    protected $table = 'periodos';

    protected $fillable = [
        'mes_numero',
        'mes',
        'axo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
    ];

    protected $casts = [
        'mes_numero' => 'integer',
        'axo' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'is_active' => 'boolean',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['mes_numero', 'mes', 'axo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $user = Auth::user();
        $userNombre = $user ? "\"{$user->name}\" ({$user->email})" : 'el sistema';

        // Agregar IP, user_agent y contexto
        $props = $activity->properties->toArray();
        $props['ip'] = request()->ip();
        $props['user_agent'] = request()->userAgent();
        $props['realizado_por'] = $user?->name ?? 'Sistema';
        $activity->properties = collect($props);

        $fechaInicio = $this->fecha_inicio ? $this->fecha_inicio->format('d/m/Y') : 'N/A';
        $fechaFin = $this->fecha_fin ? $this->fecha_fin->format('d/m/Y') : 'N/A';
        $estado = $this->is_active ? 'Activo (abierto)' : 'Inactivo (cerrado)';

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Creación de período';
                $activity->description = "Se configuró un nuevo período de entrega: {$this->mes} {$this->axo}."
                    . " Descripción: " . ($this->descripcion ?? 'Sin descripción') . "."
                    . " Vigencia: del {$fechaInicio} al {$fechaFin}. Estado: {$estado}."
                    . " Creado por: {$userNombre}.";
                break;

            case 'updated':
                $activity->log_name = 'Actualización de período';
                $original = $this->getOriginal();
                $nombreOriginal = ($original['mes'] ?? 'Sin mes') . ' ' . ($original['axo'] ?? 'Sin año');
                $nombreNuevo = $this->mes . ' ' . $this->axo;

                $cambiosTexto = $this->buildCambiosTexto($activity);

                $activity->description = "Se modificó el período \"{$nombreOriginal}\""
                    . ($nombreOriginal !== $nombreNuevo ? " (ahora \"{$nombreNuevo}\")" : "") . "."
                    . " Cambios: {$cambiosTexto}."
                    . " Modificado por: {$userNombre}.";
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de período';
                $activity->description = "Se eliminó el período \"{$this->mes} {$this->axo}\"."
                    . " Descripción: " . ($this->descripcion ?? 'Sin descripción') . "."
                    . " Eliminado por: {$userNombre}.";
                break;
        }
    }

    /**
     * Construye texto legible de los cambios old→new
     */
    private function buildCambiosTexto(Activity $activity): string
    {
        $old = $activity->properties['old'] ?? [];
        $attributes = $activity->properties['attributes'] ?? [];

        $labels = [
            'mes_numero'  => 'Mes número',
            'mes'         => 'Mes',
            'axo'         => 'Año',
            'descripcion' => 'Descripción',
            'fecha_inicio' => 'Fecha inicio',
            'fecha_fin'   => 'Fecha fin',
            'is_active'   => 'Estado',
        ];

        $cambios = [];
        foreach ($attributes as $campo => $nuevoValor) {
            $valorAnterior = $old[$campo] ?? 'vacío';
            $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));

            if ($campo === 'is_active') {
                $valorAnterior = $valorAnterior ? 'Activo' : 'Inactivo';
                $nuevoValor = $nuevoValor ? 'Activo' : 'Inactivo';
            }

            $cambios[] = "{$label}: \"{$valorAnterior}\" → \"{$nuevoValor}\"";
        }

        return implode('; ', $cambios) ?: 'Sin cambios relevantes';
    }

    // ── Accessors ────────────────────────────────────────────

    public function getFechaInicioDmaAttribute(): ?string
    {
        return $this->fecha_inicio
            ? $this->fecha_inicio->format('d-m-Y')
            : null;
    }

    public function getFechaFinDmaAttribute(): ?string
    {
        return $this->fecha_fin
            ? $this->fecha_fin->format('d-m-Y')
            : null;
    }

    public function getMesNombreAttribute()
    {
        return $this->mes ?? 'Desconocido';
    }
    public function getAxoMesAttribute(): string
    {
        return sprintf(
            '%02d%02d',
            $this->axo,
            $this->mes_numero
        );
    }

    // ── Relationships ────────────────────────────────────────

    public function periodosEntes()
    {
        return $this->hasMany(PeriodoEnte::class, 'periodo_id');
    }
}
