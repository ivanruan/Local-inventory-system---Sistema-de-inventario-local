@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalles del Producto</h1>
        <div>
            <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Código del Producto - Destacado -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-barcode"></i> Código: {{ $producto->codigo }}
            </h4>
        </div>
    </div>

    <!-- Información Básica -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información Básica</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Nombre:</strong>
                    <p class="text-muted">{{ $producto->nombre }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Estado:</strong>
                    <p>
                        @switch($producto->status)
                            @case('Activo')
                                <span class="badge bg-success">{{ $producto->status }}</span>
                                @break
                            @case('Inactivo')
                                <span class="badge bg-warning">{{ $producto->status }}</span>
                                @break
                            @case('Obsoleto')
                                <span class="badge bg-danger">{{ $producto->status }}</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ $producto->status }}</span>
                        @endswitch
                    </p>
                </div>
            </div>
            
            @if($producto->especificacion)
            <div class="row">
                <div class="col-12">
                    <strong>Especificación:</strong>
                    <p class="text-muted">{{ $producto->especificacion }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Clasificación -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-tags"></i> Clasificación</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Categoría:</strong>
                    <p class="text-muted">
                        @if($producto->categoria)
                            {{ $producto->categoria->nombre }}
                            @if($producto->categoria->codigo)
                                <small class="text-info">({{ $producto->categoria->codigo }})</small>
                            @endif
                        @else
                            <span class="text-danger">Sin categoría</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4">
                    <strong>Marca:</strong>
                    <p class="text-muted">
                        {{ $producto->marca ? $producto->marca->nombre : 'Sin marca' }}
                    </p>
                </div>
                <div class="col-md-4">
                    <strong>Ubicación:</strong>
                    <p class="text-muted">
                        @if($producto->ubicacion)
                            {{ $producto->ubicacion->codigo }} - Nivel {{ $producto->ubicacion->nivel }}
                        @else
                            <span class="text-danger">Sin ubicación</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventario y Stock -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-boxes"></i> Inventario y Stock</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Unidad:</strong>
                    <p class="text-muted">{{ $producto->unidad ?? 'No especificada' }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Stock Actual:</strong>
                    <p class="fs-5 fw-bold 
                        @if($producto->stock_actual <= ($producto->stock_minimo ?? 0)) text-danger 
                        @elseif($producto->stock_actual <= ($producto->stock_seguridad ?? 0)) text-warning 
                        @else text-success @endif">
                        {{ $producto->stock_actual ?? 0 }}
                    </p>
                </div>
                <div class="col-md-3">
                    <strong>Stock Mínimo:</strong>
                    <p class="text-muted">{{ $producto->stock_minimo ?? 'No definido' }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Stock Máximo:</strong>
                    <p class="text-muted">{{ $producto->stock_maximo ?? 'No definido' }}</p>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <strong>Stock de Seguridad:</strong>
                    <p class="text-muted">{{ $producto->stock_seguridad ?? 'No definido' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Duración Inventario:</strong>
                    <p class="text-muted">
                        {{ $producto->duracion_inventario ? $producto->duracion_inventario . ' días' : 'No definida' }}
                    </p>
                </div>
            </div>

            <!-- Barra de progreso del stock -->
            @if($producto->stock_actual && $producto->stock_maximo)
            <div class="mt-3">
                <small class="text-muted">Nivel de Stock:</small>
                <div class="progress" style="height: 20px;">
                    @php
                        $porcentaje = ($producto->stock_actual / $producto->stock_maximo) * 100;
                        $colorBarra = $porcentaje <= 25 ? 'bg-danger' : ($porcentaje <= 50 ? 'bg-warning' : 'bg-success');
                    @endphp
                    <div class="progress-bar {{ $colorBarra }}" role="progressbar" 
                         style="width: {{ min($porcentaje, 100) }}%" 
                         aria-valuenow="{{ $producto->stock_actual }}" 
                         aria-valuemin="0" 
                         aria-valuemax="{{ $producto->stock_maximo }}">
                        {{ number_format($porcentaje, 1) }}%
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Información Económica y Técnica -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Información Económica</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Costo:</strong>
                        <p class="fs-4 fw-bold text-primary">
                            {{ $producto->costo ? '$' . number_format($producto->costo, 2) : 'No definido' }}
                        </p>
                    </div>
                    
                    @if($producto->costo && $producto->stock_actual)
                    <div>
                        <strong>Valor Total en Inventario:</strong>
                        <p class="fs-5 fw-bold text-success">
                            ${{ number_format($producto->costo * $producto->stock_actual, 2) }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Información Técnica</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Vida Útil:</strong>
                        <p class="text-muted">
                            {{ $producto->vida_util ? $producto->vida_util . ' meses' : 'No definida' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Observaciones -->
    @if($producto->observaciones)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-sticky-note"></i> Observaciones</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">{{ $producto->observaciones }}</p>
        </div>
    </div>
    @endif

    <!-- Información de Auditoría -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-clock"></i> Información de Registro</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        <strong>Creado:</strong> {{ $producto->created_at ? $producto->created_at->format('d/m/Y H:i') : 'No disponible' }}
                    </small>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">
                        <strong>Última actualización:</strong> {{ $producto->updated_at ? $producto->updated_at->format('d/m/Y H:i') : 'No disponible' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción al final -->
    <div class="d-flex justify-content-between mt-4">
        <div>
            <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar Producto
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </div>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i> Ver Todos los Productos
        </a>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar el producto <strong>{{ $producto->nombre }}</strong>?
                <br><small class="text-muted">Esta acción no se puede deshacer.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection