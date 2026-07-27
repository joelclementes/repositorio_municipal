<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class CategoriasDocumento extends Model
{
    use LogsActivity;

    protected $fillable = [
        'clave',
        'nombre',
        'roles_permitidos',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['clave', 'nombre', 'roles_permitidos'])
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

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Creación de categoría de documento';
                $activity->description = "Se registró una nueva categoría de documento: \"{$this->nombre}\" (clave: {$this->clave})."
                    . " Creado por: {$userNombre}.";
                break;

            case 'updated':
                $activity->log_name = 'Actualización de categoría de documento';
                $original = $this->getOriginal();
                $nombreOriginal = $original['nombre'] ?? $this->nombre;
                $cambiosTexto = $this->buildCategoriaCambiosTexto($activity);

                $activity->description = "Se modificó la categoría de documento \"{$nombreOriginal}\"."
                    . " Cambios: {$cambiosTexto}."
                    . " Modificado por: {$userNombre}.";
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de categoría de documento';
                $activity->description = "Se eliminó permanentemente la categoría de documento \"{$this->nombre}\" (clave: {$this->clave})."
                    . " Eliminado por: {$userNombre}.";
                break;
        }
    }

    /**
     * Construye texto legible de los cambios de la categoría
     */
    private function buildCategoriaCambiosTexto(Activity $activity): string
    {
        $old = $activity->properties['old'] ?? [];
        $attributes = $activity->properties['attributes'] ?? [];

        $labels = [
            'clave'            => 'Clave',
            'nombre'           => 'Nombre',
            'roles_permitidos' => 'Roles permitidos',
        ];

        $cambios = [];
        foreach ($attributes as $campo => $nuevoValor) {
            if ($campo === 'updated_at') continue;
            $valorAnterior = $old[$campo] ?? 'vacío';
            $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));

            // Si es un array/json, formatearlo legible
            if (is_array($valorAnterior)) {
                $valorAnterior = json_encode($valorAnterior);
            }
            if (is_array($nuevoValor)) {
                $nuevoValor = json_encode($nuevoValor);
            }

            $cambios[] = "{$label}: \"{$valorAnterior}\" → \"{$nuevoValor}\"";
        }

        return implode('; ', $cambios) ?: 'Sin cambios relevantes';
    }

    // ── Relationships ────────────────────────────────────────

    public function subcategorias()
    {
        return $this->hasMany(SubcategoriasDocumento::class, 'categoria_id');
    }
}
