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
            ['descripcion' => 'Documento ilegible',],
            ['descripcion' => 'Documento no corresponde al solicitado',],
            ['descripcion' => 'Faltan firmas',],
            ['descripcion' => 'Las firmas no corresponden a los titulares',],
            ['descripcion' => 'Documento dañado y no se puede descargar',],
            ['descripcion' => 'A solicitud del Ayuntamiento',],
            ['descripcion' => 'Documento en blanco',],
            ['descripcion' => 'Documento de otro periodo (mes y/o año)',],
            ['descripcion' => 'Documento que no se debe presentar en este periodo',],
            ['descripcion' => 'Documentaciòn no dirigida al funcionario actual',],
            ['descripcion' => 'Documento incompleto',],
            ['descripcion' => 'Documento no aprobado en sesión de cabildo',],
            ['descripcion' => 'Este formato no forma parte de sus obligaciones a presentar',],
        ];

        foreach ($causas as $causa) {
            CausaRechazo::create($causa);
        }
    }
}
