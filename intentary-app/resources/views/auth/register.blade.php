@extends('layouts.app')

@section('title', 'Registrarse')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-4 text-center">Crear cuenta</h3>

                {{-- Mostrar mensajes de error --}}
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Mostrar mensaje de éxito --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="row">
                        {{-- Nombre de usuario --}}
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre de usuario *</label>
                            <input type="text" name="nombre" id="nombre" 
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" required autofocus autocomplete="username">

                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Correo electrónico --}}
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Correo electrónico *</label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required autocomplete="email">

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Contraseña --}}
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror" 
                                   required autocomplete="new-password">

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Mínimo 8 caracteres</div>
                        </div>

                        {{-- Confirmar contraseña --}}
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar contraseña *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control" required autocomplete="new-password">
                        </div>
                    </div>

                    {{-- Rol --}}
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol *</label>
                        <select name="rol" id="rol" class="form-select @error('rol') is-invalid @enderror" required>
                            <option value="">Selecciona un rol</option>
                            <option value="operador" {{ old('rol') == 'operador' ? 'selected' : '' }}>Operador</option>
                            <option value="supervisor" {{ old('rol') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>

                        @error('rol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small>
                                <strong>Operador:</strong> Acceso básico al sistema<br>
                                <strong>Supervisor:</strong> Permisos de supervisión y reportes<br>
                                <strong>Administrador:</strong> Acceso completo al sistema
                            </small>
                        </div>
                    </div>

                    {{-- Estado activo (opcional, solo para admins) --}}
                    @auth
                        @if(auth()->user()->rol === 'admin')
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="activo" id="activo" 
                                           class="form-check-input" value="1" 
                                           {{ old('activo', '1') ? 'checked' : '' }}>
                                    <label for="activo" class="form-check-label">Usuario activo</label>
                                </div>
                                <div class="form-text">Los usuarios inactivos no podrán iniciar sesión</div>
                            </div>
                        @endif
                    @endauth

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-success">Crear cuenta</button>
                    </div>

                    <div class="text-center">
                        <span class="text-muted">¿Ya tienes cuenta?</span>
                        <a href="{{ route('login') }}" class="text-decoration-none">Inicia sesión aquí</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script para mostrar/ocultar información de roles --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rolSelect = document.getElementById('rol');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    // Validación en tiempo real de confirmación de contraseña
    confirmPasswordInput.addEventListener('input', function() {
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    });

    passwordInput.addEventListener('input', function() {
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    });
});
</script>
@endsection