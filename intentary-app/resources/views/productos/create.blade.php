@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Producto</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <label for="codigo">Código</label>
                <input type="text" name="codigo" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="especificacion">Especificación</label>
            <textarea name="especificacion" class="form-control"></textarea>
        </div>

        <!-- Marca -->
        <div class="form-group mt-3">
            <label for="marca_id">Marca</label>
            <div class="d-flex">
                <select name="marca_id" id="marca_id" class="form-control me-2" onchange="toggleInput('marca_id', 'nueva_marca', 'btn_agregar_marca')">
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                    @endforeach
                    <option value="nueva">Agregar nuevo</option>
                </select>
                <input type="text" name="nueva_marca" id="nueva_marca" class="form-control me-2" placeholder="Nueva marca" style="display:none;">
                <button type="button" class="btn btn-outline-primary" id="btn_agregar_marca" style="display:none;" onclick="agregarMarca()">Agregar</button>
            </div>
        </div>

        <!-- Categoría -->
        <div class="form-group mt-3">
            <label for="categoria_id">Categoría</label>
            <div class="d-flex">
                <select name="categoria_id" id="categoria_id" class="form-control me-2" onchange="toggleInput('categoria_id', 'nueva_categoria', 'btn_agregar_categoria')">
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                    <option value="nueva">Agregar nuevo</option>
                </select>
                <input type="text" name="nueva_categoria" id="nueva_categoria" class="form-control me-2" placeholder="Nueva categoría" style="display:none;">
                <button type="button" class="btn btn-outline-primary" id="btn_agregar_categoria" style="display:none;" onclick="agregarCategoria()">Agregar</button>
            </div>
        </div>

        <!-- Ubicación -->
        <!-- Ubicación -->
        <div class="form-group mt-3">
            <label for="ubicacion_id">Ubicación</label>
            <div class="d-flex">
                <select name="ubicacion_id" id="ubicacion_id" class="form-control me-2" onchange="toggleInput('ubicacion_id', 'nueva_ubicacion', 'btn_agregar_ubicacion')">
                    @foreach($ubicaciones as $ubicacion)
                        <option value="{{ $ubicacion->id }}">{{ $ubicacion->codigo }} - Nivel {{ $ubicacion->nivel }}</option>
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
                <input type="text" name="unidad" class="form-control" required>
            </div>
            
            <div class="col-md-4">
                <label for="stock_actual">Stock actual</label>
                <input type="number" name="stock_actual" class="form-control" required>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <label for="stock_minimo">Stock mínimo</label>
                <input type="number" name="stock_minimo" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="stock_maximo">Stock máximo</label>
                <input type="number" name="stock_maximo" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="stock_seguridad">Stock seguridad</label>
                <input type="number" name="stock_seguridad" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="duracion_inventario">Duración Inventario</label>
                <input type="number" name="duracion_inventario" class="form-control">
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="costo">Costo</label>
            <input type="number" name="costo" step="0.01" class="form-control">
        </div>

        <div class="form-group mt-3">
            <label for="vida_util">Vida útil</label>
            <input type="number" name="vida_util" class="form-control">
        </div>

        <div class="form-group mt-3">
            <label for="status">Estado</label>
            <select name="status" class="form-control">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success mt-4">Guardar Producto</button>
    </form>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/productos/create.js') }}"></script>
@endpush