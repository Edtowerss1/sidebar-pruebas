<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    /**
     * Obtener todos los roles
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }
}
