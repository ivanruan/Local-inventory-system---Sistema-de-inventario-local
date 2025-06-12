{{-- resources/views/usuarios/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Crear Nuevo Usuario')

@section('content')
    <div class="card">
        <div class="card-header">
            Crear Nuevo Usuario
        </div>
        <div class="card-body">
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select class="form-select @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                        <option value="">Selecciona un rol</option>
                        <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="operador" {{ old('rol') == 'operador' ? 'selected' : '' }}>Operador</option>
                        <option value="supervisor" {{ old('rol') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                    </select>
                    @error('rol')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@endsection
