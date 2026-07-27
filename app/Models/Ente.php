<?php
// app/Models/Ente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

use App\Models\TiposEnte;
use App\Models\PeriodoEnte;


class Ente extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $appends = [
        'tipo_ente_nombre',
    ];

    protected $fillable = [
        'nombre',
        'tipos_entes_id',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'tipos_entes_id'])
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

        $tipoNombre = $this->tipoEnte?->nombre ?? 'Sin tipo';

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Creación de municipio/ente';
                $activity->description = "Se registró el organismo/municipio \"{$this->nombre}\"."
                    . " Tipo de ente: {$tipoNombre}."
                    . " Creado por: {$userNombre}.";
                break;

            case 'updated':
                $activity->log_name = 'Actualización de municipio/ente';
                $original = $this->getOriginal();
                $nombreOriginal = $original['nombre'] ?? $this->nombre;
                $cambiosTexto = $this->buildEnteCambiosTexto($activity);

                $activity->description = "Se modificaron los datos del organismo/municipio \"{$nombreOriginal}\""
                    . ($nombreOriginal !== $this->nombre ? " (ahora \"{$this->nombre}\")" : "") . "."
                    . " Cambios: {$cambiosTexto}."
                    . " Modificado por: {$userNombre}.";
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de municipio/ente';
                $activity->description = "Se eliminó permanentemente el organismo/municipio \"{$this->nombre}\"."
                    . " Tipo de ente: {$tipoNombre}."
                    . " Eliminado por: {$userNombre}.";
                break;
        }
    }

    /**
     * Construye texto legible de los cambios del ente
     */
    private function buildEnteCambiosTexto(Activity $activity): string
    {
        $old = $activity->properties['old'] ?? [];
        $attributes = $activity->properties['attributes'] ?? [];

        $labels = [
            'nombre'         => 'Nombre',
            'tipos_entes_id' => 'Tipo de ente',
        ];

        $cambios = [];
        foreach ($attributes as $campo => $nuevoValor) {
            if ($campo === 'updated_at') continue;
            $valorAnterior = $old[$campo] ?? 'vacío';
            $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));

            if ($campo === 'tipos_entes_id') {
                $valorAnterior = TiposEnte::find($valorAnterior)?->nombre ?? 'Sin tipo';
                $nuevoValor = TiposEnte::find($nuevoValor)?->nombre ?? 'Sin tipo';
            }

            $cambios[] = "{$label}: \"{$valorAnterior}\" → \"{$nuevoValor}\"";
        }

        return implode('; ', $cambios) ?: 'Sin cambios relevantes';
    }

    // ── Relationships ────────────────────────────────────────

    /**
     * Relación con el tipo de ente
     */
    public function tipoEnte(): BelongsTo
    {
        return $this->belongsTo(TiposEnte::class, 'tipos_entes_id');
    }

    /**
     * Relación con los usuarios (EntesObligados)
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación con los avisos a través de la tabla pivote
     */
    public function avisos()
    {
        return $this->belongsToMany(Aviso::class, 'aviso_entes')
            ->withPivot(['estado_envio', 'fecha_envio', 'fecha_lectura'])
            ->withTimestamps();
    }

    /**
     * Relación con la tabla pivote aviso_entes
     */
    public function avisoEntes(): HasMany
    {
        return $this->hasMany(AvisoEnte::class);
    }

    /**
     * Accesor para obtener el nombre del tipo de ente
     */
    public function getTipoEnteNombreAttribute()
    {
        return $this->tipoEnte ? $this->tipoEnte->nombre : 'Sin tipo';
    }

    public function periodosEntes()
    {
        return $this->hasMany(PeriodoEnte::class, 'ente_id');
    }
}
