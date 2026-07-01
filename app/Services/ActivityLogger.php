<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Registra un log utilizando el paquete Spatie Activitylog.
     * Inyecta de forma automática la IP y el User Agent en las propiedades del log.
     */
    public static function log(string $logName, string $description, $subject = null, ?array $properties = null, $causer = null): void
    {
        $causer = $causer ?? Auth::user();
        
        $props = $properties ?? [];
        $props['ip'] = Request::ip();
        $props['user_agent'] = Request::userAgent();

        $activity = activity($logName)
            ->causedBy($causer)
            ->withProperties($props);

        if ($subject) {
            $activity->performedOn($subject);
        }

        $activity->log($description);
    }
}
