<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoriasDocumento;

class CategoriasDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $categorias = [
            [
                'user_data' => [
                    'clave' => 'EF1',
                    'nombre' => 'Estado de Actividades',
                ],
            ],
            [
                'user_data' => [
                    'clave' => 'EF2',
                    'nombre' => 'Estado de Situación Financiera',
                ],
            ],
        ];

        foreach ($categorias as $categoria) {
            $categoriaModel = CategoriasDocumento::create($categoria['user_data']);
        }
    }
}
