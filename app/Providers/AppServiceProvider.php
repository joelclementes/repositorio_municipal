<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Models\User;
use App\Models\ArchivoDocumentoRecibido;
use App\Models\Aviso;
use App\Models\Periodo;
use App\Services\ActivityLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(125);

        // 1. Log logins
        Event::listen(Login::class, function (Login $event) {
            ActivityLogger::log(
                'Inicio de sesión',
                "El usuario {$event->user->name} ({$event->user->email}) ingresó al sistema",
                $event->user,
                null,
                $event->user
            );
        });

        // 2. Observe User model
        User::created(function (User $user) {
            ActivityLogger::log(
                'Creación de usuario',
                "Se creó el usuario {$user->name} con usuario '{$user->email}'" . ($user->ente ? " asignado al ente {$user->ente->nombre}" : ""),
                $user
            );
        });

        User::updated(function (User $user) {
            $changes = $user->getChanges();
            if (isset($changes['is_active'])) {
                $status = $user->is_active ? 'Activo' : 'Inactivo';
                ActivityLogger::log(
                    'Actualización de usuario',
                    "Se cambió el estado del usuario {$user->name} a {$status}",
                    $user,
                    $changes
                );
            } else {
                ActivityLogger::log(
                    'Actualización de usuario',
                    "Se actualizaron los datos del usuario {$user->name}",
                    $user,
                    $changes
                );
            }
        });

        User::deleted(function (User $user) {
            ActivityLogger::log(
                'Eliminación de usuario',
                "Se eliminó el usuario {$user->name} ({$user->email})",
                $user
            );
        });

        // 3. Observe ArchivoDocumentoRecibido model
        ArchivoDocumentoRecibido::created(function (ArchivoDocumentoRecibido $archivo) {
            $enteNombre = $archivo->ente?->nombre ?? 'N/A';
            ActivityLogger::log(
                'Carga de documento',
                "Se subió el archivo '{$archivo->nombre}' del ente {$enteNombre}",
                $archivo
            );
        });

        ArchivoDocumentoRecibido::updated(function (ArchivoDocumentoRecibido $archivo) {
            $changes = $archivo->getChanges();
            if (isset($changes['estado_id'])) {
                $enteNombre = $archivo->ente?->nombre ?? 'N/A';
                $estadoNombre = $archivo->estado?->nombre ?? 'Desconocido';
                
                $action = 'Actualización de documento';
                $desc = "Se cambió el estado del documento '{$archivo->nombre}' del ente {$enteNombre} a {$estadoNombre}";

                if ($archivo->estado_id == 3) {
                    $action = 'Aprobación de documento';
                    $desc = "Se aprobó el documento '{$archivo->nombre}' del ente {$enteNombre}";
                } elseif ($archivo->estado_id == 4) {
                    $action = 'Rechazo de documento';
                    $causa = $archivo->causaRechazo?->descripcion ?? '';
                    $desc = "Se rechazó el documento '{$archivo->nombre}' del ente {$enteNombre}" . ($causa ? " por causa: {$causa}" : "");
                }

                ActivityLogger::log($action, $desc, $archivo, $changes);
            }
        });

        // 4. Observe Aviso model
        Aviso::created(function (Aviso $aviso) {
            ActivityLogger::log(
                'Creación de aviso',
                "Se publicó el aviso institucional: '{$aviso->titulo}'",
                $aviso
            );
        });

        Aviso::updated(function (Aviso $aviso) {
            ActivityLogger::log(
                'Actualización de aviso',
                "Se actualizó el aviso institucional: '{$aviso->titulo}'",
                $aviso,
                $aviso->getChanges()
            );
        });

        // 5. Observe Periodo model
        Periodo::created(function (Periodo $periodo) {
            ActivityLogger::log(
                'Creación de período',
                "Se configuró el período '{$periodo->mes}' / {$periodo->axo}",
                $periodo
            );
        });

        Periodo::updated(function (Periodo $periodo) {
            ActivityLogger::log(
                'Actualización de período',
                "Se modificó el período '{$periodo->mes}' / {$periodo->axo}",
                $periodo,
                $periodo->getChanges()
            );
        });
    }
}

