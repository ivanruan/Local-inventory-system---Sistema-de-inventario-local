@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle"></i> Nuevo Movimiento de Inventario</h1>
        <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Información del Movimiento</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('movimientos.store') }}" method="POST" id="movimientoForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo de Movimiento <span class="text-danger">*</span></label>
                                <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="entrada" {{ old('tipo') == 'entrada' ? 'selected' : '' }}>
                                        <i class="fas fa-arrow-down"></i> Entrada
                                    </option>
                                    <option value="salida" {{ old('tipo') == 'salida' ? 'selected' : '' }}>
                                        <i class="fas fa-arrow-up"></i> Salida
                                    </option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fecha_hora" class="form-label">Fecha y Hora <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="fecha_hora" id="fecha_hora" 
                                       class="form-control @error('fecha_hora') is-invalid @enderror" 
                                       value="{{ old('fecha_hora', now()->format('Y-m-d\TH:i')) }}" required>
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
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" 
                                                data-stock="{{ $producto->stock }}"
                                                data-unidad="{{ $producto->unidad }}"
                                                {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
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
                                    <input type="number" name="cantidad" id="cantidad" 
                                           class="form-control @error('cantidad') is-invalid @enderror" 
                                           value="{{ old('cantidad') }}" 
                                           step="0.01" min="0.01" required>
                                    <span class="input-group-text" id="unidad-display">Unidad</span>
                                </div>
                                @error('cantidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="campos-entrada" class="d-none">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="proveedor_id" class="form-label">Proveedor</label>
                                    <select name="proveedor_id" id="proveedor_id" class="form-select @error('proveedor_id') is-invalid @enderror">
                                        <option value="">Seleccionar proveedor...</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
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
                                        <input type="number" name="precio_unitario" id="precio_unitario" 
                                               class="form-control @error('precio_unitario') is-invalid @enderror" 
                                               value="{{ old('precio_unitario') }}" 
                                               step="0.01" min="0">
                                    </div>
                                    @error('precio_unitario')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="campos-salida" class="d-none">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="proyecto_id" class="form-label">Proyecto</label>
                                    <select name="proyecto_id" id="proyecto_id" class="form-select @error('proyecto_id') is-invalid @enderror">
                                        <option value="">Seleccionar proyecto...</option>
                                        @foreach($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id }}" {{ old('proyecto_id') == $proyecto->id ? 'selected' : '' }}>
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
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" {{ old('usuario_destino') == $usuario->id ? 'selected' : '' }}>
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
                                <textarea name="observaciones" id="observaciones" 
                                          class="form-control @error('observaciones') is-invalid @enderror" 
                                          rows="3" placeholder="Detalles adicionales del movimiento...">{{ old('observaciones') }}</textarea>
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
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Registrar Movimiento
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Producto</h5>
                </div>
                <div class="card-body">
                    <div id="info-producto" class="d-none">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Código:</strong></div>
                            <div class="col-6" id="producto-codigo">-</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Nombre:</strong></div>
                            <div class="col-6" id="producto-nombre">-</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Stock Actual:</strong></div>
                            <div class="col-6">
                                <span id="producto-stock" class="fw-bold">0</span>
                                <span id="producto-unidad" class="text-muted">unidades</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Estado:</strong></div>
                            <div class="col-6">
                                <span id="stock-status" class="badge bg-secondary">Normal</span>
                            </div>
                        </div>
                    </div>
                    <div id="sin-producto" class="text-center text-muted">
                        <i class="fas fa-box fa-2x mb-2"></i>
                        <p>Selecciona un producto para ver su información</p>
                    </div>
                </div>
            </div>

            <div id="alerta-stock" class="alert alert-warning mt-3 d-none">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>¡Atención!</strong> Este producto tiene stock bajo.
            </div>

            <div id="confirmacion-salida" class="alert alert-danger mt-3 d-none">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>¡Cuidado!</strong> La cantidad solicitada excede el stock disponible.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/movimientos/movimientos_create.js') }}"></script>
@endpush