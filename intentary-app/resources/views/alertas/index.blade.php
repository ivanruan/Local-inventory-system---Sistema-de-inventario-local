@extends('layouts.app')

@section('title', 'Listado de Alertas de Stock')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-danger">Alertas de Stock</h2>
        <div class="d-flex align-items-center">
            {{-- Nuevo botón para ir al Dashboard --}}
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg rounded-pill shadow-sm me-2">
                <i class="fas fa-tachometer-alt me-2"></i> Ir al Dashboard
            </a>
            {{-- Aquí no hay botón de "Nueva Alerta" porque se generan automáticamente --}}
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-lg shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-lg shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($alertas->isEmpty())
        <div class="alert alert-info text-center rounded-lg shadow-sm" role="alert">
            <p class="mb-0">No hay alertas de stock registradas en este momento.</p>
        </div>
    @else
        <div class="table-responsive rounded-lg shadow-sm">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Producto</th>
                        <th scope="col">Tipo de Alerta</th>
                        <th scope="col">Nivel Crítico</th>
                        <th scope="col">Fecha Generación</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alertas as $alerta)
                        <tr>
                            <td>
                                <strong>{{ $alerta->producto->nombre }}</strong>
                                <br><small class="text-muted">{{ $alerta->producto->codigo }}</small>
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $alerta->tipo_alerta)) }}</td>
                            <td>{{ $alerta->nivel_critico }}</td>
                            <td>{{ $alerta->fecha_generacion->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2
                                    @if (!$alerta->resuelta) bg-danger
                                    @else bg-success @endif">
                                    {{ $alerta->resuelta ? 'Resuelta' : 'Activa' }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{-- Asegúrate de que las rutas usen 'alertas.show' y 'alertas.resolver' --}}
                                <a href="{{ route('alertas.show', $alerta) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 me-1">
                                    <i class="fas fa-eye me-1"></i> Ver
                                </a>
                                @if (!$alerta->resuelta)
                                    <form action="{{ route('alertas.resolver', $alerta) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3"
                                                onclick="return confirm('¿Estás seguro de marcar esta alerta como resuelta?')">
                                            <i class="fas fa-check-circle me-1"></i> Resolver
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Enlaces de paginación: Este bloque es el que genera los botones < > y los números de página --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $alertas->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endpush
