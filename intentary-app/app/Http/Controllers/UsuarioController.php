<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        // Obtener todos los usuarios, ordenados por fecha de creación descendente
        $usuarios = Usuario::orderBy('created_at', 'desc')->get();

        // Retornar la vista con los usuarios
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create'); // Asegúrate de que esta vista exista
    }

    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $usuario = Usuario::create($data);
        // Redirige después de crear el usuario, en lugar de retornar JSON
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(Usuario $usuario)
    {
        return response()->json($usuario);
    }

    public function edit(Usuario $usuario)
    {
        // Retorna la vista 'usuarios.edit' y le pasa el objeto $usuario
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        $data = $request->validated();

        // Solo actualiza la contraseña si se proporciona una nueva
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // No actualiza la contraseña si está vacía
        }

        // Asegúrate de manejar el campo 'activo' correctamente
        // Si no está presente en la request (por ejemplo, si el checkbox no se marca),
        // Laravel por defecto lo consideraría 'null' si la columna es nullable.
        // Si quieres que sea false cuando no está marcado, puedes hacer esto:
        $data['activo'] = isset($data['activo']); // Esto lo convierte a true/false

        $usuario->update($data);

        // Redirige después de actualizar, en lugar de retornar JSON
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        // Redirige a la página de índice con un mensaje de éxito
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}

