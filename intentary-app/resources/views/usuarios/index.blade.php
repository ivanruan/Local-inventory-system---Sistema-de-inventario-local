{{-- resources/views/usuarios/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Usuarios del sistema')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Usuarios del Sistema</h2>
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-2"></i> Nuevo Usuario
            </a>
        </div>

        {{-- Mensajes de Sesión (Éxito o Error) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Email</th>
                                <th scope="col">Rol</th>
                                <th scope="col" class="text-center">Activo</th>
                                <th scope="col">Creado</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usuarios as $user)
                                <tr>
                                    <td>{{ $user->nombre }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($user->rol) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($user->activo)
                                            <span class="badge bg-success py-2 px-3 rounded-pill">Sí</span>
                                        @else
                                            <span class="badge bg-danger py-2 px-3 rounded-pill">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-sm btn-warning me-1" title="Editar Usuario">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </a>
                                        <form action="{{ route('usuarios.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar a {{ $user->nombre }}? Esta acción es irreversible.')" title="Eliminar Usuario">
                                                <i class="fas fa-trash-alt me-1"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
