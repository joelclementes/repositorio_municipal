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
                    'mes' => 'ABRIL',
                    'axo' => '2026',
                    'descripcion' => 'ABRIL de 2026',
                    'fecha_inicio' => '2026-04-01',
                    'fecha_fin' => '2026-04-30',
                    'is_active' => true,
                ],
            ],
        ];

        foreach ($periodos as $periodo) {
            Periodo::create($periodo['periodo_data']);
        }
    }
}
