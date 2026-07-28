<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class MensajeDestinatario extends Model
{
    use LogsActivity;

    protected $table = 'mensaje_destinatarios';

    protected $fillable = [
        'mensaje_id',
        'destinatario_id',
        'estado',
        'leido_at',
    ];

    protected $casts = [
        'leido_at' => 'datetime',
    ];

    // ── Activity Log Configuration ───────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['estado', 'leido_at'])
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

        $destinatarioNombre = $this->destinatario?->name ?? 'N/A';
        $asunto = $this->mensaje?->asunto ?? 'Sin asunto';
        $remitenteNombre = $this->mensaje?->remitente?->name ?? 'N/A';

        switch ($eventName) {
            case 'updated':
                $dirty = $this->getDirty();
                if (isset($dirty['estado']) && $this->estado === 'leido') {
                    $activity->log_name = 'Lectura de mensaje';
                    $activity->description = "El usuario \"{$destinatarioNombre}\" leyó el mensaje con asunto: \"{$asunto}\" (Enviado por: {$remitenteNombre}).";
                } else {
                    $activity->log_name = 'Actualización de recepción';
                    $activity->description = "Se actualizó el estado del mensaje para \"{$destinatarioNombre}\".";
                }
                break;
        }
    }

    // ── Relationships ────────────────────────────────────────

    public function mensaje()
    {
        return $this->belongsTo(Mensaje::class);
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }
}
