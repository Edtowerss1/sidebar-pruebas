<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Support\Collection;

class MenuService
{
    public function getMenuForUser(User $user): Collection
    {
        // Obtener todos los ítems del menú con sus operaciones y subitems
        $menuItems = MenuItem::with(['operations', 'subItems'])
            ->orderBy('order')
            ->get();

        // Transformar los datos para que coincidan con la estructura esperada por el frontend
        return $menuItems->map(function ($item) use ($user) {
            // Verificar permisos para cada operación
            $operations = [];
            foreach ($item->operations as $op) {
                $permissionName = "{$item->item_id}.{$op->operation}";
                $hasPermission = $user->hasRole('admin') || $user->can($permissionName);

                $operations[$op->operation] = $op->enabled && $hasPermission;
            }

            // Formatear subitems
            $subItems = $item->subItems->map(function ($subItem) {
                return [
                    'id' => $subItem->subitem_id,
                    'label' => $subItem->label,
                    'href' => $subItem->href,
                    'operation' => $subItem->operation,
                ];
            });

            return [
                'id' => $item->item_id,
                'label' => $item->label,
                'icon' => $item->icon,
                'href' => $item->href,
                'enabled' => $item->enabled,
                'operations' => $operations,
                'subItems' => $subItems,
            ];
        });
    }

    public function checkAccess(User $user, string $path): bool
    {
        // Rutas siempre permitidas
        if (in_array($path, ['/', '/unauthorized', '/admin/sidebar'])) {
            return true;
        }

        // Extraer el módulo y la operación de la ruta
        $pathParts = explode('/', trim($path, '/'));
        if (empty($pathParts)) {
            return true;
        }

        $moduleId = $pathParts[0];

        // Buscar el módulo
        $module = MenuItem::where('item_id', $moduleId)->first();
        if (!$module || !$module->enabled) {
            return false;
        }

        // Si es solo la ruta del módulo, verificar si alguna operación está habilitada
        if (count($pathParts) === 1) {
            $hasAnyOperation = $module->operations()->where('enabled', true)->exists();

            // Verificar si el usuario tiene permiso para alguna operación
            $hasPermission = $user->hasRole('admin') ||
                $user->can("{$moduleId}.create") ||
                $user->can("{$moduleId}.read") ||
                $user->can("{$moduleId}.update") ||
                $user->can("{$moduleId}.delete");

            return $hasAnyOperation && $hasPermission;
        }

        // Si hay una operación específica en la ruta
        if (count($pathParts) > 1) {
            $operation = $pathParts[1];

            // Verificar si la operación es válida
            if (!in_array($operation, ['create', 'read', 'update', 'delete'])) {
                return false;
            }

            // Verificar si la operación está habilitada
            $operationEnabled = $module->operations()
                ->where('operation', $operation)
                ->where('enabled', true)
                ->exists();

            // Verificar si el usuario tiene permiso para esta operación
            $hasPermission = $user->hasRole('admin') || $user->can("{$moduleId}.{$operation}");

            return $operationEnabled && $hasPermission;
        }

        return false;
    }
}
