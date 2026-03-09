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
        $periodos = [
            [
                'periodo_data' => [
                    'nombre' => 'Enero 2026',
                    'fecha_inicio' => '2026-01-01',
                    'fecha_fin' => '2026-01-25',
                    'is_active' => true,
                ],
            ],
            [
                'periodo_data' => [
                    'nombre' => 'Febrero 2026',
                    'fecha_inicio' => '2026-02-01',
                    'fecha_fin' => '2026-02-25',
                    'is_active' => true,
                ],
            ],
        ];

        foreach ($periodos as $periodo) {
            Periodo::create($periodo['periodo_data']);
        }
    }
}
