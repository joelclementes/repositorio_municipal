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
                'categoria_Data' => [
                    'clave' => 'IEFM',
                    'nombre' => 'Integración del Estado Financiero Mensual',
                ],
            ],
            [
                'categoria_Data' => [
                    'clave' => 'IEFMOP',
                    'nombre' => 'Integración del Estado Financiero Mensual de Obra Pública',
                ],
            ],
            [
                'categoria_Data' => [
                    'clave' => 'OM',
                    'nombre' => 'Obligaciones Municipales',
                ],
            ],
            [
                'categoria_Data' => [
                    'clave' => 'CI',
                    'nombre' => 'Contralores Internos',
                ],
            ],
            [
                'categoria_Data' => [
                    'clave' => 'IMM',
                    'nombre' => 'Institutos Municipales de las Mujeres',
                ],
            ],
        ];

        foreach ($categorias as $categoria) {
            CategoriasDocumento::create($categoria['categoria_Data']);
        }
    }
}
