<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolSuperUsusario = Role::create(['name' => 'SuperUsuario']);
        $rolAdmin = Role::create(['name' => 'Administrador']);
        $rolRevisor = Role::create(['name' => 'Revisor']);
        $rolEnte = Role::create(['name' => 'EnteObligado']);

        Permission::create(['name' => 'configurar'])->syncRoles([$rolSuperUsusario]);

        // Administrará catálogos, asignación de entes a los revisores, supervisará a los revisores, creará avisos.
        Permission::create(['name' => 'administrar'])->syncRoles([$rolAdmin,$rolSuperUsusario]);

        Permission::create(['name' => 'registrar'])->syncRoles([$rolEnte,$rolSuperUsusario]);

        Permission::create(['name' => 'revisar-documentos'])->syncRoles([$rolRevisor,$rolSuperUsusario]);

        Permission::create(['name' => 'generar-reportes'])->syncRoles([$rolAdmin,$rolRevisor,$rolSuperUsusario]);
    }
}
