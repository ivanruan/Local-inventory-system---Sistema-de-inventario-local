@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Producto</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('productos.update', $producto->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $producto->nombre) }}" required onchange="generarCodigo()">
            </div>
            
            <div class="col-md-6">
                <label for="especificacion">Especificación</label>
                <textarea name="especificacion" id="especificacion" class="form-control" onchange="generarCodigo()">{{ old('especificacion', $producto->especificacion) }}</textarea>
            </div>
        </div>

        <!-- Categoría -->
        <div class="form-group mt-3">
            <label for="categoria_id">Categoría</label>
            <div class="d-flex">
                <select name="categoria_id" id="categoria_id" class="form-control me-2" onchange="toggleInput('categoria_id', 'nueva_categoria', 'btn_agregar_categoria'); generarCodigo()">
                    <option value="">Seleccionar categoría</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" 
                                data-codigo="{{ $categoria->codigo ?? substr($categoria->nombre, 0, 3) }}"
                                {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                    <option value="nueva">Agregar nuevo</option>
                </select>
                <input type="text" name="nueva_categoria" id="nueva_categoria" class="form-control me-2" placeholder="Nueva categoría" style="display:none;">
                <button type="button" class="btn btn-outline-primary" id="btn_agregar_categoria" style="display:none;" onclick="agregarCategoria()">Agregar</button>
            </div>
        </div>

        <!-- Marca -->
        <div class="form-group mt-3">
            <label for="marca_id">Marca</label>
            <div class="d-flex">
                <select name="marca_id" id="marca_id" class="form-control me-2" onchange="toggleInput('marca_id', 'nueva_marca', 'btn_agregar_marca')">
                    <option value="">Seleccionar marca</option>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}" {{ old('marca_id', $producto->marca_id) == $marca->id ? 'selected' : '' }}>
                            {{ $marca->nombre }}
                        </option>
                    @endforeach
                    <option value="nueva">Agregar nuevo</option>
                </select>
                <input type="text" name="nueva_marca" id="nueva_marca" class="form-control me-2" placeholder="Nueva marca" style="display:none;">
                <button type="button" class="btn btn-outline-primary" id="btn_agregar_marca" style="display:none;" onclick="agregarMarca()">Agregar</button>
            </div>
        </div>

        <!-- Ubicación -->
        <div class="form-group mt-3">
            <label for="ubicacion_id">Ubicación</label>
            <div class="d-flex">
                <select name="ubicacion_id" id="ubicacion_id" class="form-control me-2" onchange="toggleInput('ubicacion_id', 'nueva_ubicacion', 'btn_agregar_ubicacion'); generarCodigo()">
                    <option value="">Seleccionar ubicación</option>
                    @foreach($ubicaciones as $ubicacion)
                        <option value="{{ $ubicacion->id }}" 
                                data-codigo="{{ $ubicacion->codigo }}" 
                                data-nivel="{{ $ubicacion->nivel }}"
                                {{ old('ubicacion_id', $producto->ubicacion_id) == $ubicacion->id ? 'selected' : '' }}>
                            {{ $ubicacion->codigo }} - Nivel {{ $ubicacion->nivel }}
                        </option>
                    @endforeach
                    <option value="nueva">Agregar nuevo</option>
                </select>
                <input type="text" name="nueva_ubicacion" id="nueva_ubicacion" class="form-control me-2" placeholder="Código - Nivel (ej: A1 - 2)" style="display:none;">
                <button type="button" class="btn btn-outline-primary" id="btn_agregar_ubicacion" style="display:none;" onclick="agregarUbicacion()">Agregar</button>
            </div>
        </div>

        <!-- Otros campos -->
        <div class="row mt-3">        
            <div class="col-md-4">
                <label for="unidad">Unidad</label>
                <input type="text" name="unidad" class="form-control" value="{{ old('unidad', $producto->unidad) }}" required>
            </div>
            
            <div class="col-md-4">
                <label for="stock_actual">Stock actual</label>
                <input type="number" name="stock_actual" class="form-control" value="{{ old('stock_actual', $producto->stock_actual) }}" required>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <label for="stock_minimo">Stock mínimo</label>
                <input type="number" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', $producto->stock_minimo) }}">
            </div>
            <div class="col-md-3">
                <label for="stock_maximo">Stock máximo</label>
                <input type="number" name="stock_maximo" class="form-control" value="{{ old('stock_maximo', $producto->stock_maximo) }}">
            </div>
            <div class="col-md-3">
                <label for="stock_seguridad">Stock seguridad</label>
                <input type="number" name="stock_seguridad" class="form-control" value="{{ old('stock_seguridad', $producto->stock_seguridad) }}">
            </div>
            <div class="col-md-3">
                <label for="duracion_inventario">Duración Inventario</label>
                <input type="number" name="duracion_inventario" class="form-control" value="{{ old('duracion_inventario', $producto->duracion_inventario) }}">
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="costo">Costo</label>
            <input type="number" name="costo" step="0.01" class="form-control" value="{{ old('costo', $producto->costo) }}">
        </div>

        <div class="form-group mt-3">
            <label for="vida_util">Vida útil</label>
            <input type="number" name="vida_util" class="form-control" value="{{ old('vida_util', $producto->vida_util) }}">
        </div>

        <div class="form-group mt-3">
            <label for="status">Estado</label>
            <select name="status" class="form-control" required>
                <option value="">Seleccionar estado</option>
                <option value="Activo" {{ old('status', $producto->status) == 'Activo' ? 'selected' : '' }}>Activo</option>
                <option value="Inactivo" {{ old('status', $producto->status) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                <option value="Obsoleto" {{ old('status', $producto->status) == 'Obsoleto' ? 'selected' : '' }}>Obsoleto</option>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" class="form-control">{{ old('observaciones', $producto->observaciones) }}</textarea>
        </div>

        <!-- Mostrar código generado al final del formulario -->
        <div class="card mt-4" id="codigo_preview">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Código del Producto</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <strong>Código Actual:</strong> <span id="codigo_display" class="font-monospace fs-4">{{ $producto->codigo }}</span>
                </div>
                <small class="text-muted">
                    El código se actualiza automáticamente basado en: Categoría + 3 primeras letras del nombre + 4 primeras letras de la especificación + Código de ubicación + Nivel de ubicación + ID del producto
                    <br><strong>ID actual:</strong> {{ $producto->id }}
                </small>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-success">Actualizar Producto</button>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
        
        <!-- Campo de código generado (oculto para envío) -->
        <input type="hidden" name="codigo" id="codigo_hidden" value="{{ $producto->codigo }}">
    </form>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/productos/main.js') }}"></script>
    <script>
        // Inicializar el código al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            generarCodigo();
        });
    </script>
@endpush

