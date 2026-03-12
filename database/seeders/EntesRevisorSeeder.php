<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EnteRevisor;

class EntesRevisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entesrevisor = [
            [
                'revisor_id' => '4',
                'ente_id' => '1',
            ],
            [
                'revisor_id' => '4',
                'ente_id' => '2',
            ],
            [
                'revisor_id' => '4',
                'ente_id' => '3',
            ],
            [
                'revisor_id' => '5',
                'ente_id' => '4',
            ],
            [
                'revisor_id' => '5',
                'ente_id' => '5',
            ],
            [
                'revisor_id' => '5',
                'ente_id' => '6',
            ],
        ];

        foreach ($entesrevisor as $er) {
            EnteRevisor::create($er);
        }
    }
}
