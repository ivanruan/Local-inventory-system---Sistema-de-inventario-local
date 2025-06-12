@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit"></i> Editar Movimiento de Inventario #{{ $movimiento->id }}</h1>
        <a href="{{ route('movimientos.show', $movimiento->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Detalles
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-pencil-alt"></i> Información del Movimiento</h5>
                </div>
                <div class="card-body">
                    {{-- El formulario apunta a la ruta 'update' y utiliza el método PUT --}}
                    <form action="{{ route('movimientos.update', $movimiento->id) }}" method="POST" id="movimientoForm">
                        @csrf
                        @method('PUT') {{-- Importante para las solicitudes PUT/PATCH en Laravel --}}
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo de Movimiento <span class="text-danger">*</span></label>
                                <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                    <option value="">Seleccionar tipo...</option>
                                    {{-- Se pre-selecciona el tipo actual del movimiento --}}
                                    <option value="entrada" {{ old('tipo', $movimiento->tipo) == 'entrada' ? 'selected' : '' }}>
                                        <i class="fas fa-arrow-down"></i> Entrada
                                    </option>
                                    <option value="salida" {{ old('tipo', $movimiento->tipo) == 'salida' ? 'selected' : '' }}>
                                        <i class="fas fa-arrow-up"></i> Salida
                                    </option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fecha_hora" class="form-label">Fecha y Hora <span class="text-danger">*</span></label>
                                {{-- Se pre-llena con la fecha y hora actuales del movimiento --}}
                                <input type="datetime-local" name="fecha_hora" id="fecha_hora" 
                                       class="form-control @error('fecha_hora') is-invalid @enderror" 
                                       value="{{ old('fecha_hora', $movimiento->fecha_hora->format('Y-m-d\TH:i')) }}" required>
                                @error('fecha_hora')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="producto_id" class="form-label">Producto <span class="text-danger">*</span></label>
                                <select name="producto_id" id="producto_id" class="form-select @error('producto_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar producto...</option>
                                    {{-- Se pre-selecciona el producto actual del movimiento --}}
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" 
                                                data-stock="{{ $producto->stock_actual }}" {{-- Usar stock_actual --}}
                                                data-unidad="{{ $producto->unidad }}"
                                                {{ old('producto_id', $movimiento->producto_id) == $producto->id ? 'selected' : '' }}>
                                            {{ $producto->codigo }} - {{ $producto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('producto_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cantidad" class="form-label">Cantidad <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    {{-- Se pre-llena con la cantidad actual del movimiento --}}
                                    <input type="number" name="cantidad" id="cantidad" 
                                           class="form-control @error('cantidad') is-invalid @enderror" 
                                           value="{{ old('cantidad', $movimiento->cantidad) }}" 
                                           step="0.01" min="0.01" required>
                                    <span class="input-group-text" id="unidad-display">Unidad</span>
                                </div>
                                @error('cantidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Campos condicionales según el tipo, su visibilidad será controlada por JS --}}
                        <div id="campos-entrada" class="{{ old('tipo', $movimiento->tipo) == 'entrada' ? '' : 'd-none' }}">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="proveedor_id" class="form-label">Proveedor</label>
                                    <select name="proveedor_id" id="proveedor_id" class="form-select @error('proveedor_id') is-invalid @enderror">
                                        <option value="">Seleccionar proveedor...</option>
                                        {{-- Se pre-selecciona el proveedor actual --}}
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $movimiento->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proveedor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="precio_unitario" class="form-label">Precio Unitario</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        {{-- Se pre-llena con el precio unitario actual --}}
                                        <input type="number" name="precio_unitario" id="precio_unitario" 
                                               class="form-control @error('precio_unitario') is-invalid @enderror" 
                                               value="{{ old('precio_unitario', $movimiento->precio_unitario) }}" 
                                               step="0.01" min="0">
                                    </div>
                                    @error('precio_unitario')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="campos-salida" class="{{ old('tipo', $movimiento->tipo) == 'salida' ? '' : 'd-none' }}">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="proyecto_id" class="form-label">Proyecto</label>
                                    <select name="proyecto_id" id="proyecto_id" class="form-select @error('proyecto_id') is-invalid @enderror">
                                        <option value="">Seleccionar proyecto...</option>
                                        {{-- Se pre-selecciona el proyecto actual --}}
                                        @foreach($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id }}" {{ old('proyecto_id', $movimiento->proyecto_id) == $proyecto->id ? 'selected' : '' }}>
                                                {{ $proyecto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proyecto_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="usuario_destino" class="form-label">Usuario Destino</label>
                                    <select name="usuario_destino" id="usuario_destino" class="form-select @error('usuario_destino') is-invalid @enderror">
                                        <option value="">Seleccionar usuario...</option>
                                        {{-- Se pre-selecciona el usuario destino actual.
                                             En tu controlador, si el movimiento es de salida, `usuario_id` es el `usuario_destino`.
                                             Así que usamos $movimiento->usuario_id. --}}
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" {{ old('usuario_destino', $movimiento->usuario_id) == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('usuario_destino')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                {{-- Se pre-llena con las observaciones actuales --}}
                                <textarea name="observaciones" id="observaciones" 
                                          class="form-control @error('observaciones') is-invalid @enderror" 
                                          rows="3" placeholder="Detalles adicionales del movimiento...">{{ old('observaciones', $movimiento->observaciones) }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary"> {{-- Cambiado a btn-primary para diferenciar --}}
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de información del producto (similar al de create/show) -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Producto</h5>
                </div>
                <div class="card-body">
                    {{-- La visibilidad inicial se basa en si hay un producto seleccionado --}}
                    <div id="info-producto" class="{{ $movimiento->producto_id ? '' : 'd-none' }}">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Código:</strong></div>
                            <div class="col-6" id="producto-codigo">{{ $movimiento->producto->codigo ?? '-' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Nombre:</strong></div>
                            <div class="col-6" id="producto-nombre">{{ $movimiento->producto->nombre ?? '-' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Stock Actual:</strong></div>
                            <div class="col-6">
                                <span id="producto-stock" class="fw-bold">{{ number_format($movimiento->producto->stock_actual ?? 0, 2) }}</span>
                                <span id="producto-unidad" class="text-muted">{{ $movimiento->producto->unidad ?? 'unidades' }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Estado:</strong></div>
                            <div class="col-6">
                                @php
                                    $stock = $movimiento->producto->stock_actual ?? 0;
                                    $statusClass = '';
                                    $statusText = '';
                                    if ($stock <= 0) {
                                        $statusClass = 'bg-danger';
                                        $statusText = 'Sin Stock';
                                    } elseif ($stock <= 10) { 
                                        $statusClass = 'bg-warning';
                                        $statusText = 'Stock Bajo';
                                    } else {
                                        $statusClass = 'bg-success';
                                        $statusText = 'Stock Normal';
                                    }
                                @endphp
                                <span id="stock-status" class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                        </div>
                    </div>
                    <div id="sin-producto" class="{{ $movimiento->producto_id ? 'd-none' : '' }} text-center text-muted">
                        <i class="fas fa-box fa-2x mb-2"></i>
                        <p>Selecciona un producto para ver su información</p>
                    </div>
                </div>
            </div>

            <!-- Alerta de stock bajo -->
            <div id="alerta-stock" class="alert alert-warning mt-3 {{ (($movimiento->producto->stock_actual ?? 0) > 0 && ($movimiento->producto->stock_actual ?? 0) <= 10) ? '' : 'd-none' }}">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>¡Atención!</strong> Este producto tiene stock bajo.
            </div>

            <!-- Confirmación de salida (inicialmente oculto, JS lo mostrará) -->
            <div id="confirmacion-salida" class="alert alert-danger mt-3 d-none">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>¡Cuidado!</strong> La cantidad solicitada excede el stock disponible.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Reutilizamos el mismo script que para 'create' ya que la lógica dinámica es idéntica --}}
<script src="{{ asset('js/movimientos/movimientos_create.js') }}"></script>
@endpush
