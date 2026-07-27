<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class PeriodoEnte extends Model
{
    use LogsActivity;

    protected $table = 'periodos_entes';

    protected $fillable = [
        'ente_id',
        'periodo_id',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'is_active' => 'boolean',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fecha_inicio', 'fecha_fin', 'is_active', 'ente_id', 'periodo_id'])
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

        $enteNombre = $this->ente?->nombre ?? 'N/A';
        $periodoDesc = $this->periodo?->descripcion ?? ($this->periodo ? "{$this->periodo->mes} {$this->periodo->axo}" : 'N/A');

        $fechaInicio = $this->fecha_inicio ? $this->fecha_inicio->format('d/m/Y') : 'N/A';
        $fechaFin = $this->fecha_fin ? $this->fecha_fin->format('d/m/Y') : 'N/A';
        $estado = $this->is_active ? 'Activo' : 'Inactivo';

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Asignación de período a municipio';
                $activity->description = "Se asignó el período \"{$periodoDesc}\" al organismo/municipio \"{$enteNombre}\"."
                    . " Vigencia: del {$fechaInicio} al {$fechaFin}. Estado: {$estado}."
                    . " Realizado por: {$userNombre}.";
                break;

            case 'updated':
                $activity->log_name = 'Actualización de período de municipio';
                $cambiosTexto = $this->buildCambiosTexto($activity);

                $activity->description = "Se modificaron las fechas/estado del período \"{$periodoDesc}\" para el municipio \"{$enteNombre}\"."
                    . " Cambios: {$cambiosTexto}."
                    . " Modificado por: {$userNombre}.";
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de período de municipio';
                $activity->description = "Se eliminó la asignación del período \"{$periodoDesc}\" del municipio \"{$enteNombre}\"."
                    . " Realizado por: {$userNombre}.";
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
            'fecha_inicio' => 'Fecha inicio',
            'fecha_fin'    => 'Fecha fin',
            'is_active'    => 'Estado',
        ];

        $cambios = [];
        foreach ($attributes as $campo => $nuevoValor) {
            if ($campo === 'updated_at') continue;
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

    // ── Relationships ────────────────────────────────────────

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function ente()
    {
        return $this->belongsTo(Ente::class, 'ente_id');
    }
}
