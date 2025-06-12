@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-file-alt"></i> Detalles del Movimiento #{{ $movimiento->id }}
        </h1>
        <div>
            <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
            <a href="{{ route('movimientos.edit', $movimiento->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar Movimiento
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Movimiento</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Tipo de Movimiento:</strong></p>
                            @if($movimiento->tipo == 'entrada')
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-arrow-down"></i> Entrada
                                </span>
                            @else
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-arrow-up"></i> Salida
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Fecha y Hora:</strong></p>
                            <p class="lead mb-0">
                                {{ $movimiento->fecha_hora->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Producto:</strong></p>
                            <p class="fs-5 mb-0">
                                <strong>{{ $movimiento->producto->codigo ?? 'N/A' }}</strong>
                            </p>
                            <p class="mb-0 text-muted">
                                {{ $movimiento->producto->nombre ?? 'Producto no encontrado' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Cantidad:</strong></p>
                            <p class="fs-4 fw-bold mb-0 {{ $movimiento->tipo == 'entrada' ? 'text-success' : 'text-danger' }}">
                                {{ $movimiento->tipo == 'entrada' ? '+' : '-' }}{{ number_format($movimiento->cantidad, 2) }}
                                <small class="text-muted">{{ $movimiento->producto->unidad ?? '' }}</small>
                            </p>
                        </div>
                    </div>

                    @if($movimiento->tipo == 'entrada')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-0"><strong>Proveedor:</strong></p>
                                <p class="fs-5 mb-0">
                                    {{ $movimiento->proveedor->nombre ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-0"><strong>Precio Unitario:</strong></p>
                                <p class="fs-5 mb-0">
                                    ${{ number_format($movimiento->precio_unitario, 2) }}
                                </p>
                            </div>
                        </div>
                    @elseif($movimiento->tipo == 'salida')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-0"><strong>Proyecto:</strong></p>
                                <p class="fs-5 mb-0">
                                    {{ $movimiento->proyecto->nombre ?? 'N/A' }}
                                </p>
                            </div>
                            {{-- El campo "Usuario Destino" ahora se muestra en la sección de "Registrado Por / Destinatario" --}}
                            {{-- Aquí ya no es necesario mostrarlo de forma duplicada --}}
                            {{-- <div class="col-md-6">
                                <p class="mb-0"><strong>Usuario Destino:</strong></p>
                                <p class="fs-5 mb-0">
                                    {{ $movimiento->usuario_destino_rel->nombre ?? 'N/A' }}
                                </p>
                            </div> --}}
                        </div>
                    @endif

                    <div class="mb-3">
                        <p class="mb-0"><strong>Observaciones:</strong></p>
                        <p class="text-muted">
                            {{ $movimiento->observaciones ?? 'Sin observaciones.' }}
                        </p>
                    </div>

                    <div class="border-top pt-3">
                        <p class="mb-0">
                            {{-- Ajusta el texto según el tipo de movimiento --}}
                            <strong>{{ $movimiento->tipo == 'salida' ? 'Destinatario:' : 'Registrado Por:' }}</strong>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-user-circle"></i> {{ $movimiento->usuario->nombre ?? 'Usuario Desconocido' }}
                            <small class="text-muted">({{ $movimiento->created_at->format('d/m/Y H:i') }})</small>
                        </p>
                        @if ($movimiento->updated_at != $movimiento->created_at)
                            <p class="mb-0">
                                <i class="fas fa-history"></i> Última Actualización: 
                                <small class="text-muted">{{ $movimiento->updated_at->format('d/m/Y H:i') }}</small>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Información del Producto (similar al de create) -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-boxes"></i> Stock Actual del Producto</h5>
                </div>
                <div class="card-body">
                    @if($movimiento->producto)
                        <div class="row mb-2">
                            <div class="col-6"><strong>Stock Actual:</strong></div>
                            <div class="col-6">
                                <span class="fw-bold fs-5">{{ number_format($movimiento->producto->stock_actual, 2) }}</span>
                                <span class="text-muted">{{ $movimiento->producto->unidad }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Estado:</strong></div>
                            <div class="col-6">
                                @php
                                    $stock = $movimiento->producto->stock_actual; // Usar stock_actual
                                    $statusClass = '';
                                    $statusText = '';
                                    if ($stock <= 0) {
                                        $statusClass = 'bg-danger';
                                        $statusText = 'Sin Stock';
                                    } elseif ($stock <= 10) { // Umbral de stock bajo, puedes ajustarlo
                                        $statusClass = 'bg-warning';
                                        $statusText = 'Stock Bajo';
                                    } else {
                                        $statusClass = 'bg-success';
                                        $statusText = 'Stock Normal';
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p>Producto no disponible o eliminado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
