<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Obtener los roles de un usuario
     */
    public function getRoles($userId)
    {
        $user = User::findOrFail($userId);
        $roles = $user->roles()->get();

        return response()->json($roles);
    }
}
