@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Producto</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.update', $producto->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" name="codigo" id="codigo" class="form-control" value="{{ old('codigo', $producto->codigo) }}" required>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $producto->nombre) }}" required>
        </div>

        <div class="mb-3">
            <label for="especificacion" class="form-label">Especificación</label>
            <textarea name="especificacion" id="especificacion" class="form-control">{{ old('especificacion', $producto->especificacion) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="marca_id" class="form-label">Marca</label>
            <select name="marca_id" id="marca_id" class="form-control" required>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id }}" {{ $producto->marca_id == $marca->id ? 'selected' : '' }}>
                        {{ $marca->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría</label>
            <select name="categoria_id" id="categoria_id" class="form-control" required>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="ubicacion_id" class="form-label">Ubicación</label>
            <select name="ubicacion_id" id="ubicacion_id" class="form-control" required>
                @foreach ($ubicaciones as $ubicacion)
                    <option value="{{ $ubicacion->id }}" {{ $producto->ubicacion_id == $ubicacion->id ? 'selected' : '' }}>
                        {{ $ubicacion->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="unidad" class="form-label">Unidad</label>
            <input type="text" name="unidad" id="unidad" class="form-control" value="{{ old('unidad', $producto->unidad) }}">
        </div>

        <div class="mb-3">
            <label for="nivel" class="form-label">Nivel</label>
            <input type="number" name="nivel" id="nivel" class="form-control" value="{{ old('nivel', $producto->nivel) }}">
        </div>

        <div class="mb-3">
            <label for="stock_minimo" class="form-label">Stock mínimo</label>
            <input type="number" name="stock_minimo" id="stock_minimo" class="form-control" value="{{ old('stock_minimo', $producto->stock_minimo) }}">
        </div>

        <div class="mb-3">
            <label for="stock_actual" class="form-label">Stock actual</label>
            <input type="number" name="stock_actual" id="stock_actual" class="form-control" value="{{ old('stock_actual', $producto->stock_actual) }}">
        </div>

        <div class="mb-3">
            <label for="stock_maximo" class="form-label">Stock máximo</label>
            <input type="number" name="stock_maximo" id="stock_maximo" class="form-control" value="{{ old('stock_maximo', $producto->stock_maximo) }}">
        </div>

        <div class="mb-3">
            <label for="stock_seguridad" class="form-label">Stock seguridad</label>
            <input type="number" name="stock_seguridad" id="stock_seguridad" class="form-control" value="{{ old('stock_seguridad', $producto->stock_seguridad) }}">
        </div>

        <div class="mb-3">
            <label for="duracion_inventario" class="form-label">Duración Inventario (días)</label>
            <input type="number" name="duracion_inventario" id="duracion_inventario" class="form-control" value="{{ old('duracion_inventario', $producto->duracion_inventario) }}">
        </div>

        <div class="mb-3">
            <label for="vida_util" class="form-label">Vida útil (meses)</label>
            <input type="number" name="vida_util" id="vida_util" class="form-control" value="{{ old('vida_util', $producto->vida_util) }}">
        </div>

        <div class="mb-3">
            <label for="costo" class="form-label">Costo</label>
            <input type="number" step="0.01" name="costo" id="costo" class="form-control" value="{{ old('costo', $producto->costo) }}">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="activo" {{ $producto->status === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ $producto->status === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control">{{ old('observaciones', $producto->observaciones) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

