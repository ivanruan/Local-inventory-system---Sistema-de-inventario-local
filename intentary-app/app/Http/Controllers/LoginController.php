<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar inicio de sesión.
     */
    public function login(Request $request)
    {
        // Validar entradas
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->filled('remember');

        // Intentar iniciar sesión
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // Evita ataques de sesión
            return redirect()->intended('/dashboard'); // Ajusta la ruta según tu proyecto
        }

        // Si falla
        throw ValidationException::withMessages([
            'email' => __('Las credenciales no son válidas.'),
        ]);
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();  // Elimina la sesión
        $request->session()->regenerateToken();  // Regenera el token CSRF

        return redirect('/login');
    }
}
