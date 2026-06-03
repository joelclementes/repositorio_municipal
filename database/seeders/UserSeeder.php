<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'user_data' => [
                    'name' => 'Joel Clemente Serrano',
                    'email' => 'jclemente',
                    'password' => bcrypt('123456789'),
                ],
                'role' => 'SuperUsuario'
            ],
            [
                'user_data' => [
                    'name' => 'Lic. Juan Carlos Patiño',
                    'email' => 'jpatino',
                    'password' => bcrypt('123456789'),
                ],
                'role' => 'Administrador'
            ],
            [
                'user_data' => [
                    'name' => 'Mtra. Lorena Rivera Ruiz',
                    'email' => 'lrivera',
                    'password' => bcrypt('123456789'),
                    'ente_id' => 1,
                ],
                'role' => 'Revisor'
            ],
            [
                'user_data' => [
                    'name' => 'Mtra. Leticia Sedas Vargas',
                    'email' => 'lsedas',
                    'password' => bcrypt('123456789'),
                    'ente_id' => 1,
                ],
                'role' => 'Revisor'
            ],
        ];

        // Generar usuarios para todos los municipios (tipos_entes_id = 1)
        $municipios = \App\Models\Ente::where('tipos_entes_id', 1)->get();

        foreach ($municipios as $municipio) {
            // Limpiar el nombre: minúsculas, sin acentos ni espacios
            $cleanName = str_replace(
                ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', ' '],
                ['a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'n', ''],
                mb_strtolower($municipio->nombre, 'UTF-8')
            );
            $cleanName = preg_replace('/[^a-z0-9]/', '', $cleanName);

            // Tesorero
            $usuarios[] = [
                'user_data' => [
                    'name' => 'Tesorero - ' . $municipio->nombre,
                    'email' => 't' . $cleanName,
                    'password' => bcrypt('123456789'),
                    'ente_id' => $municipio->id,
                ],
                'role' => 'Tesorero'
            ];

            // Contralor
            $usuarios[] = [
                'user_data' => [
                    'name' => 'Contralor - ' . $municipio->nombre,
                    'email' => 'c' . $cleanName,
                    'password' => bcrypt('123456789'),
                    'ente_id' => $municipio->id,
                ],
                'role' => 'Contralor'
            ];

            // Director Obras Publicas
            $usuarios[] = [
                'user_data' => [
                    'name' => 'Director de Obras - ' . $municipio->nombre,
                    'email' => 'd' . $cleanName,
                    'password' => bcrypt('123456789'),
                    'ente_id' => $municipio->id,
                ],
                'role' => 'Director Obras Publicas'
            ];
        }

        foreach ($usuarios as $usuario) {
            $user = User::create($usuario['user_data']);
            $user->assignRole($usuario['role']);
        }
    }
}
