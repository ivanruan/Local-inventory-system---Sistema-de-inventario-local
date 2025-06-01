<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\UbicacionController;
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

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::resource('productos', ProductoController::class);

Route::post('/marcas', [MarcaController::class, 'store'])->name('marcas.store');
Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
Route::post('/ubicaciones', [UbicacionController::class, 'store']);

Route::resource('alertas', AlertaStockController::class);
Route::patch('/alertas/{id}/resolver', [AlertaStockController::class, 'resolver'])->name('alertas.resolver');


Route::resource('movimientos', MovimientoInventarioController::class);
Route::resource('mantenimientos', MantenimientoController::class);
Route::resource('usuarios', UsuarioController::class);




// Login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Register routes (if you want registration)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Add your other protected routes here
});

// Logout Route (for authenticated users)
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Email Verification Routes (if using email verification)
Route::middleware('auth')->group(function () {
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});