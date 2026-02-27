<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TiposEnte;

class TiposEntesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos_entes = [
            ['nombre' => 'Municipio',],
            ['nombre' => 'Comisión',],
            ['nombre' => 'Instituto',],
            ['nombre' => 'Foro',],
        ];

        foreach ($tipos_entes as $tipo_ente) {
            TiposEnte::create($tipo_ente);
        }
    }
}
