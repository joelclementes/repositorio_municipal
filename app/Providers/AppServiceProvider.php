<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
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

        // ─────────────────────────────────────────────
        // INICIO DE SESIÓN
        // (Este evento no es CRUD de modelo, se mantiene
        //  con ActivityLogger ya que LogsActivity no lo cubre)
        // ─────────────────────────────────────────────
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            $rolNombre = $user->roles->first()?->name ?? 'Sin rol';
            $enteNombre = $user->ente?->nombre ?? null;

            $origen = $user->hasAnyRole(['SuperUsuario', 'Administrador', 'Revisor'])
                ? 'Congreso del Estado'
                : ($enteNombre ? "Municipio de {$enteNombre}" : 'Sin ente asignado');

            $desc = "El usuario \"{$user->name}\" (cuenta: {$user->email}) inició sesión en el sistema."
                  . " Rol asignado: {$rolNombre}. Origen: {$origen}.";

            ActivityLogger::log(
                'Inicio de sesión',
                $desc,
                $user,
                [
                    'usuario_id'     => $user->id,
                    'usuario_nombre' => $user->name,
                    'cuenta'         => $user->email,
                    'rol'            => $rolNombre,
                    'origen'         => $origen,
                    'ente'           => $enteNombre,
                ],
                $user
            );
        });

        // ─────────────────────────────────────────────
        // NOTA: El registro de actividades para los modelos
        // (User, Periodo, ArchivoDocumentoRecibido, Aviso)
        // ahora se maneja automáticamente con el trait
        // Spatie\Activitylog\Traits\LogsActivity en cada modelo.
        // Ver: app/Models/*.php
        // ─────────────────────────────────────────────
    }
}
