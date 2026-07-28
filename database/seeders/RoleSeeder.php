<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolSuperUsuario = Role::firstOrCreate(['name' => 'SuperUsuario']);
        $rolAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        $rolRevisor = Role::firstOrCreate(['name' => 'Revisor']);
        $rolTesorero = Role::firstOrCreate(['name' => 'Tesorero']);
        $rolTesoreroOrganoDescentralizado = Role::firstOrCreate(['name' => 'Tesorero Organo Descentralizado']);
        $rolDirectorObrasPublicas = Role::firstOrCreate(['name' => 'Director Obras Publicas']);
        $rolContralor = Role::firstOrCreate(['name' => 'Contralor']);

        Permission::firstOrCreate(['name' => 'configurar'])
            ->syncRoles([$rolSuperUsuario]);

        Permission::firstOrCreate(['name' => 'administrar'])
            ->syncRoles([$rolAdmin, $rolSuperUsuario]);

        Permission::firstOrCreate(['name' => 'registrar'])
            ->syncRoles([
                $rolTesorero,
                $rolTesoreroOrganoDescentralizado,
                $rolDirectorObrasPublicas,
                $rolContralor,
                $rolSuperUsuario,
            ]);

        Permission::firstOrCreate(['name' => 'revisar-documentos'])
            ->syncRoles([$rolRevisor, $rolSuperUsuario, $rolAdmin]);

        Permission::firstOrCreate(['name' => 'generar-reportes'])
            ->syncRoles([$rolAdmin, $rolSuperUsuario]);

        Permission::firstOrCreate(['name' => 'usar-mensajeria'])
            ->syncRoles([
                $rolAdmin,
                $rolSuperUsuario,
                $rolTesorero,
                $rolTesoreroOrganoDescentralizado,
                $rolDirectorObrasPublicas,
                $rolContralor,
            ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}

// namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

// class RoleSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         $rolSuperUsusario = Role::create(['name' => 'SuperUsuario']);
//         $rolAdmin = Role::create(['name' => 'Administrador']);
//         $rolRevisor = Role::create(['name' => 'Revisor']);
//         // $rolEnte = Role::create(['name' => 'EnteObligado']);
//         $rolTesorero = Role::create(['name' => 'Tesorero']);
//         $rolTesoreroOrganoDescentralizado = Role::create(['name' => 'Tesorero Organo Descentralizado']);
//         $rolDirectorObrasPublicas = Role::create(['name' => 'Director Obras Publicas']);
//         $rolContralor = Role::create(['name' => 'Contralor']);

//         Permission::create(['name' => 'configurar'])->syncRoles([$rolSuperUsusario]);

//         // Administrará catálogos, asignación de entes a los revisores, supervisará a los revisores, creará avisos.
//         Permission::create(['name' => 'administrar'])->syncRoles([$rolAdmin,$rolSuperUsusario]);

//         Permission::create(['name' => 'registrar'])->syncRoles([$rolTesorero,$rolTesoreroOrganoDescentralizado,$rolDirectorObrasPublicas,$rolContralor,$rolSuperUsusario]);

//         Permission::create(['name' => 'revisar-documentos'])->syncRoles([$rolRevisor,$rolSuperUsusario,$rolAdmin]);

//         Permission::create(['name' => 'generar-reportes'])->syncRoles([$rolAdmin,$rolSuperUsusario]);

//         Permission::create(['name' => 'usar-mensajeria'])->syncRoles([$rolAdmin,$rolSuperUsusario,$rolTesorero,$rolTesoreroOrganoDescentralizado,$rolDirectorObrasPublicas,$rolContralor]);
//     }
// }
