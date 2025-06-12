@extends('layouts.app')

@section('title', 'Gestión de Mantenimientos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-primary">Listado de Mantenimientos</h2>
        <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary btn-lg rounded-pill shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> Nuevo Mantenimiento
        </a>
    </div>

    {{-- Mensaje de éxito si lo hay --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-lg shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Mensaje de error si lo hay (importante para las eliminaciones fallidas o validaciones) --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-lg shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($mantenimientos->isEmpty())
        <div class="alert alert-info text-center rounded-lg shadow-sm" role="alert">
            <p class="mb-0">No hay mantenimientos registrados en este momento. ¡Crea uno nuevo!</p>
        </div>
    @else
        <div class="table-responsive rounded-lg shadow-sm">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Producto</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Fecha Programada</th>
                        <th scope="col">Responsable</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Costo</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mantenimientos as $m)
                        <tr>
                            <td>
                                <strong>{{ $m->producto->nombre }}</strong>
                                <br><small class="text-muted">{{ $m->producto->codigo }}</small>
                            </td>
                            <td>{{ ucfirst($m->tipo) }}</td>
                            <td>{{ $m->fecha_programada->format('d/m/Y H:i') }}</td>
                            <td>{{ $m->responsable }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2
                                    @if ($m->status == 'pendiente') bg-warning text-dark
                                    @elseif ($m->status == 'completado') bg-success
                                    @elseif ($m->status == 'cancelado') bg-danger
                                    @else bg-secondary @endif">
                                    {{ ucfirst($m->status) }}
                                </span>
                            </td>
                            <td>${{ number_format($m->costo, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('mantenimientos.show', $m) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 me-1">
                                    <i class="fas fa-eye me-1"></i> Ver
                                </a>
                                {{-- Botones de acción: Editar y Eliminar --}}
                                <a href="{{ route('mantenimientos.edit', $m) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                    <i class="fas fa-edit me-1"></i> Editar
                                </a>
                                <form action="{{ route('mantenimientos.destroy', $m) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('¿Estás seguro de que deseas eliminar este mantenimiento? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-trash-alt me-1"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Enlaces de paginación --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $mantenimientos->links('pagination::bootstrap-5') }} {{-- Usa el tema de paginación de Bootstrap 5 --}}
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endpush


