<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Periodo;

class PeriodosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $anio = 2026;

        $meses = [
            // 1 => 'enero',
            // 2 => 'febrero',
            // 3 => 'marzo',
            // 4 => 'abril',
            // 5 => 'mayo',
            // 6 => 'junio',
            7 => 'julio',
            // 8 => 'agosto',
            // 9 => 'septiembre',
            // 10 => 'octubre',
            // 11 => 'noviembre',
            // 12 => 'diciembre',
        ];

        foreach ($meses as $numero => $nombre) {
            Periodo::create([
                'mes_numero' => $numero,
                'mes' => $nombre,
                'axo' => $anio,
                'descripcion' => ucfirst($nombre) . ' ' . $anio,
                'fecha_inicio' => now()->setDate($anio, $numero, 1)->startOfMonth()->toDateString(),
                'fecha_fin' => now()->setDate($anio, $numero, 1)->endOfMonth()->toDateString(),
                'is_active' => true,
            ]);
        }
        return;
        // Agregar periodo para 2025 para pruebas
        $anio2025 = 2025;
        foreach ($meses as $numero => $nombre) {
            Periodo::create([
                'mes_numero' => $numero,
                'mes' => $nombre,
                'axo' => $anio2025,
                'descripcion' => ucfirst($nombre) . ' ' . $anio2025,
                'fecha_inicio' => now()->setDate($anio2025, $numero, 1)->startOfMonth()->toDateString(),
                'fecha_fin' => now()->setDate($anio2025, $numero, 1)->endOfMonth()->toDateString(),
                'is_active' => true,
            ]);
        }
        /* $periodos = [
            [
                'periodo_data' => [
                    'mes' => 'MARZO',
                    'axo' => '2026',
                    'descripcion' => 'MARZO de 2026',
                    'fecha_inicio' => '2026-03-01',
                    'fecha_fin' => '2026-03-30',
                    'is_active' => true,
                ],
            ],
            [
                'periodo_data' => [
                    'mes' => 'ABRIL',
                    'axo' => '2026',
                    'descripcion' => 'ABRIL de 2026',
                    'fecha_inicio' => '2026-04-01',
                    'fecha_fin' => '2026-04-30',
                    'is_active' => true,
                ],
            ],
            [
                'periodo_data' => [
                    'mes' => 'MAYO',
                    'axo' => '2026',
                    'descripcion' => 'MAYO de 2026',
                    'fecha_inicio' => '2026-05-01',
                    'fecha_fin' => '2026-05-30',
                    'is_active' => true,
                ],
            ],
        ];

        foreach ($periodos as $periodo) {
            Periodo::create($periodo['periodo_data']);
        } */
    }
}
