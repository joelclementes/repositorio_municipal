<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class Documento extends Model
{
    use LogsActivity;

    protected $table = 'documentos';

    protected $fillable = [
        'clave',
        'nombre',
        'subcategoria_id',
        'regla_presentacion',
        'formato',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['clave', 'nombre', 'subcategoria_id', 'regla_presentacion', 'formato'])
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

        $subcategoriaNombre = $this->subcategoria?->nombre ?? 'N/A';

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Creación de tipo de documento';
                $activity->description = "Se registró un nuevo tipo de documento: \"{$this->nombre}\" (clave: {$this->clave})."
                    . " Subcategoría: {$subcategoriaNombre}."
                    . " Creado por: {$userNombre}.";
                break;

            case 'updated':
                $activity->log_name = 'Actualización de tipo de documento';
                $original = $this->getOriginal();
                $nombreOriginal = $original['nombre'] ?? $this->nombre;
                $cambiosTexto = $this->buildDocumentoCambiosTexto($activity);

                $activity->description = "Se modificaron los datos del tipo de documento \"{$nombreOriginal}\"."
                    . " Cambios: {$cambiosTexto}."
                    . " Modificado por: {$userNombre}.";
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de tipo de documento';
                $activity->description = "Se eliminó permanentemente el tipo de documento \"{$this->nombre}\" (clave: {$this->clave})."
                    . " Eliminado por: {$userNombre}.";
                break;
        }
    }

    /**
     * Construye texto legible de los cambios del documento catálogo
     */
    private function buildDocumentoCambiosTexto(Activity $activity): string
    {
        $old = $activity->properties['old'] ?? [];
        $attributes = $activity->properties['attributes'] ?? [];

        $labels = [
            'clave'              => 'Clave',
            'nombre'             => 'Nombre',
            'subcategoria_id'    => 'Subcategoría',
            'regla_presentacion' => 'Regla de presentación',
            'formato'            => 'Formato',
        ];

        $cambios = [];
        foreach ($attributes as $campo => $nuevoValor) {
            if ($campo === 'updated_at') continue;
            $valorAnterior = $old[$campo] ?? 'vacío';
            $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));

            if ($campo === 'subcategoria_id') {
                $valorAnterior = SubcategoriasDocumento::find($valorAnterior)?->nombre ?? 'Sin subcategoría';
                $nuevoValor = SubcategoriasDocumento::find($nuevoValor)?->nombre ?? 'Sin subcategoría';
            }

            if ($campo === 'regla_presentacion') {
                $opciones = self::reglasPresentacionOptions();
                $valorAnterior = $opciones[$valorAnterior] ?? $valorAnterior;
                $nuevoValor = $opciones[$nuevoValor] ?? $nuevoValor;
            }

            $cambios[] = "{$label}: \"{$valorAnterior}\" → \"{$nuevoValor}\"";
        }

        return implode('; ', $cambios) ?: 'Sin cambios relevantes';
    }

    // ── Relationships ────────────────────────────────────────

    public function subcategoria()
    {
        return $this->belongsTo(SubcategoriasDocumento::class, 'subcategoria_id');
    }

    public static function reglasPresentacionOptions(): array
    {
        return config('documentos.reglas_presentacion', []);
    }

    public function getReglaPresentacionEtiquetaAttribute(): string
    {
        $opciones = self::reglasPresentacionOptions();

        return $opciones[$this->regla_presentacion] ?? $this->regla_presentacion;
    }
}
