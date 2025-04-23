<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetear roles y permisos en caché
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos para usuarios
        Permission::create(['name' => 'usuarios.create']);
        Permission::create(['name' => 'usuarios.read']);
        Permission::create(['name' => 'usuarios.update']);
        Permission::create(['name' => 'usuarios.delete']);

        // Crear permisos para empleados
        Permission::create(['name' => 'empleados.create']);
        Permission::create(['name' => 'empleados.read']);
        Permission::create(['name' => 'empleados.update']);
        Permission::create(['name' => 'empleados.delete']);

        // Crear permisos para administrar el menú
        Permission::create(['name' => 'menu.manage']);

        // Crear rol de administrador
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Crear rol de usuario
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'usuarios.read',
            'empleados.read',
        ]);

        // Crear rol de editor
        $editorRole = Role::create(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'usuarios.read',
            'usuarios.update',
            'empleados.read',
            'empleados.update',
        ]);
    }
}
