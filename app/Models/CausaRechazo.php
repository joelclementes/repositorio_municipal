<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class CausaRechazo extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'causas_rechazo';

    protected $fillable = [
        'descripcion',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['descripcion'])
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
                $activity->log_name = 'Creación de causa de rechazo';
                $activity->description = "Se registró una nueva causa de rechazo: \"{$this->descripcion}\"."
                    . " Creado por: {$userNombre}.";
                break;

            case 'updated':
                $activity->log_name = 'Actualización de causa de rechazo';
                $original = $this->getOriginal();
                $descOriginal = $original['descripcion'] ?? $this->descripcion;

                $activity->description = "Se modificó la causa de rechazo \"{$descOriginal}\" (ahora \"{$this->descripcion}\")."
                    . " Modificado por: {$userNombre}.";
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de causa de rechazo';
                $activity->description = "Se eliminó permanentemente la causa de rechazo \"{$this->descripcion}\"."
                    . " Eliminado por: {$userNombre}.";
                break;
        }
    }

    // ── Relationships ────────────────────────────────────────

    public function archivos()
    {
        return $this->hasMany(ArchivoDocumentoRecibido::class, 'causas_rechazo_id');
    }
}
