<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
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

        // ─────────────────────────────────────────────
        // 1. INICIO DE SESIÓN
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
                    'usuario_id'   => $user->id,
                    'usuario_nombre' => $user->name,
                    'cuenta'       => $user->email,
                    'rol'          => $rolNombre,
                    'origen'       => $origen,
                    'ente'         => $enteNombre,
                ],
                $user
            );
        });

        // ─────────────────────────────────────────────
        // 2. USUARIOS — Creación
        // ─────────────────────────────────────────────
        User::created(function (User $user) {
            $creadoPor = Auth::user();
            $creadoPorNombre = $creadoPor ? "\"{$creadoPor->name}\" ({$creadoPor->email})" : 'el sistema';
            $enteNombre = $user->ente?->nombre ?? 'Sin ente';
            $rolNombre = $user->roles->first()?->name ?? 'Sin rol asignado';

            $desc = "Se registró un nuevo usuario en el sistema."
                  . " Nombre: \"{$user->name}\". Cuenta de acceso: {$user->email}."
                  . " Rol asignado: {$rolNombre}. Ente/Dependencia: {$enteNombre}."
                  . " Creado por: {$creadoPorNombre}.";

            ActivityLogger::log(
                'Creación de usuario',
                $desc,
                $user,
                [
                    'usuario_creado_id'     => $user->id,
                    'usuario_creado_nombre' => $user->name,
                    'cuenta'                => $user->email,
                    'rol_asignado'          => $rolNombre,
                    'ente'                  => $enteNombre,
                    'creado_por'            => $creadoPor?->name ?? 'Sistema',
                ]
            );
        });

        // ─────────────────────────────────────────────
        // 2b. USUARIOS — Actualización
        // ─────────────────────────────────────────────
        User::updated(function (User $user) {
            $changes = $user->getChanges();
            // Ignorar cambios internos (timestamps, tokens)
            $ignorar = ['updated_at', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'current_team_id', 'profile_photo_path'];
            $cambiosRelevantes = array_diff_key($changes, array_flip($ignorar));
            if (empty($cambiosRelevantes)) {
                return;
            }

            $modificadoPor = Auth::user();
            $modificadoPorNombre = $modificadoPor ? "\"{$modificadoPor->name}\" ({$modificadoPor->email})" : 'el sistema';

            // Construir lista legible de cambios
            $detalleCambios = [];
            $original = $user->getOriginal();
            foreach ($cambiosRelevantes as $campo => $nuevoValor) {
                if ($campo === 'password') {
                    $detalleCambios[] = "Contraseña: (actualizada)";
                    continue;
                }
                $valorAnterior = $original[$campo] ?? 'vacío';
                if ($campo === 'is_active') {
                    $valorAnterior = $valorAnterior ? 'Activo' : 'Inactivo';
                    $nuevoValor = $nuevoValor ? 'Activo' : 'Inactivo';
                }
                $detalleCambios[] = ucfirst(str_replace('_', ' ', $campo)) . ": \"{$valorAnterior}\" → \"{$nuevoValor}\"";
            }
            $listaCambios = implode('; ', $detalleCambios);

            // Determinar acción específica
            $logName = 'Actualización de usuario';
            if (isset($cambiosRelevantes['is_active'])) {
                $status = $user->is_active ? 'ACTIVADO' : 'DESACTIVADO';
                $logName = $user->is_active ? 'Activación de usuario' : 'Desactivación de usuario';
                $desc = "Se {$status} la cuenta del usuario \"{$user->name}\" (cuenta: {$user->email})."
                      . " Ente: " . ($user->ente?->nombre ?? 'N/A') . "."
                      . " Acción realizada por: {$modificadoPorNombre}.";
            } else {
                $desc = "Se modificaron los datos del usuario \"{$user->name}\" (cuenta: {$user->email})."
                      . " Campos actualizados → {$listaCambios}."
                      . " Modificado por: {$modificadoPorNombre}.";
            }

            ActivityLogger::log(
                $logName,
                $desc,
                $user,
                [
                    'usuario_afectado'  => $user->name,
                    'cuenta'            => $user->email,
                    'ente'              => $user->ente?->nombre ?? 'N/A',
                    'cambios_detalle'   => $detalleCambios,
                    'valores_anteriores' => array_intersect_key($original, $cambiosRelevantes),
                    'valores_nuevos'    => $cambiosRelevantes,
                    'modificado_por'    => $modificadoPor?->name ?? 'Sistema',
                ]
            );
        });

        // ─────────────────────────────────────────────
        // 2c. USUARIOS — Eliminación
        // ─────────────────────────────────────────────
        User::deleted(function (User $user) {
            $eliminadoPor = Auth::user();
            $eliminadoPorNombre = $eliminadoPor ? "\"{$eliminadoPor->name}\" ({$eliminadoPor->email})" : 'el sistema';
            $rolNombre = $user->roles->first()?->name ?? 'Sin rol';
            $enteNombre = $user->ente?->nombre ?? 'Sin ente';

            $desc = "Se eliminó permanentemente el usuario \"{$user->name}\" (cuenta: {$user->email})."
                  . " Rol: {$rolNombre}. Ente: {$enteNombre}."
                  . " Eliminado por: {$eliminadoPorNombre}.";

            ActivityLogger::log(
                'Eliminación de usuario',
                $desc,
                $user,
                [
                    'usuario_eliminado'  => $user->name,
                    'cuenta'             => $user->email,
                    'rol'                => $rolNombre,
                    'ente'               => $enteNombre,
                    'eliminado_por'      => $eliminadoPor?->name ?? 'Sistema',
                ]
            );
        });

        // ─────────────────────────────────────────────
        // 3. DOCUMENTOS — Carga
        // ─────────────────────────────────────────────
        ArchivoDocumentoRecibido::created(function (ArchivoDocumentoRecibido $archivo) {
            $enteNombre = $archivo->ente?->nombre ?? 'N/A';
            $subidoPor = Auth::user();
            $subidoPorNombre = $subidoPor ? "\"{$subidoPor->name}\" ({$subidoPor->email})" : 'el sistema';

            // Información del período y documento
            $docRecibido = $archivo->documentoRecibido;
            $periodoInfo = '';
            $categoriaInfo = '';
            if ($docRecibido) {
                $periodo = $docRecibido->periodo ?? null;
                if ($periodo) {
                    $periodoInfo = " Período: {$periodo->mes} {$periodo->axo}.";
                }
            }

            $tipoRecepcion = $archivo->tipo_recepcion ?? 'N/A';
            $observaciones = $archivo->observaciones_ente ? " Observaciones del ente: \"{$archivo->observaciones_ente}\"." : '';

            $desc = "Se cargó el archivo \"{$archivo->nombre}\" al sistema."
                  . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                  . " Tipo de recepción: {$tipoRecepcion}."
                  . " Subido por: {$subidoPorNombre}.{$observaciones}";

            ActivityLogger::log(
                'Carga de documento',
                $desc,
                $archivo,
                [
                    'archivo_nombre'    => $archivo->nombre,
                    'ente'              => $enteNombre,
                    'periodo'           => $periodoInfo ?: 'N/A',
                    'tipo_recepcion'    => $tipoRecepcion,
                    'observaciones'     => $archivo->observaciones_ente,
                    'subido_por'        => $subidoPor?->name ?? 'Sistema',
                ]
            );
        });

        // ─────────────────────────────────────────────
        // 3b. DOCUMENTOS — Cambio de Estado (Revisión/Aprobación/Rechazo)
        // ─────────────────────────────────────────────
        ArchivoDocumentoRecibido::updated(function (ArchivoDocumentoRecibido $archivo) {
            $changes = $archivo->getChanges();
            if (!isset($changes['estado_id'])) {
                return;
            }

            $enteNombre = $archivo->ente?->nombre ?? 'N/A';
            $estadoNombre = $archivo->estado?->nombre ?? 'Desconocido';
            $revisadoPor = Auth::user();
            $revisadoPorNombre = $revisadoPor ? "\"{$revisadoPor->name}\" ({$revisadoPor->email})" : 'el sistema';
            $rolRevisor = $revisadoPor?->roles->first()?->name ?? 'N/A';

            // Período del documento
            $docRecibido = $archivo->documentoRecibido;
            $periodoInfo = '';
            if ($docRecibido) {
                $periodo = $docRecibido->periodo ?? null;
                if ($periodo) {
                    $periodoInfo = " Período: {$periodo->mes} {$periodo->axo}.";
                }
            }

            $estadoAnterior = $archivo->getOriginal('estado_id');
            $estadoAnteriorNombre = \App\Models\Estado::find($estadoAnterior)?->nombre ?? 'Sin estado';

            if ($archivo->estado_id == 3) {
                // APROBACIÓN
                $desc = "Se APROBÓ el documento \"{$archivo->nombre}\"."
                      . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                      . " Estado anterior: {$estadoAnteriorNombre} → Nuevo estado: {$estadoNombre}."
                      . " Revisado y aprobado por: {$revisadoPorNombre} (rol: {$rolRevisor}).";

                if ($archivo->observaciones_revisor) {
                    $desc .= " Observaciones del revisor: \"{$archivo->observaciones_revisor}\".";
                }

                ActivityLogger::log('Aprobación de documento', $desc, $archivo, [
                    'archivo_nombre'        => $archivo->nombre,
                    'ente'                  => $enteNombre,
                    'periodo'               => $periodoInfo ?: 'N/A',
                    'estado_anterior'       => $estadoAnteriorNombre,
                    'estado_nuevo'          => $estadoNombre,
                    'revisado_por'          => $revisadoPor?->name ?? 'Sistema',
                    'rol_revisor'           => $rolRevisor,
                    'observaciones_revisor' => $archivo->observaciones_revisor,
                ]);

            } elseif ($archivo->estado_id == 4) {
                // RECHAZO
                $causa = $archivo->causaRechazo?->descripcion ?? 'Sin causa especificada';
                $observaciones = $archivo->observaciones_revisor ? " Observaciones del revisor: \"{$archivo->observaciones_revisor}\"." : '';
                $reenvio = $archivo->autorizado_reenviar ? 'Sí' : 'No';

                $desc = "Se RECHAZÓ el documento \"{$archivo->nombre}\"."
                      . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                      . " Estado anterior: {$estadoAnteriorNombre} → Nuevo estado: {$estadoNombre}."
                      . " Causa del rechazo: \"{$causa}\"."
                      . " Reenvío autorizado: {$reenvio}."
                      . " Revisado por: {$revisadoPorNombre} (rol: {$rolRevisor}).{$observaciones}";

                ActivityLogger::log('Rechazo de documento', $desc, $archivo, [
                    'archivo_nombre'        => $archivo->nombre,
                    'ente'                  => $enteNombre,
                    'periodo'               => $periodoInfo ?: 'N/A',
                    'estado_anterior'       => $estadoAnteriorNombre,
                    'estado_nuevo'          => $estadoNombre,
                    'causa_rechazo'         => $causa,
                    'autorizado_reenviar'   => $reenvio,
                    'revisado_por'          => $revisadoPor?->name ?? 'Sistema',
                    'rol_revisor'           => $rolRevisor,
                    'observaciones_revisor' => $archivo->observaciones_revisor,
                ]);

            } else {
                // OTRO CAMBIO DE ESTADO
                $desc = "Se cambió el estado del documento \"{$archivo->nombre}\"."
                      . " Ente obligado: {$enteNombre}.{$periodoInfo}"
                      . " Estado anterior: {$estadoAnteriorNombre} → Nuevo estado: {$estadoNombre}."
                      . " Actualizado por: {$revisadoPorNombre}.";

                ActivityLogger::log('Actualización de documento', $desc, $archivo, [
                    'archivo_nombre'  => $archivo->nombre,
                    'ente'            => $enteNombre,
                    'periodo'         => $periodoInfo ?: 'N/A',
                    'estado_anterior' => $estadoAnteriorNombre,
                    'estado_nuevo'    => $estadoNombre,
                    'actualizado_por' => $revisadoPor?->name ?? 'Sistema',
                ]);
            }
        });

        // ─────────────────────────────────────────────
        // 4. AVISOS INSTITUCIONALES
        // ─────────────────────────────────────────────
        Aviso::created(function (Aviso $aviso) {
            $creadoPor = Auth::user();
            $creadoPorNombre = $creadoPor ? "\"{$creadoPor->name}\" ({$creadoPor->email})" : 'el sistema';
            $tipoAviso = $aviso->tipo_aviso ?? 'General';
            $fechaPub = $aviso->fecha_publicacion ? $aviso->fecha_publicacion->format('d/m/Y H:i') : 'Inmediata';
            $fechaExp = $aviso->fecha_expiracion ? $aviso->fecha_expiracion->format('d/m/Y H:i') : 'Sin fecha de expiración';
            $estado = $aviso->activo ? 'Activo' : 'Inactivo';

            $desc = "Se publicó un nuevo aviso institucional: \"{$aviso->titulo}\"."
                  . " Tipo: {$tipoAviso}. Estado: {$estado}."
                  . " Fecha de publicación: {$fechaPub}. Expiración: {$fechaExp}."
                  . " Creado por: {$creadoPorNombre}.";

            ActivityLogger::log(
                'Creación de aviso',
                $desc,
                $aviso,
                [
                    'aviso_titulo'         => $aviso->titulo,
                    'tipo_aviso'           => $tipoAviso,
                    'estado'               => $estado,
                    'fecha_publicacion'    => $fechaPub,
                    'fecha_expiracion'     => $fechaExp,
                    'contenido_resumen'    => \Illuminate\Support\Str::limit(strip_tags($aviso->texto), 150),
                    'creado_por'           => $creadoPor?->name ?? 'Sistema',
                ]
            );
        });

        Aviso::updated(function (Aviso $aviso) {
            $changes = $aviso->getChanges();
            $ignorar = ['updated_at'];
            $cambiosRelevantes = array_diff_key($changes, array_flip($ignorar));
            if (empty($cambiosRelevantes)) {
                return;
            }

            $modificadoPor = Auth::user();
            $modificadoPorNombre = $modificadoPor ? "\"{$modificadoPor->name}\" ({$modificadoPor->email})" : 'el sistema';

            $detalleCambios = [];
            $original = $aviso->getOriginal();
            foreach ($cambiosRelevantes as $campo => $nuevoValor) {
                $valorAnterior = $original[$campo] ?? 'vacío';
                if ($campo === 'activo') {
                    $valorAnterior = $valorAnterior ? 'Activo' : 'Inactivo';
                    $nuevoValor = $nuevoValor ? 'Activo' : 'Inactivo';
                }
                $detalleCambios[] = ucfirst(str_replace('_', ' ', $campo)) . ": \"{$valorAnterior}\" → \"{$nuevoValor}\"";
            }
            $listaCambios = implode('; ', $detalleCambios);

            $desc = "Se actualizó el aviso institucional: \"{$aviso->titulo}\"."
                  . " Cambios realizados → {$listaCambios}."
                  . " Modificado por: {$modificadoPorNombre}.";

            ActivityLogger::log(
                'Actualización de aviso',
                $desc,
                $aviso,
                [
                    'aviso_titulo'    => $aviso->titulo,
                    'cambios_detalle' => $detalleCambios,
                    'modificado_por'  => $modificadoPor?->name ?? 'Sistema',
                ]
            );
        });

        // ─────────────────────────────────────────────
        // 5. PERÍODOS DE ENTREGA
        // ─────────────────────────────────────────────
        Periodo::created(function (Periodo $periodo) {
            $creadoPor = Auth::user();
            $creadoPorNombre = $creadoPor ? "\"{$creadoPor->name}\" ({$creadoPor->email})" : 'el sistema';
            $fechaInicio = $periodo->fecha_inicio ? $periodo->fecha_inicio->format('d/m/Y') : 'N/A';
            $fechaFin = $periodo->fecha_fin ? $periodo->fecha_fin->format('d/m/Y') : 'N/A';
            $estado = $periodo->is_active ? 'Activo (abierto)' : 'Inactivo (cerrado)';

            $desc = "Se configuró un nuevo período de entrega: {$periodo->mes} {$periodo->axo}."
                  . " Descripción: " . ($periodo->descripcion ?? 'Sin descripción') . "."
                  . " Vigencia: del {$fechaInicio} al {$fechaFin}. Estado: {$estado}."
                  . " Creado por: {$creadoPorNombre}.";

            ActivityLogger::log(
                'Creación de período',
                $desc,
                $periodo,
                [
                    'periodo'       => "{$periodo->mes} {$periodo->axo}",
                    'descripcion'   => $periodo->descripcion,
                    'fecha_inicio'  => $fechaInicio,
                    'fecha_fin'     => $fechaFin,
                    'estado'        => $estado,
                    'creado_por'    => $creadoPor?->name ?? 'Sistema',
                ]
            );
        });

        Periodo::updated(function (Periodo $periodo) {
            $changes = $periodo->getChanges();
            $ignorar = ['updated_at'];
            $cambiosRelevantes = array_diff_key($changes, array_flip($ignorar));
            if (empty($cambiosRelevantes)) {
                return;
            }

            $modificadoPor = Auth::user();
            $modificadoPorNombre = $modificadoPor ? "\"{$modificadoPor->name}\" ({$modificadoPor->email})" : 'el sistema';

            $detalleCambios = [];
            $original = $periodo->getOriginal();
            foreach ($cambiosRelevantes as $campo => $nuevoValor) {
                $valorAnterior = $original[$campo] ?? 'vacío';
                if ($campo === 'is_active') {
                    $valorAnterior = $valorAnterior ? 'Activo' : 'Inactivo';
                    $nuevoValor = $nuevoValor ? 'Activo' : 'Inactivo';
                }
                $detalleCambios[] = ucfirst(str_replace('_', ' ', $campo)) . ": \"{$valorAnterior}\" → \"{$nuevoValor}\"";
            }
            $listaCambios = implode('; ', $detalleCambios);

            $desc = "Se modificó el período {$periodo->mes} {$periodo->axo}."
                  . " Cambios realizados → {$listaCambios}."
                  . " Modificado por: {$modificadoPorNombre}.";

            ActivityLogger::log(
                'Actualización de período',
                $desc,
                $periodo,
                [
                    'periodo'         => "{$periodo->mes} {$periodo->axo}",
                    'cambios_detalle' => $detalleCambios,
                    'modificado_por'  => $modificadoPor?->name ?? 'Sistema',
                ]
            );
        });
    }
}
