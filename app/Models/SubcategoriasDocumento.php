<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class SubcategoriasDocumento extends Model
{
    use LogsActivity;

    protected $fillable = [
        'clave',
        'nombre',
        'categoria_id',
    ];
    
    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['clave', 'nombre', 'categoria_id'])
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

        $categoriaNombre = $this->categoria?->nombre ?? 'N/A';

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Creación de subcategoría';
                $activity->description = "Se registró una nueva subcategoría de documento: \"{$this->nombre}\" (clave: {$this->clave})."
                    . " Categoría asociada: {$categoriaNombre}."
                    . " Creado por: {$userNombre}.";
                break;

            case 'updated':
                $activity->log_name = 'Actualización de subcategoría';
                $original = $this->getOriginal();
                $nombreOriginal = $original['nombre'] ?? $this->nombre;
                $cambiosTexto = $this->buildSubcategoriaCambiosTexto($activity);

                $activity->description = "Se modificaron los datos de la subcategoría \"{$nombreOriginal}\"."
                    . " Cambios: {$cambiosTexto}."
                    . " Modificado por: {$userNombre}.";
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de subcategoría';
                $activity->description = "Se eliminó permanentemente la subcategoría \"{$this->nombre}\" (clave: {$this->clave})."
                    . " Eliminado por: {$userNombre}.";
                break;
        }
    }

    /**
     * Construye texto legible de los cambios de la subcategoría
     */
    private function buildSubcategoriaCambiosTexto(Activity $activity): string
    {
        $old = $activity->properties['old'] ?? [];
        $attributes = $activity->properties['attributes'] ?? [];

        $labels = [
            'clave'        => 'Clave',
            'nombre'       => 'Nombre',
            'categoria_id' => 'Categoría',
        ];

        $cambios = [];
        foreach ($attributes as $campo => $nuevoValor) {
            if ($campo === 'updated_at') continue;
            $valorAnterior = $old[$campo] ?? 'vacío';
            $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));

            if ($campo === 'categoria_id') {
                $valorAnterior = CategoriasDocumento::find($valorAnterior)?->nombre ?? 'Sin categoría';
                $nuevoValor = CategoriasDocumento::find($nuevoValor)?->nombre ?? 'Sin categoría';
            }

            $cambios[] = "{$label}: \"{$valorAnterior}\" → \"{$nuevoValor}\"";
        }

        return implode('; ', $cambios) ?: 'Sin cambios relevantes';
    }

    // ── Relationships ────────────────────────────────────────

    public function categoria()
    {
        return $this->belongsTo(CategoriasDocumento::class, 'categoria_id');
    }
}
