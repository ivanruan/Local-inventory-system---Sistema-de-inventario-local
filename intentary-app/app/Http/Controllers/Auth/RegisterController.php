<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the application registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:usuarios',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|in:admin,operador,supervisor',
            'activo' => 'sometimes|boolean',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.unique' => 'Este nombre de usuario ya está en uso.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'Ingresa un email válido.',
            'email.unique' => 'Este email ya está registrado.',
            'email.max' => 'El email no puede tener más de 255 caracteres.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'rol.required' => 'El campo rol es obligatorio.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ]);

        // Determinar si el usuario debe estar activo
        $activo = true;
        if (auth()->check() && auth()->user()->rol === 'admin') {
            $activo = $request->has('activo') ? (bool) $request->activo : true;
        }

        $user = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            'activo' => $activo,
        ]);

        // Si es un admin creando el usuario, no hacer login automático
        if (auth()->check() && auth()->user()->rol === 'admin') {
            return redirect()->route('register')->with('success', 
                'Usuario creado exitosamente: ' . $user->nombre . ' (' . $user->rol . ')');
        }

        // Login automático para auto-registro
        Auth::login($user);

        return redirect('/dashboard')->with('success', 
            '¡Bienvenido! Tu cuenta ha sido creada exitosamente.');
    }
}
