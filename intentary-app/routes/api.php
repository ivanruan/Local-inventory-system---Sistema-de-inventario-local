<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\UsuarioController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Grupo de rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('marcas', MarcaController::class)->except(['index']);
    Route::apiResource('proveedores', ProveedorController::class);
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('proyectos', ProyectoController::class);
    Route::apiResource('mantenimientos', MantenimientoController::class);
    Route::apiResource('ubicaciones', UbicacionController::class);
    Route::apiResource('usuarios', UsuarioController::class);
});

//Route::apiResource('marcas', MarcaController::class)->except(['index']);
Route::get('/marcas', [MarcaController::class, 'index']); // Public path
//
Route::put('/debug/ubicaciones/{id}', function(Request $request, $id) {
    $ubicacion = Ubicacion::find($id);
    
    if (!$ubicacion) {
        return response()->json([
            'error' => 'Ubicación no encontrada',
            'id_recibido' => $id,
            'existentes' => Ubicacion::all()->pluck('id')
        ], 404);
    }
    
    $ubicacion->update($request->all());
    return $ubicacion;
});
