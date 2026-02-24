<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $usuarios = [
            [
                'user_data' => [
                    'name' => 'Joel Clemente Serrano',
                    'email' => 'joel@vigilancia.com',
                    'password' => bcrypt('123456789'),
                ],
                // 'role' => 'SuperUsuario'
            ],
        ];

        foreach ($usuarios as $usuario) {
            $user = User::create($usuario['user_data']);
            // $user->assignRole($usuario['role']);
        }
    }
}
