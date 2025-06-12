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

// Login routes (públicas)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Register routes (públicas - si quieres registration)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes - TODAS las rutas principales ahora están protegidas
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Recursos principales (mantienen Route::resource si tienen CRUD completo)
    Route::resource('productos', ProductoController::class);
    Route::resource('movimientos', MovimientoInventarioController::class);
    Route::resource('mantenimientos', MantenimientoController::class);
    Route::resource('usuarios', UsuarioController::class);
    
    // Rutas específicas para Alertas de Stock (ya que no es un CRUD completo)
    // NOTA: Todos los nombres de ruta de alerta ahora usan el prefijo 'alertas.'
    Route::get('/alertas', [AlertaStockController::class, 'index'])->name('alertas.index');
    Route::get('/alertas/{alertaStock}', [AlertaStockController::class, 'show'])->name('alertas.show');
    Route::patch('/alertas/{alertaStock}/resolver', [AlertaStockController::class, 'resolver'])->name('alertas.resolver');
    
    // Rutas adicionales específicas (mantienen las que ya tenías)
    Route::post('/marcas', [MarcaController::class, 'store'])->name('marcas.store');
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::post('/ubicaciones', [UbicacionController::class, 'store']);
    Route::post('/productos/preview-codigo', [ProductoController::class, 'previewCodigo'])
        ->name('productos.preview-codigo');
    
    // Email Verification Routes (dentro del grupo auth)
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});

// Logout Route (para usuarios autenticados)
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');


