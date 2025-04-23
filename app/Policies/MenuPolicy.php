<?php

namespace App\Policies;

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Todos los usuarios autenticados pueden ver el menú
    }

    public function manage(User $user): bool
    {
        return $user->can('menu.manage');
    }

    public function access(User $user, MenuItem $menuItem, string $operation = null): bool
    {
        // Si el usuario es admin, siempre tiene acceso
        if ($user->hasRole('admin')) {
            return true;
        }

        // Si el ítem no está habilitado, nadie tiene acceso
        if (!$menuItem->enabled) {
            return false;
        }

        // Si no se especifica operación, verificar si tiene acceso a alguna operación
        if ($operation === null) {
            return $user->can("{$menuItem->item_id}.create") ||
                $user->can("{$menuItem->item_id}.read") ||
                $user->can("{$menuItem->item_id}.update") ||
                $user->can("{$menuItem->item_id}.delete");
        }

        // Verificar si la operación está habilitada
        $operationEnabled = $menuItem->operations()
            ->where('operation', $operation)
            ->where('enabled', true)
            ->exists();

        // Verificar si el usuario tiene permiso para esta operación
        return $operationEnabled && $user->can("{$menuItem->item_id}.{$operation}");
    }
}
