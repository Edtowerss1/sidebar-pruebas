<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Información del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rutas de menú
    Route::get('/menu-items', [MenuController::class, 'index']);
    Route::put('/menu-items/{id}/status', [MenuController::class, 'updateStatus']);
    Route::get('/menu-items/{id}/permissions', [MenuController::class, 'getPermissions']);
    Route::put('/menu-items/{id}/permissions', [MenuController::class, 'updatePermissions']);
    Route::get('/menu-items/{menuItemId}/subitems', [MenuController::class, 'getSubItems']);
    Route::post('/menu-items/{menuItemId}/subitems', [MenuController::class, 'addSubItem']);
    Route::put('/menu-subitems/{id}', [MenuController::class, 'updateSubItem']);
    Route::delete('/menu-subitems/{id}', [MenuController::class, 'deleteSubItem']);

    // Ruta para verificar acceso
    Route::post('/check-access', [MenuController::class, 'checkAccess']);

    // Rutas para roles
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/users/{userId}/roles', [UserController::class, 'getRoles']);
});
