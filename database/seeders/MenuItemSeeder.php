<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuOperation;
use App\Models\MenuSubItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear ítem de menú para Usuarios
        $usuariosItem = MenuItem::create([
            'item_id' => 'usuarios',
            'label' => 'Usuarios',
            'icon' => 'users',
            'href' => '/usuarios',
            'enabled' => true,
            'order' => 1,
        ]);

        // Crear operaciones CRUD para Usuarios
        foreach (['create', 'read', 'update', 'delete'] as $operation) {
            MenuOperation::create([
                'menu_item_id' => $usuariosItem->id,
                'operation' => $operation,
                'enabled' => true,
            ]);
        }

        // Crear subítem para Lista de Usuarios
        MenuSubItem::create([
            'menu_item_id' => $usuariosItem->id,
            'subitem_id' => 'lista-usuarios',
            'label' => 'Lista de Usuarios',
            'href' => '/usuarios',
            'operation' => 'read',
            'order' => 1,
        ]);

        // Crear ítem de menú para Empleados
        $empleadosItem = MenuItem::create([
            'item_id' => 'empleados',
            'label' => 'Empleados',
            'icon' => 'briefcase',
            'href' => '/empleados',
            'enabled' => true,
            'order' => 2,
        ]);

        // Crear operaciones CRUD para Empleados
        foreach (['create', 'read', 'update', 'delete'] as $operation) {
            MenuOperation::create([
                'menu_item_id' => $empleadosItem->id,
                'operation' => $operation,
                'enabled' => true,
            ]);
        }

        // Crear subítem para Lista de Empleados
        MenuSubItem::create([
            'menu_item_id' => $empleadosItem->id,
            'subitem_id' => 'lista-empleados',
            'label' => 'Lista de Empleados',
            'href' => '/empleados',
            'operation' => 'read',
            'order' => 1,
        ]);
    }
}
