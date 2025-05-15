<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AlertaStockController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\UsuarioController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
});

Route::resource('productos', ProductoController::class);

Route::post('/marcas', [MarcaController::class, 'store'])->name('marcas.store');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::resource('alertas', AlertaStockController::class);
Route::patch('/alertas/{id}/resolver', [AlertaStockController::class, 'resolver'])->name('alertas.resolver');


Route::resource('movimientos', MovimientoInventarioController::class);
Route::resource('mantenimientos', MantenimientoController::class);
Route::resource('usuarios', UsuarioController::class);


