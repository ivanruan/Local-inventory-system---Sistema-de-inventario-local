{{-- resources/views/usuarios/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Editar Usuario: ' . $usuario->nombre)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-3">
                        <h4 class="mb-0">Editar Usuario: {{ $usuario->nombre }}</h4>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Volver a Usuarios
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
                            @csrf
                            @method('PUT') {{-- Importante: Usa PUT o PATCH para actualizaciones --}}

                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-semibold">Nombre:</label>
                                <input type="text" class="form-control rounded-pill @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required autofocus>
                                @error('nombre')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email:</label>
                                <input type="email" class="form-control rounded-pill @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Nueva Contraseña (dejar vacío para no cambiar):</label>
                                <input type="password" class="form-control rounded-pill @error('password') is-invalid @enderror" id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirmar Nueva Contraseña:</label>
                                <input type="password" class="form-control rounded-pill" id="password_confirmation" name="password_confirmation">
                            </div>

                            <div class="mb-3">
                                <label for="rol" class="form-label fw-semibold">Rol:</label>
                                <select class="form-select rounded-pill @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                                    <option value="">Selecciona un rol</option>
                                    <option value="admin" {{ old('rol', $usuario->rol) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="operador" {{ old('rol', $usuario->rol) == 'operador' ? 'selected' : '' }}>Operador</option>
                                    <option value="supervisor" {{ old('rol', $usuario->rol) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                </select>
                                @error('rol')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" {{ old('activo', $usuario->activo) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="activo">Usuario Activo</label>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-success btn-lg rounded-pill px-4">
                                    <i class="fas fa-save me-2"></i> Actualizar Usuario
                                </button>
                                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-lg rounded-pill px-4">
                                    <i class="fas fa-times-circle me-2"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
