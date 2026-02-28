<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AvisoEnte;

class AvisosEntesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $avisos_entes = [
            [
                'aviso_id' => '1',
                'ente_id' => '1',
                'estado_envio' => 'pendiente',
                'fecha_envio' => now(),
                'fecha_lectura' => null,
                'enviado_por' => '1',
                'enviado_por' => 2,
            ],
            [
                'aviso_id' => '1',
                'ente_id' => '2',
                'estado_envio' => 'pendiente',
                'fecha_envio' => now(),
                'fecha_lectura' => null,
                'enviado_por' => '1',
                'enviado_por' => 2,
            ],
            [
                'aviso_id' => '2',
                'ente_id' => '1',
                'estado_envio' => 'pendiente',
                'fecha_envio' => now(),
                'fecha_lectura' => null,
                'enviado_por' => '1',
                'enviado_por' => 2,
            ],
            [
                'aviso_id' => '2',
                'ente_id' => '3',
                'estado_envio' => 'pendiente',
                'fecha_envio' => now(),
                'fecha_lectura' => null,
                'enviado_por' => '1',
                'enviado_por' => 2,
            ],
        ];

        foreach ($avisos_entes as $aviso_ente) {
            AvisoEnte::create($aviso_ente);
        }
    }
}
