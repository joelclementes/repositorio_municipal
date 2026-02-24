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
        ];

        foreach ($usuarios as $usuario) {
            $user = User::create($usuario['user_data']);
            $user->assignRole($usuario['role']);
        }
    }
}
