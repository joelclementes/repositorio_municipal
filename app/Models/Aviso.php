<?php
// app/Models/Aviso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Aviso extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'titulo',
        'tipo_aviso',
        'texto',
        'activo',
        'url',
        'archivo',
        'fecha_publicacion',
        'fecha_expiracion',
        'creado_por',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'fecha_expiracion' => 'datetime',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['titulo', 'tipo_aviso', 'activo', 'fecha_publicacion', 'fecha_expiracion', 'url', 'archivo'])
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

        $tipoAviso = $this->tipo_aviso ?? 'General';
        $estado = $this->activo ? 'Activo' : 'Inactivo';

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Creación de aviso';
                $fechaPub = $this->fecha_publicacion ? $this->fecha_publicacion->format('d/m/Y H:i') : 'Inmediata';
                $fechaExp = $this->fecha_expiracion ? $this->fecha_expiracion->format('d/m/Y H:i') : 'Sin fecha de expiración';

                $activity->description = "Se publicó un nuevo aviso institucional: \"{$this->titulo}\"."
                    . " Tipo: {$tipoAviso}. Estado: {$estado}."
                    . " Fecha de publicación: {$fechaPub}. Expiración: {$fechaExp}."
                    . " Contenido: " . Str::limit(strip_tags($this->texto), 100) . "."
                    . " Creado por: {$userNombre}.";
                break;

            case 'updated':
                $activity->log_name = 'Actualización de aviso';
                $original = $this->getOriginal();
                $tituloOriginal = $original['titulo'] ?? $this->titulo;
                $cambiosTexto = $this->buildAvisoCambiosTexto($activity);

                $activity->description = "Se actualizó el aviso institucional \"{$tituloOriginal}\"."
                    . " Cambios: {$cambiosTexto}."
                    . " Modificado por: {$userNombre}.";
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de aviso';
                $activity->description = "Se eliminó el aviso institucional \"{$this->titulo}\"."
                    . " Tipo: {$tipoAviso}."
                    . " Eliminado por: {$userNombre}.";
                break;
        }
    }

    /**
     * Construye texto legible de los cambios del aviso
     */
    private function buildAvisoCambiosTexto(Activity $activity): string
    {
        $old = $activity->properties['old'] ?? [];
        $attributes = $activity->properties['attributes'] ?? [];

        $labels = [
            'titulo'            => 'Título',
            'tipo_aviso'        => 'Tipo de aviso',
            'activo'            => 'Estado',
            'fecha_publicacion' => 'Fecha de publicación',
            'fecha_expiracion'  => 'Fecha de expiración',
            'url'               => 'URL',
            'archivo'           => 'Archivo adjunto',
        ];

        $cambios = [];
        foreach ($attributes as $campo => $nuevoValor) {
            $valorAnterior = $old[$campo] ?? 'vacío';
            $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));

            if ($campo === 'activo') {
                $valorAnterior = $valorAnterior ? 'Activo' : 'Inactivo';
                $nuevoValor = $nuevoValor ? 'Activo' : 'Inactivo';
            }

            $cambios[] = "{$label}: \"{$valorAnterior}\" → \"{$nuevoValor}\"";
        }

        return implode('; ', $cambios) ?: 'Sin cambios relevantes';
    }

    // ── Relationships ────────────────────────────────────────

    /**
     * Relación con la tabla pivote aviso_entes
     */
    public function avisoEntes(): HasMany
    {
        return $this->hasMany(AvisoEnte::class);
    }

    /**
     * Relación con los entes a través de la tabla pivote
     */
    public function entes()
    {
        return $this->belongsToMany(Ente::class, 'aviso_entes')
            ->withPivot(['estado_envio', 'fecha_envio', 'fecha_lectura'])
            ->withTimestamps();
    }

    /**
     * Usuario que creó el aviso
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
