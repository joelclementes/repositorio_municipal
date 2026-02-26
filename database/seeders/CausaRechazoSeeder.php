<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CausaRechazo;

class CausaRechazoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $causas = [
            [
                'descripcion' => 'Documentación incompleta',
            ],
            [
                'descripcion' => 'Datos incorrectos',
            ],
            [
                'descripcion' => 'Fuera de plazo',
            ],
        ];

        foreach ($causas as $causa) {
            CausaRechazo::create($causa);
        }
    }
}
