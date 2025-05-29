@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Producto</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Corrige los siguientes errores:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.update', $producto->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Datos básicos --}}
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
            <textarea name="especificacion" id="especificacion" class="form-control" rows="2">{{ old('especificacion', $producto->especificacion) }}</textarea>
        </div>

        {{-- Relaciones --}}
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="marca_id" class="form-label">Marca</label>
                <select name="marca_id" id="marca_id" class="form-select" required>
                    @foreach ($marcas as $marca)
                        <option value="{{ $marca->id }}" {{ $producto->marca_id == $marca->id ? 'selected' : '' }}>
                            {{ $marca->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select name="categoria_id" id="categoria_id" class="form-select" required>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="ubicacion_id" class="form-label">Ubicación</label>
                <select name="ubicacion_id" id="ubicacion_id" class="form-select" required>
                    @foreach ($ubicaciones as $ubicacion)
                        <option value="{{ $ubicacion->id }}" {{ $producto->ubicacion_id == $ubicacion->id ? 'selected' : '' }}>
                            {{ $ubicacion->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Más campos --}}
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="unidad" class="form-label">Unidad</label>
                <input type="text" name="unidad" id="unidad" class="form-control" value="{{ old('unidad', $producto->unidad) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label for="nivel" class="form-label">Nivel</label>
                <input type="number" name="nivel" id="nivel" class="form-control" min="0" value="{{ old('nivel', $producto->nivel) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label for="costo" class="form-label">Costo ($)</label>
                <input type="number" name="costo" id="costo" class="form-control" min="0" step="0.01" value="{{ old('costo', $producto->costo) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label for="status" class="form-label">Estado</label>
                <select name="status" id="status" class="form-select">
                    <option value="activo" {{ $producto->status === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ $producto->status === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    <option value="obsoleto" {{ $producto->status === 'obsoleto' ? 'selected' : '' }}>Obsoleto</option>
                </select>
            </div>
        </div>

        {{-- Stocks --}}
        <div class="row">
            @foreach ([
                'stock_minimo' => 'Stock Mínimo',
                'stock_actual' => 'Stock Actual',
                'stock_maximo' => 'Stock Máximo',
                'stock_seguridad' => 'Stock Seguridad'
            ] as $name => $label)
                <div class="col-md-3 mb-3">
                    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
                    <input type="number" name="{{ $name }}" id="{{ $name }}" class="form-control" min="0" value="{{ old($name, $producto->$name) }}">
                </div>
            @endforeach
        </div>

        {{-- Tiempos --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="duracion_inventario" class="form-label">Duración Inventario (días)</label>
                <input type="number" name="duracion_inventario" id="duracion_inventario" class="form-control" min="0" value="{{ old('duracion_inventario', $producto->duracion_inventario) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="vida_util" class="form-label">Vida Útil (meses)</label>
                <input type="number" name="vida_util" id="vida_util" class="form-control" min="0" value="{{ old('vida_util', $producto->vida_util) }}">
            </div>
        </div>

        {{-- Observaciones --}}
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control" rows="2">{{ old('observaciones', $producto->observaciones) }}</textarea>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Actualizar Producto</button>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection

