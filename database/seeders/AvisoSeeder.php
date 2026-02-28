<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aviso;

class AvisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $avisos = [
            [
                'titulo' => 'Mantenimiento programado',
                'tipo_aviso' => 'Aviso',
                'texto' => 'El sistema estará en mantenimiento el próximo sábado de 2:00 a 4:00 AM.',
                'activo' => true,
                'archivo' => null,
                'fecha_publicacion' => now(),
                'fecha_expiracion' => now()->addDays(7),
                'creado_por' => 2,
            ],
            [
                'titulo' => 'Invitación a la reunión mensual',
                'tipo_aviso' => 'Invitación',
                'texto' => 'Te invitamos a la reunión mensual el próximo lunes a las 10:00 AM en la sala de conferencias.',
                'activo' => true,
                'archivo' => null,
                'fecha_publicacion' => now(),
                'fecha_expiracion' => now()->addDays(30),
                'creado_por' => 2,
            ],
            [
                'titulo' => 'Exhorto para actualizar contraseñas',
                'tipo_aviso' => 'Exhorto',
                'texto' => 'Se exhorta a todos los usuarios a actualizar sus contraseñas cada 90 días para mantener la seguridad.',
                'activo' => true,
                'archivo' => null,
                'fecha_publicacion' => now(),
                'fecha_expiracion' => now()->addDays(90),
                'creado_por' => 2,
            ],
        ];

        foreach ($avisos as $aviso) {
            Aviso::create($aviso);
        }
    }
}
