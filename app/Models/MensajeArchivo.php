<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class MensajeArchivo extends Model
{
    use LogsActivity;

    protected $table = 'mensaje_archivos';

    protected $fillable = [
        'mensaje_id',
        'nombre_original',
        'ruta',
        'mime_type',
        'extension',
        'size',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre_original', 'mensaje_id', 'size'])
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

        $asunto = $this->mensaje?->asunto ?? 'Sin asunto';
        $sizeKb = round($this->size / 1024, 1);

        switch ($eventName) {
            case 'created':
                $activity->log_name = 'Adjunto de mensaje';
                $activity->description = "Se adjuntó el archivo \"{$this->nombre_original}\" ({$sizeKb} KB) al mensaje con asunto: \"{$asunto}\" por el usuario {$userNombre}.";
                break;
        }
    }

    // ── Relationships ────────────────────────────────────────

    public function mensaje()
    {
        return $this->belongsTo(Mensaje::class);
    }
}
