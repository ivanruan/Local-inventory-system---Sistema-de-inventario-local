@extends('layouts.app')

@section('title', 'Listado de Productos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Listado de Productos</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Producto
            </a>
            <button class="btn btn-outline-secondary" onclick="toggleFilters()">
                <i class="bi bi-funnel"></i> Filtros
            </button>
            <button class="btn btn-outline-success" onclick="exportarExcel()">
                <i class="bi bi-file-earmark-excel"></i> Exportar
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Panel de Filtros --}}
    <div id="filtrosPanel" class="card mb-3" style="display: none;">
        <div class="card-body">
            <form method="GET" action="{{ route('productos.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" placeholder="Código, nombre, especificación...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas</option>
                        @foreach($categorias ?? [] as $categoria)
                            <option value="{{ $categoria->id }}" 
                                {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Marca</label>
                    <select name="marca" class="form-select">
                        <option value="">Todas</option>
                        @foreach($marcas ?? [] as $marca)
                            <option value="{{ $marca->id }}" 
                                {{ request('marca') == $marca->id ? 'selected' : '' }}>
                                {{ $marca->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Producto::getStatusOptions() as $status)
                            <option value="{{ $status }}" 
                                {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Stock</label>
                    <select name="stock_filter" class="form-select">
                        <option value="">Todos</option>
                        <option value="bajo" {{ request('stock_filter') == 'bajo' ? 'selected' : '' }}>Stock Bajo</option>
                        <option value="fuera" {{ request('stock_filter') == 'fuera' ? 'selected' : '' }}>Fuera de Stock</option>
                        <option value="sobre" {{ request('stock_filter') == 'sobre' ? 'selected' : '' }}>Sobre Stock</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Estadísticas Rápidas --}}
    <div class="row mb-3">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam fs-4 me-2"></i>
                        <div>
                            <div class="fs-6 fw-bold">{{ $productos->total() ?? 0 }}</div>
                            <div class="small">Total Productos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle fs-4 me-2"></i>
                        <div>
                            <div class="fs-6 fw-bold">{{ $activos ?? 0 }}</div>
                            <div class="small">Activos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle fs-4 me-2"></i>
                        <div>
                            <div class="fs-6 fw-bold">{{ $stockBajo ?? 0 }}</div>
                            <div class="small">Stock Bajo</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-x-circle fs-4 me-2"></i>
                        <div>
                            <div class="fs-6 fw-bold">{{ $fueraStock ?? 0 }}</div>
                            <div class="small">Fuera de Stock</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-currency-dollar fs-4 me-2"></i>
                        <div>
                            <div class="fs-6 fw-bold">${{ number_format($valorTotal ?? 0, 2) }}</div>
                            <div class="small">Valor Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-arrow-up-down fs-4 me-2"></i>
                        <div>
                            <div class="fs-6 fw-bold">{{ $sobreStock ?? 0 }}</div>
                            <div class="small">Sobre Stock</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($productos->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-info-circle"></i> No hay productos registrados que coincidan con los criterios de búsqueda.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle table-sm">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th style="min-width: 80px;">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                               class="text-white text-decoration-none">
                                Fecha <i class="bi bi-arrow-down-up"></i>
                            </a>
                        </th>
                        <th style="min-width: 100px;">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'codigo', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                               class="text-white text-decoration-none">
                                Código <i class="bi bi-arrow-down-up"></i>
                            </a>
                        </th>
                        <th style="min-width: 150px;">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nombre', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                               class="text-white text-decoration-none">
                                Producto <i class="bi bi-arrow-down-up"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">Especificación</th>
                        <th style="min-width: 80px;">Marca</th>
                        <th style="min-width: 80px;">Categoría</th>
                        <th style="min-width: 80px;">Ubicación</th>
                        <th style="min-width: 60px;">Nivel</th>
                        <th style="min-width: 60px;">Unidad</th>
                        <th style="min-width: 80px;">Stock Inicial</th>
                        <th style="min-width: 70px;">Entradas</th>
                        <th style="min-width: 70px;">Salidas</th>
                        <th style="min-width: 80px;">Stock Mín.</th>
                        <th style="min-width: 80px;">Stock Actual</th>
                        <th style="min-width: 80px;">Stock Máx.</th>
                        <th style="min-width: 80px;">Stock Seg.</th>
                        <th style="min-width: 80px;">Duración Inv.</th>
                        <th style="min-width: 100px;">Status</th>
                        <th style="min-width: 80px;">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'costo', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                               class="text-white text-decoration-none">
                                Costo <i class="bi bi-arrow-down-up"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">Proveedor</th>
                        <th style="min-width: 150px;">Observaciones</th>
                        <th style="min-width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                        <tr class="{{ $producto->es_fuera_de_stock ? 'table-danger' : ($producto->es_stock_bajo ? 'table-warning' : ($producto->es_sobre_stock ? 'table-info' : '')) }}">
                            <td class="text-nowrap">
                                <small>{{ $producto->created_at ? $producto->created_at->format('d/m/Y') : '-' }}</small>
                            </td>
                            <td class="text-nowrap">
                                <strong>{{ $producto->codigo }}</strong>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $producto->nombre }}</div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ Str::limit($producto->especificacion, 30) }}
                                </small>
                            </td>
                            <td>
                                <small>{{ $producto->marca->nombre ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ $producto->categoria->nombre ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ $producto->ubicacion->codigo ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $producto->nivel }}</span>
                            </td>
                            <td class="text-center">
                                <small>{{ $producto->unidad }}</small>
                            </td>
                            <td class="text-center">
                                {{ number_format($producto->stock_inicial) }}
                            </td>
                            <td class="text-center text-success">
                                <strong>{{ number_format($producto->total_entradas) }}</strong>
                            </td>
                            <td class="text-center text-danger">
                                <strong>{{ number_format($producto->total_salidas) }}</strong>
                            </td>
                            <td class="text-center">
                                <small class="text-warning">{{ $producto->stock_minimo ? number_format($producto->stock_minimo) : '-' }}</small>
                            </td>
                            <td class="text-center">
                                <strong class="{{ $producto->es_fuera_de_stock ? 'text-danger' : ($producto->es_stock_bajo ? 'text-warning' : 'text-success') }}">
                                    {{ number_format($producto->stock_actual) }}
                                </strong>
                            </td>
                            <td class="text-center">
                                <small class="text-info">{{ $producto->stock_maximo ? number_format($producto->stock_maximo) : '-' }}</small>
                            </td>
                            <td class="text-center">
                                <small class="text-secondary">{{ $producto->stock_seguridad ? number_format($producto->stock_seguridad) : '-' }}</small>
                            </td>
                            <td class="text-center">
                                <small>{{ $producto->duracion_inventario ? $producto->duracion_inventario . ' días' : '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $producto->status == 'Activo' ? 'success' : 
                                    ($producto->status == 'Inactivo' ? 'secondary' : 
                                    ($producto->status == 'Stock Bajo' ? 'warning' : 
                                    ($producto->status == 'Fuera de Stock' ? 'danger' : 
                                    ($producto->status == 'Sobre Stock' ? 'info' : 
                                    ($producto->status == 'Stock Optimo' ? 'success' : 'primary'))))) 
                                }}">
                                    {{ $producto->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <strong>${{ number_format($producto->costo, 2) }}</strong>
                            </td>
                            <td>
                                <small>{{ $producto->proveedor->nombre ?? '-' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $producto->observaciones ? Str::limit($producto->observaciones, 40) : '-' }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('productos.show', $producto) }}" 
                                       class="btn btn-outline-info" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('productos.edit', $producto) }}" 
                                       class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="btn btn-outline-secondary" 
                                            onclick="verMovimientos({{ $producto->id }})" 
                                            title="Ver movimientos">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>
                                    <form action="{{ route('productos.destroy', $producto) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Información de paginación --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} 
                de {{ $productos->total() }} resultados
            </div>
            <div>
                {{ $productos->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

{{-- Modal para ver movimientos --}}
<div class="modal fade" id="movimientosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Movimientos de Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="movimientosContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .table-responsive {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .table th {
        white-space: nowrap;
        font-size: 0.85rem;
    }
    
    .table td {
        font-size: 0.85rem;
        vertical-align: middle;
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endpush

@push('scripts')
<script>
function toggleFilters() {
    const panel = document.getElementById('filtrosPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function verMovimientos(productoId) {
    const modal = new bootstrap.Modal(document.getElementById('movimientosModal'));
    const content = document.getElementById('movimientosContent');
    
    // Mostrar loading
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Hacer petición AJAX para obtener movimientos
    fetch(`/productos/${productoId}/movimientos`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="table-responsive">';
            html += '<table class="table table-sm">';
            html += '<thead><tr><th>Fecha</th><th>Tipo</th><th>Cantidad</th><th>Observaciones</th></tr></thead>';
            html += '<tbody>';
            
            if (data.length > 0) {
                data.forEach(mov => {
                    html += `<tr>
                        <td>${mov.fecha}</td>
                        <td><span class="badge bg-${mov.tipo === 'Entrada' ? 'success' : 'danger'}">${mov.tipo}</span></td>
                        <td>${mov.cantidad}</td>
                        <td>${mov.observaciones || '-'}</td>
                    </tr>`;
                });
            } else {
                html += '<tr><td colspan="4" class="text-center text-muted">No hay movimientos registrados</td></tr>';
            }
            
            html += '</tbody></table></div>';
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Error al cargar los movimientos</div>';
            console.error('Error:', error);
        });
}

function exportarExcel() {
    // Implementar lógica de exportación
    const url = new URL(window.location.href);
    url.pathname = url.pathname.replace('/productos', '/productos/export');
    window.open(url.toString(), '_blank');
}

// Auto-refresh para el estado de stock cada 5 minutos
setInterval(function() {
    if (document.visibilityState === 'visible') {
        location.reload();
    }
}, 300000); // 5 minutos
</script>
@endpush