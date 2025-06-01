@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-4 text-center">Iniciar sesión</h3>

                {{-- Mostrar mensajes de error --}}
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Mostrar mensaje de éxito si viene de reset password --}}
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Nombre de usuario --}}
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de usuario</label>
                        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre') }}" required autofocus autocomplete="username">

                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror" 
                               required autocomplete="current-password">

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="form-check-label">Recordarme</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Ingresar</button>
                    </div>
                </form>

                {{-- Opcional: enlace de registro si tienes registro habilitado --}}
                @if (Route::has('register'))
                    <div class="text-center mt-3">
                        <span class="text-muted">¿No tienes cuenta?</span>
                        <a href="{{ route('register') }}" class="text-decoration-none">Regístrate aquí</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection