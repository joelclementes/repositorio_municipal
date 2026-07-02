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
        // Obtener usuarios sembrados
        $super = User::where('email', 'jclemente')->first();
        $admin = User::where('email', 'jpatino')->first();
        $tesorero = User::where('email', 'tacajete')->first();
        $revisor = User::where('email', 'lrivera')->first();

        $logs = [
            // Actividades del SuperUsuario (Congreso)
            [
                'log_name' => 'Inicio de sesión',
                'description' => 'El usuario Joel Clemente Serrano (jclemente) ingresó al sistema',
                'causer_type' => $super ? User::class : null,
                'causer_id' => $super?->id,
                'properties' => [
                    'ip' => '192.168.1.10',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'created_at' => Carbon::now()->subDays(5)->setHour(9)->setMinute(15),
            ],
            [
                'log_name' => 'Creación de usuario',
                'description' => "Se creó el usuario Tesorero - Acajete con usuario 'tacajete' asignado al ente Acajete",
                'causer_type' => $super ? User::class : null,
                'causer_id' => $super?->id,
                'properties' => [
                    'ip' => '192.168.1.10',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'created_at' => Carbon::now()->subDays(5)->setHour(9)->setMinute(30),
            ],
            [
                'log_name' => 'Creación de usuario',
                'description' => "Se creó el usuario Mtra. Lorena Rivera Ruiz con usuario 'lrivera' asignado al ente Acajete",
                'causer_type' => $super ? User::class : null,
                'causer_id' => $super?->id,
                'properties' => [
                    'ip' => '192.168.1.10',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'created_at' => Carbon::now()->subDays(5)->setHour(9)->setMinute(45),
            ],

            // Actividades del Administrador (Congreso)
            [
                'log_name' => 'Inicio de sesión',
                'description' => 'El usuario Lic. Juan Carlos Patiño (jpatino) ingresó al sistema',
                'causer_type' => $admin ? User::class : null,
                'causer_id' => $admin?->id,
                'properties' => [
                    'ip' => '192.168.1.12',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'created_at' => Carbon::now()->subDays(3)->setHour(10)->setMinute(0),
            ],
            [
                'log_name' => 'Creación de aviso',
                'description' => "Se publicó el aviso institucional: 'Aviso Importante: Entrega de Cuenta Pública 2026'",
                'causer_type' => $admin ? User::class : null,
                'causer_id' => $admin?->id,
                'properties' => [
                    'ip' => '192.168.1.12',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'created_at' => Carbon::now()->subDays(3)->setHour(10)->setMinute(15),
            ],
            [
                'log_name' => 'Creación de período',
                'description' => "Se configuró el período 'Junio' / 2026",
                'causer_type' => $admin ? User::class : null,
                'causer_id' => $admin?->id,
                'properties' => [
                    'ip' => '192.168.1.12',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'created_at' => Carbon::now()->subDays(2)->setHour(11)->setMinute(30),
            ],

            // Actividades del Tesorero (Municipio - Acajete)
            [
                'log_name' => 'Inicio de sesión',
                'description' => 'El usuario Tesorero - Acajete (tacajete) ingresó al sistema',
                'causer_type' => $tesorero ? User::class : null,
                'causer_id' => $tesorero?->id,
                'properties' => [
                    'ip' => '189.203.45.67',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/119.0'
                ],
                'created_at' => Carbon::now()->subDays(1)->setHour(9)->setMinute(5),
            ],
            [
                'log_name' => 'Carga de documento',
                'description' => "Se subió el archivo 'Balanza de Comprobación Junio.pdf' del ente Acajete",
                'causer_type' => $tesorero ? User::class : null,
                'causer_id' => $tesorero?->id,
                'properties' => [
                    'ip' => '189.203.45.67',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/119.0'
                ],
                'created_at' => Carbon::now()->subDays(1)->setHour(9)->setMinute(22),
            ],
            [
                'log_name' => 'Carga de documento',
                'description' => "Se subió el archivo 'Estado de Situación Financiera.xlsx' del ente Acajete",
                'causer_type' => $tesorero ? User::class : null,
                'causer_id' => $tesorero?->id,
                'properties' => [
                    'ip' => '189.203.45.67',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/119.0'
                ],
                'created_at' => Carbon::now()->subDays(1)->setHour(9)->setMinute(40),
            ],

            // Actividades del Revisor (Congreso - revisa Acajete)
            [
                'log_name' => 'Inicio de sesión',
                'description' => 'El usuario Mtra. Lorena Rivera Ruiz (lrivera) ingresó al sistema',
                'causer_type' => $revisor ? User::class : null,
                'causer_id' => $revisor?->id,
                'properties' => [
                    'ip' => '192.168.1.15',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'created_at' => Carbon::now()->subHour(2),
            ],
            [
                'log_name' => 'Aprobación de documento',
                'description' => "Se aprobó el documento 'Balanza de Comprobación Junio.pdf' del ente Acajete",
                'causer_type' => $revisor ? User::class : null,
                'causer_id' => $revisor?->id,
                'properties' => [
                    'ip' => '192.168.1.15',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'created_at' => Carbon::now()->subHour(1),
            ],
            [
                'log_name' => 'Rechazo de documento',
                'description' => "Se rechazó el documento 'Estado de Situación Financiera.xlsx' del ente Acajete por causa: Falta firma del tesorero y sello digital",
                'causer_type' => $revisor ? User::class : null,
                'causer_id' => $revisor?->id,
                'properties' => [
                    'ip' => '192.168.1.15',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'created_at' => Carbon::now()->subMinutes(30),
            ],
        ];

        foreach ($logs as $log) {
            Activity::create($log);
        }
    }
}
