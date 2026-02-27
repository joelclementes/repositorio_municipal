<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpParser\Comment\Doc;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TiposEntesSeeder::class,
            EnteSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            EstadoSeeder::class,
            CategoriasDocumentoSeeder::class,
            SubcategoriasDocumentoSeeder::class,
            DocumentoSeeder::class,
        ]);
    }
}
