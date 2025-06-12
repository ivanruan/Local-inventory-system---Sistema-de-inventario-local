@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-exchange-alt"></i> Movimientos de Inventario</h1>
        <div>
            <a href="{{ route('movimientos.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo Movimiento
            </a>
            <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filtros">
                <i class="fas fa-filter"></i> Filtros
            </button>
        </div>
    </div>

    <!-- Panel de Filtros Colapsable -->
    <div class="collapse mb-4" id="filtros">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-search"></i> Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('movimientos.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="tipo" class="form-label">Tipo de Movimiento</label>
                            <select name="tipo" id="tipo" class="form-select">
                                <option value="">Todos</option>
                                <option value="entrada" {{ request('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="salida" {{ request('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="producto_id" class="form-label">Producto</label>
                            <select name="producto_id" id="producto_id" class="form-select">
                                <option value="">Todos los productos</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                                        {{ $producto->codigo }} - {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="fecha_desde" class="form-label">Fecha Desde</label>
                            <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $estadisticas['entradas_hoy'] ?? 0 }}</h4>
                            <p class="mb-0">Entradas Hoy</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $estadisticas['salidas_hoy'] ?? 0 }}</h4>
                            <p class="mb-0">Salidas Hoy</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $estadisticas['total_mes'] ?? 0 }}</h4>
                            <p class="mb-0">Movimientos Este Mes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $estadisticas['productos_afectados'] ?? 0 }}</h4>
                            <p class="mb-0">Productos Afectados</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Lista de Movimientos
                <small class="text-muted">({{ $movimientos->total() }} registros)</small>
            </h5>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary" onclick="exportarExcel()">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="exportarPDF()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($movimientos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>
                                    <a href="{{ route('movimientos.index', array_merge(request()->query(), ['sort' => 'fecha_hora', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                       class="text-white text-decoration-none">
                                        Fecha y Hora
                                        @if(request('sort') == 'fecha_hora')
                                            <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Tipo</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Proveedor/Proyecto</th>
                                <th>Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientos as $movimiento)
                                <tr>
                                    <td>
                                        <strong>{{ $movimiento->fecha_hora->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $movimiento->fecha_hora->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($movimiento->tipo == 'entrada')
                                            <span class="badge bg-success">
                                                <i class="fas fa-arrow-down"></i> Entrada
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-arrow-up"></i> Salida
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $movimiento->producto->codigo ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($movimiento->producto->nombre ?? 'Producto no encontrado', 30) }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold fs-5 {{ $movimiento->tipo == 'entrada' ? 'text-success' : 'text-danger' }}">
                                            {{ $movimiento->tipo == 'entrada' ? '+' : '-' }}{{ number_format($movimiento->cantidad, 2) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $movimiento->producto->unidad ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if($movimiento->proveedor)
                                            <small class="text-success">
                                                <i class="fas fa-truck"></i> {{ Str::limit($movimiento->proveedor->nombre, 20) }}
                                            </small>
                                        @elseif($movimiento->proyecto)
                                            <small class="text-info">
                                                <i class="fas fa-project-diagram"></i> {{ Str::limit($movimiento->proyecto->nombre, 20) }}
                                            </small>
                                        @else
                                            <small class="text-muted">N/A</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $movimiento->usuario->nombre ?? 'Usuario no encontrado' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('movimientos.show', $movimiento->id) }}" 
                                               class="btn btn-outline-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('movimientos.edit', $movimiento->id) }}" 
                                               class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmarEliminacion({{ $movimiento->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center p-3">
                    <div>
                        <small class="text-muted">
                            Mostrando {{ $movimientos->firstItem() }} a {{ $movimientos->lastItem() }} 
                            de {{ $movimientos->total() }} registros
                        </small>
                    </div>
                    <div>
                        {{ $movimientos->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay movimientos registrados</h5>
                    <p class="text-muted">Comienza creando tu primer movimiento de inventario</p>
                    <a href="{{ route('movimientos.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Crear Primer Movimiento
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>¡Atención!</strong> Esta acción eliminará el movimiento y podría afectar el stock del producto.
                </div>
                <p>¿Estás seguro de que deseas eliminar este movimiento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarEliminacion(movimientoId) {
        const form = document.getElementById('deleteForm');
        form.action = `/movimientos/${movimientoId}`;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    function exportarExcel() {
        const params = new URLSearchParams(window.location.search);
        params.append('export', 'excel');
        window.location.href = '{{ route("movimientos.index") }}?' + params.toString();
    }

    function exportarPDF() {
        const params = new URLSearchParams(window.location.search);
        params.append('export', 'pdf');
        window.location.href = '{{ route("movimientos.index") }}?' + params.toString();
    }

    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
