<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\MenuSubItem;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Obtener todos los elementos del menú con sus operaciones y subelementos
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->roles()->first();

        if (!$role) {
            return response()->json(['message' => 'Usuario sin rol asignado'], 403);
        }

        $menuItems = MenuItem::all();
        $result = [];

        foreach ($menuItems as $menuItem) {
            // Construir los nombres de permiso
            $base = $menuItem->item_id;

            $operations = [
                'create' => $user->hasPermissionTo("$base.create"),
                'read' => $user->hasPermissionTo("$base.read"),
                'update' => $user->hasPermissionTo("$base.update"),
                'delete' => $user->hasPermissionTo("$base.delete"),
            ];

            $subItems = $menuItem->subItems()->get()->map(function ($subItem) {
                return [
                    'id' => $subItem->id,
                    'label' => $subItem->label,
                    'href' => $subItem->href,
                    'operation' => $subItem->operation
                ];
            });

            $result[] = [
                'id' => $menuItem->id,
                'label' => $menuItem->label,
                'href' => $menuItem->href,
                'enabled' => (bool) $menuItem->enabled,
                'operations' => $operations,
                'subItems' => $subItems
            ];
        }

        return response()->json($result);
    }


    /**
     * Actualizar el estado de un elemento del menú (habilitado/deshabilitado)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'enabled' => 'required|boolean'
        ]);

        $menuItem = MenuItem::findOrFail($id);
        $menuItem->enabled = $request->enabled;
        $menuItem->save();

        return response()->json([
            'message' => 'Estado actualizado correctamente',
            'menuItem' => $menuItem
        ]);
    }

    /**
     * Obtener los permisos de un elemento del menú para todos los roles
     */
    public function getPermissions($id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $roles = Role::all();
        $permissions = [];

        foreach ($roles as $role) {
            $permission = Permission::where('role_id', $role->id)
                ->where('menu_item_id', $menuItem->id)
                ->first();

            if ($permission) {
                $permissions[] = [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'permissions' => [
                        'create' => (bool) $permission->create,
                        'read' => (bool) $permission->read,
                        'update' => (bool) $permission->update,
                        'delete' => (bool) $permission->delete
                    ]
                ];
            } else {
                $permissions[] = [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'permissions' => [
                        'create' => false,
                        'read' => false,
                        'update' => false,
                        'delete' => false
                    ]
                ];
            }
        }

        return response()->json($permissions);
    }

    /**
     * Actualizar los permisos de un elemento del menú para un rol específico
     */
    public function updatePermissions(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.create' => 'boolean',
            'permissions.read' => 'boolean',
            'permissions.update' => 'boolean',
            'permissions.delete' => 'boolean'
        ]);

        $menuItem = MenuItem::findOrFail($id);
        $roleId = $request->role_id;

        // Buscar o crear el permiso
        $permission = Permission::firstOrNew([
            'role_id' => $roleId,
            'menu_item_id' => $menuItem->id
        ]);

        // Actualizar los permisos
        $permission->create = $request->permissions['create'] ?? false;
        $permission->read = $request->permissions['read'] ?? false;
        $permission->update = $request->permissions['update'] ?? false;
        $permission->delete = $request->permissions['delete'] ?? false;

        $permission->save();

        return response()->json([
            'message' => 'Permisos actualizados correctamente',
            'permission' => $permission
        ]);
    }

    /**
     * Obtener los subelementos de un elemento del menú
     */
    public function getSubItems($menuItemId)
    {
        $menuItem = MenuItem::findOrFail($menuItemId);
        $subItems = $menuItem->subItems()->get();

        return response()->json($subItems);
    }

    /**
     * Agregar un subelemento a un elemento del menú
     */
    public function addSubItem(Request $request, $menuItemId)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'href' => 'required|string|max:255',
            'operation' => 'required|in:create,read,update,delete'
        ]);

        $menuItem = MenuItem::findOrFail($menuItemId);

        $subItem = new MenuSubItem();
        $subItem->menu_item_id = $menuItem->id;
        $subItem->label = $request->label;
        $subItem->href = $request->href;
        $subItem->operation = $request->operation;
        $subItem->save();

        return response()->json([
            'message' => 'Subelemento agregado correctamente',
            'subItem' => $subItem
        ], 201);
    }

    /**
     * Actualizar un subelemento
     */
    public function updateSubItem(Request $request, $id)
    {
        $request->validate([
            'label' => 'sometimes|string|max:255',
            'href' => 'sometimes|string|max:255',
            'operation' => 'sometimes|in:create,read,update,delete'
        ]);

        $subItem = MenuSubItem::findOrFail($id);

        if ($request->has('label')) {
            $subItem->label = $request->label;
        }

        if ($request->has('href')) {
            $subItem->href = $request->href;
        }

        if ($request->has('operation')) {
            $subItem->operation = $request->operation;
        }

        $subItem->save();

        return response()->json([
            'message' => 'Subelemento actualizado correctamente',
            'subItem' => $subItem
        ]);
    }

    /**
     * Eliminar un subelemento
     */
    public function deleteSubItem($id)
    {
        $subItem = MenuSubItem::findOrFail($id);
        $subItem->delete();

        return response()->json([
            'message' => 'Subelemento eliminado correctamente'
        ]);
    }

    /**
     * Verificar si el usuario tiene acceso a una ruta específica
     */
    public function checkAccess(Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);

        $path = $request->path;

        // Rutas públicas siempre accesibles
        if ($path === '/' || $path === '/unauthorized' || $path === '/admin/sidebar' || $path === '/login') {
            return response()->json(['hasAccess' => true]);
        }

        // Obtener el usuario autenticado y su rol
        $user = Auth::user();
        $role = $user->roles()->first();

        if (!$role) {
            return response()->json(['hasAccess' => false]);
        }

        // Extraer el módulo y la operación de la ruta
        $pathParts = array_filter(explode('/', $path));

        if (empty($pathParts)) {
            return response()->json(['hasAccess' => true]); // Ruta raíz
        }

        $moduleSlug = reset($pathParts); // Primer segmento de la ruta

        // Buscar el elemento del menú correspondiente
        $menuItem = MenuItem::where('href', 'like', "/%$moduleSlug%")->first();

        if (!$menuItem || !$menuItem->enabled) {
            return response()->json(['hasAccess' => false]);
        }

        // Obtener los permisos del usuario para este elemento del menú
        $permission = Permission::where('role_id', $role->id)
            ->where('menu_item_id', $menuItem->id)
            ->first();

        if (!$permission) {
            return response()->json(['hasAccess' => false]);
        }

        // Si es solo la ruta del módulo, verificar si tiene algún permiso
        if (count($pathParts) === 1) {
            $hasAnyPermission = $permission->create || $permission->read || $permission->update || $permission->delete;
            return response()->json(['hasAccess' => $hasAnyPermission]);
        }

        // Si hay una operación específica en la ruta
        if (count($pathParts) > 1) {
            $operation = $pathParts[1];

            switch ($operation) {
                case 'create':
                    return response()->json(['hasAccess' => (bool) $permission->create]);
                case 'read':
                    return response()->json(['hasAccess' => (bool) $permission->read]);
                case 'update':
                    return response()->json(['hasAccess' => (bool) $permission->update]);
                case 'delete':
                    return response()->json(['hasAccess' => (bool) $permission->delete]);
                default:
                    // Para otras operaciones, verificar si tiene permiso de lectura
                    return response()->json(['hasAccess' => (bool) $permission->read]);
            }
        }

        return response()->json(['hasAccess' => false]);
    }
}
