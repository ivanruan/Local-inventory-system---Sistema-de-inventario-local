@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
    <h2>Editar Usuario</h2>

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" name="nombre" class="form-control" value="{{ $usuario->nombre }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" value="{{ $usuario->email }}" required>
        </div>

        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="operador" {{ $usuario->rol === 'operador' ? 'selected' : '' }}>Operador</option>
                <option value="supervisor" {{ $usuario->rol === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">¿Activo?</label>
            <select name="activo" class="form-select">
                <option value="1" {{ $usuario->activo ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ !$usuario->activo ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <button class="btn btn-primary">Actualizar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection
