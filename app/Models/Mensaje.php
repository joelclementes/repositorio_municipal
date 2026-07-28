<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class Mensaje extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'mensaje_raiz_id',
        'mensaje_padre_id',
        'remitente_id',
        'asunto',
        'cuerpo',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['remitente_id', 'asunto', 'cuerpo', 'mensaje_raiz_id', 'mensaje_padre_id'])
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
                if ($this->mensaje_padre_id) {
                    $activity->log_name = 'Respuesta de mensaje';
                    $activity->description = "El usuario {$userNombre} respondió al mensaje con asunto: \"{$this->asunto}\".";
                } else {
                    $activity->log_name = 'Envío de mensaje';
                    $activity->description = "El usuario {$userNombre} envió un nuevo mensaje con asunto: \"{$this->asunto}\".";
                }
                break;

            case 'deleted':
                $activity->log_name = 'Eliminación de mensaje';
                $activity->description = "El usuario {$userNombre} eliminó el mensaje con asunto: \"{$this->asunto}\".";
                break;
        }
    }

    // ── Relationships ────────────────────────────────────────

    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function destinatarios()
    {
        return $this->hasMany(MensajeDestinatario::class);
    }

    public function archivos()
    {
        return $this->hasMany(MensajeArchivo::class);
    }

    public function respuestas()
    {
        return $this->hasMany(Mensaje::class, 'mensaje_padre_id');
    }

    public function hilo()
    {
        return $this->hasMany(Mensaje::class, 'mensaje_raiz_id');
    }

    public function mensajeRaiz()
    {
        return $this->belongsTo(Mensaje::class, 'mensaje_raiz_id');
    }
}
