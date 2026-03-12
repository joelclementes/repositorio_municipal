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
                    'name' => 'Tesorero - Acajete',
                    'email' => 'tacajete',
                    'password' => bcrypt('123456789'),
                    'ente_id' => 1,
                ],
                'role' => 'Tesorero'
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

        foreach ($usuarios as $usuario) {
            $user = User::create($usuario['user_data']);
            $user->assignRole($usuario['role']);
        }
    }
}
