<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar logs anteriores
        Activity::truncate();

        // Obtener usuarios sembrados
        $super = User::where('email', 'jclemente')->first();
        $admin = User::where('email', 'jpatino')->first();
        $tesorero = User::where('email', 'tacajete')->first();
        $revisor = User::where('email', 'lrivera')->first();

        $logs = [
            // 1. Inicio de sesión
            [
                'log_name' => 'Inicio de sesión',
                'description' => 'El usuario Joel Clemente Serrano (jclemente) ingresó al sistema. Rol asignado: SuperUsuario. Origen: Congreso del Estado.',
                'causer_type' => $super ? User::class : null,
                'causer_id' => $super?->id,
                'properties' => [
                    'ip' => '192.168.1.10',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'realizado_por' => 'Joel Clemente Serrano'
                ],
                'created_at' => Carbon::now()->subDays(5)->setHour(9)->setMinute(15),
            ],

            // 2. Creación de usuario (ejemplo de LogsActivity created)
            [
                'log_name' => 'Creación de usuario',
                'description' => 'Se registró un nuevo usuario en el sistema. Nombre: "Tesorero - Acajete". Cuenta de acceso: tacajete. Rol asignado: Tesorero. Ente/Dependencia: Acajete. Creado por: "Joel Clemente Serrano" (jclemente).',
                'causer_type' => $super ? User::class : null,
                'causer_id' => $super?->id,
                'properties' => [
                    'ip' => '192.168.1.10',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'realizado_por' => 'Joel Clemente Serrano',
                    'attributes' => [
                        'name' => 'Tesorero - Acajete',
                        'email' => 'tacajete',
                        'is_active' => true,
                        'ente_id' => 1
                    ]
                ],
                'created_at' => Carbon::now()->subDays(5)->setHour(9)->setMinute(30),
            ],

            // 3. Creación de periodo (ejemplo de LogsActivity created)
            [
                'log_name' => 'Creación de período',
                'description' => 'Se configuró un nuevo período de entrega: Mayo 2026. Descripción: Mayo 2026. Vigencia: del 01/05/2026 al 31/05/2026. Estado: Activo (abierto). Creado por: "Lic. Juan Carlos Patiño" (jpatino).',
                'causer_type' => $admin ? User::class : null,
                'causer_id' => $admin?->id,
                'properties' => [
                    'ip' => '192.168.1.12',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'realizado_por' => 'Lic. Juan Carlos Patiño',
                    'attributes' => [
                        'mes_numero' => 5,
                        'mes' => 'mayo',
                        'axo' => 2026,
                        'descripcion' => 'Mayo 2026',
                        'fecha_inicio' => '2026-05-01',
                        'fecha_fin' => '2026-05-31',
                        'is_active' => true
                    ]
                ],
                'created_at' => Carbon::now()->subDays(4)->setHour(10)->setMinute(15),
            ],

            // 4. Modificación de periodo (ejemplo de LogsActivity updated con old vs new!)
            [
                'log_name' => 'Actualización de período',
                'description' => 'Se modificó el período "Mayo 2026" (ahora llamado "Mayor 2026"). Cambios: Descripción: "Mayo 2026" → "Mayor 2026". Modificado por: "Lic. Juan Carlos Patiño" (jpatino).',
                'causer_type' => $admin ? User::class : null,
                'causer_id' => $admin?->id,
                'properties' => [
                    'ip' => '192.168.1.12',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'realizado_por' => 'Lic. Juan Carlos Patiño',
                    'attributes' => [
                        'descripcion' => 'Mayor 2026'
                    ],
                    'old' => [
                        'descripcion' => 'Mayo 2026'
                    ]
                ],
                'created_at' => Carbon::now()->subDays(3)->setHour(14)->setMinute(22),
            ],

            // 5. Carga de documento (ejemplo de LogsActivity created)
            [
                'log_name' => 'Carga de documento',
                'description' => 'Se cargó el archivo "Balanza_Comprobacion_Mayo.pdf" al sistema. Ente obligado: Acajete. Período: mayo 2026. Tipo de recepción: Digital. Subido por: "Tesorero - Acajete" (tacajete).',
                'causer_type' => $tesorero ? User::class : null,
                'causer_id' => $tesorero?->id,
                'properties' => [
                    'ip' => '189.203.45.67',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/119.0',
                    'realizado_por' => 'Tesorero - Acajete',
                    'attributes' => [
                        'nombre' => 'Balanza_Comprobacion_Mayo.pdf',
                        'tipo_recepcion' => 'Digital',
                        'ente_id' => 1,
                        'documento_recibido_id' => 10
                    ]
                ],
                'created_at' => Carbon::now()->subDays(2)->setHour(9)->setMinute(40),
            ],

            // 6. Aprobación de documento (ejemplo de LogsActivity updated con old vs new!)
            [
                'log_name' => 'Aprobación de documento',
                'description' => 'Se APROBÓ el documento "Balanza_Comprobacion_Mayo.pdf". Ente obligado: Acajete. Período: mayo 2026. Estado anterior: "Enviado" → Nuevo estado: "Aprobado". Revisado y aprobado por: "Mtra. Lorena Rivera Ruiz" (lrivera) (rol: Revisor). Observaciones del revisor: "Cumple con las firmas requeridas."',
                'causer_type' => $revisor ? User::class : null,
                'causer_id' => $revisor?->id,
                'properties' => [
                    'ip' => '192.168.1.15',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'realizado_por' => 'Mtra. Lorena Rivera Ruiz',
                    'attributes' => [
                        'estado_id' => 3,
                        'observaciones_revisor' => 'Cumple con las firmas requeridas.'
                    ],
                    'old' => [
                        'estado_id' => 2,
                        'observaciones_revisor' => null
                    ]
                ],
                'created_at' => Carbon::now()->subDays(1)->setHour(11)->setMinute(15),
            ],

            // 7. Modificación de usuario (ejemplo de LogsActivity updated con old vs new!)
            [
                'log_name' => 'Desactivación de usuario',
                'description' => 'Se DESACTIVÓ la cuenta del usuario "Tesorero - Acajete" (cuenta: tacajete). Ente: Acajete. Acción realizada por: "Joel Clemente Serrano" (jclemente).',
                'causer_type' => $super ? User::class : null,
                'causer_id' => $super?->id,
                'properties' => [
                    'ip' => '192.168.1.10',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'realizado_por' => 'Joel Clemente Serrano',
                    'attributes' => [
                        'is_active' => false
                    ],
                    'old' => [
                        'is_active' => true
                    ]
                ],
                'created_at' => Carbon::now()->subMinutes(45),
            ]
        ];

        foreach ($logs as $log) {
            Activity::create($log);
        }
    }
}
