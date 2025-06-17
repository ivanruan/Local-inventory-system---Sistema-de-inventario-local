@extends('layouts.app')

@section('title', 'Editar Producto: ' . $producto->nombre)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
<style>
.form-section {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 0.375rem;
}

.form-section h6 {
    color: #007bff;
    font-weight: 600;
    margin-bottom: 1rem;
}

.required-field::after {
    content: " *";
    color: #dc3545;
}

.form-help {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.stock-calculator {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-top: 1rem;
}

.preview-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    position: sticky;
    top: 20px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Producto: <span class="text-primary">{{ $producto->nombre }}</span></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('productos.show', $producto->id) }}">{{ $producto->codigo }}</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye"></i> Ver Detalles
            </a>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6><i class="bi bi-exclamation-triangle"></i> Por favor corrige los siguientes errores:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('productos.update', $producto->id) }}" method="POST" id="productoForm" novalidate>
        @csrf
        @method('PUT') {{-- Importante para las actualizaciones en Laravel --}}
        <div class="row">
            <div class="col-lg-8">
                {{-- Información Básica --}}
                <div class="form-section">
                    <h6><i class="bi bi-info-circle"></i> Información Básica</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label required-field">Código</label>
                            <input type="text"
                                   class="form-control @error('codigo') is-invalid @enderror"
                                   id="codigo"
                                   name="codigo"
                                   value="{{ old('codigo', $producto->codigo) }}"
                                   required
                                   maxlength="50"
                                   placeholder="Ej: PROD-001">
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-help">Código único del producto</div>
                        </div>

                        <div class="col-md-8">
                            <label for="nombre" class="form-label required-field">Nombre del Producto</label>
                            <input type="text"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre"
                                   name="nombre"
                                   value="{{ old('nombre', $producto->nombre) }}"
                                   required
                                   maxlength="255"
                                   placeholder="Nombre descriptivo del producto">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="especificacion" class="form-label">Especificación</label>
                            <textarea class="form-control @error('especificacion') is-invalid @enderror"
                                      id="especificacion"
                                      name="especificacion"
                                      rows="3"
                                      placeholder="Descripción detallada, características técnicas, etc.">{{ old('especificacion', $producto->especificacion) }}</textarea>
                            @error('especificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Clasificación --}}
                <div class="form-section">
                    <h6><i class="bi bi-tags"></i> Clasificación</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="categoria_id" class="form-label required-field">Categoría</label>
                            <select class="form-select @error('categoria_id') is-invalid @enderror"
                                    id="categoria_id"
                                    name="categoria_id"
                                    required>
                                <option value="">Seleccionar...</option>
                                @foreach($categorias ?? [] as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="marca_id" class="form-label">Marca</label>
                            <select class="form-select @error('marca_id') is-invalid @enderror"
                                    id="marca_id"
                                    name="marca_id">
                                <option value="">Sin marca</option>
                                @foreach($marcas ?? [] as $marca)
                                    <option value="{{ $marca->id }}"
                                        {{ old('marca_id', $producto->marca_id) == $marca->id ? 'selected' : '' }}>
                                        {{ $marca->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('marca_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="proveedor_id" class="form-label">Proveedor</label>
                            <select class="form-select @error('proveedor_id') is-invalid @enderror"
                                    id="proveedor_id"
                                    name="proveedor_id">
                                <option value="">Sin proveedor</option>
                                @foreach($proveedores ?? [] as $proveedor)
                                    <option value="{{ $proveedor->id }}"
                                        {{ old('proveedor_id', $producto->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proveedor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Ubicación y Medidas --}}
                <div class="form-section">
                    <h6><i class="bi bi-geo-alt"></i> Ubicación y Medidas</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="ubicacion_id" class="form-label required-field">Ubicación</label>
                            <select class="form-select @error('ubicacion_id') is-invalid @enderror"
                                    id="ubicacion_id"
                                    name="ubicacion_id"
                                    required>
                                <option value="">Seleccionar...</option>
                                @foreach($ubicaciones ?? [] as $ubicacion)
                                    <option value="{{ $ubicacion->id }}"
                                        {{ old('ubicacion_id', $producto->ubicacion_id) == $ubicacion->id ? 'selected' : '' }}>
                                        {{ $ubicacion->codigo }} - {{ $ubicacion->descripcion ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ubicacion_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="unidad" class="form-label required-field">Unidad de Medida</label>
                            <select class="form-select @error('unidad') is-invalid @enderror"
                                    id="unidad"
                                    name="unidad"
                                    required>
                                <option value="">Seleccionar...</option>
                                <option value="PZA" {{ old('unidad', $producto->unidad) == 'PZA' ? 'selected' : '' }}>Pieza (PZA)</option>
                                <option value="KG" {{ old('unidad', $producto->unidad) == 'KG' ? 'selected' : '' }}>Kilogramo (KG)</option>
                                <option value="LT" {{ old('unidad', $producto->unidad) == 'LT' ? 'selected' : '' }}>Litro (LT)</option>
                                <option value="MT" {{ old('unidad', $producto->unidad) == 'MT' ? 'selected' : '' }}>Metro (MT)</option>
                                <option value="M2" {{ old('unidad', $producto->unidad) == 'M2' ? 'selected' : '' }}>Metro Cuadrado (M2)</option>
                                <option value="M3" {{ old('unidad', $producto->unidad) == 'M3' ? 'selected' : '' }}>Metro Cúbico (M3)</option>
                                <option value="CAJA" {{ old('unidad', $producto->unidad) == 'CAJA' ? 'selected' : '' }}>Caja</option>
                                <option value="PAQ" {{ old('unidad', $producto->unidad) == 'PAQ' ? 'selected' : '' }}>Paquete (PAQ)</option>
                            </select>
                            @error('unidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="nivel" class="form-label">Nivel</label>
                            <input type="number"
                                   class="form-control @error('nivel') is-invalid @enderror"
                                   id="nivel"
                                   name="nivel"
                                   value="{{ old('nivel', $producto->nivel) }}"
                                   min="0"
                                   max="99">
                            @error('nivel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-help">Nivel de clasificación (0-99)</div>
                        </div>
                    </div>
                </div>

                {{-- Inventario --}}
                <div class="form-section">
                    <h6><i class="bi bi-boxes"></i> Control de Inventario</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="stock_inicial" class="form-label">Stock Inicial</label>
                            <input type="number"
                                   class="form-control @error('stock_inicial') is-invalid @enderror"
                                   id="stock_inicial"
                                   name="stock_inicial"
                                   value="{{ old('stock_inicial', $producto->stock_inicial) }}"
                                   min="0"
                                   step="1">
                            @error('stock_inicial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                            <input type="number"
                                   class="form-control @error('stock_minimo') is-invalid @enderror"
                                   id="stock_minimo"
                                   name="stock_minimo"
                                   value="{{ old('stock_minimo', $producto->stock_minimo) }}"
                                   min="0"
                                   step="1">
                            @error('stock_minimo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="stock_maximo" class="form-label">Stock Máximo</label>
                            <input type="number"
                                   class="form-control @error('stock_maximo') is-invalid @enderror"
                                   id="stock_maximo"
                                   name="stock_maximo"
                                   value="{{ old('stock_maximo', $producto->stock_maximo) }}"
                                   min="0"
                                   step="1">
                            @error('stock_maximo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="stock_seguridad" class="form-label">Stock de Seguridad</label>
                            <input type="number"
                                   class="form-control @error('stock_seguridad') is-invalid @enderror"
                                   id="stock_seguridad"
                                   name="stock_seguridad"
                                   value="{{ old('stock_seguridad', $producto->stock_seguridad) }}"
                                   min="0"
                                   step="1">
                            @error('stock_seguridad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="stock-calculator">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="duracion_inventario" class="form-label">Duración del Inventario (días)</label>
                                <input type="number"
                                       class="form-control @error('duracion_inventario') is-invalid @enderror"
                                       id="duracion_inventario"
                                       name="duracion_inventario"
                                       value="{{ old('duracion_inventario', $producto->duracion_inventario) }}"
                                       min="1"
                                       step="1">
                                @error('duracion_inventario')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="vida_util" class="form-label">Vida Útil (días)</label>
                                <input type="number"
                                       class="form-control @error('vida_util') is-invalid @enderror"
                                       id="vida_util"
                                       name="vida_util"
                                       value="{{ old('vida_util', $producto->vida_util) }}"
                                       min="1"
                                       step="1">
                                @error('vida_util')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Información Económica --}}
                <div class="form-section">
                    <h6><i class="bi bi-currency-dollar"></i> Información Económica</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="costo" class="form-label required-field">Costo Unitario</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control @error('costo') is-invalid @enderror"
                                       id="costo"
                                       name="costo"
                                       value="{{ old('costo', $producto->costo) }}"
                                       min="0"
                                       step="0.01"
                                       required>
                                @error('costo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label required-field">Estado</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status"
                                    required>
                                @foreach(\App\Models\Producto::getStatusOptions() as $statusOption)
                                    <option value="{{ $statusOption }}"
                                        {{ old('status', $producto->status) == $statusOption ? 'selected' : '' }}>
                                        {{ $statusOption }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Valor Total Calculado</label>
                            <div class="form-control-plaintext fw-bold text-success" id="valorTotal">
                                ${{ number_format($producto->stock_actual * $producto->costo, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="form-section">
                    <h6><i class="bi bi-chat-text"></i> Observaciones</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Notas y Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                      id="observaciones"
                                      name="observaciones"
                                      rows="4"
                                      placeholder="Información adicional, notas de mantenimiento, instrucciones especiales, etc.">{{ old('observaciones', $producto->observaciones) }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Lateral de Resumen --}}
            <div class="col-lg-4">
                <div class="preview-card">
                    <h6 class="mb-3"><i class="bi bi-card-checklist"></i> Resumen del Producto</h6>

                    <div class="mb-3">
                        <strong>Código:</strong> <span id="preview-codigo" class="text-muted">{{ $producto->codigo }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Nombre:</strong> <span id="preview-nombre" class="text-muted">{{ $producto->nombre }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Categoría:</strong> <span id="preview-categoria" class="text-muted">{{ $producto->categoria->nombre ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Ubicación:</strong> <span id="preview-ubicacion" class="text-muted">{{ $producto->ubicacion->codigo ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Unidad:</strong> <span id="preview-unidad" class="text-muted">{{ $producto->unidad }}</span>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <strong>Stock Inicial:</strong> <span id="preview-stock-inicial" class="badge bg-info">{{ number_format($producto->stock_inicial) }}</span>
                    </div>

                    <div class="mb-2">
                        <strong>Stock Actual:</strong> <span id="preview-stock-actual" class="badge bg-primary">{{ number_format($producto->stock_actual) }}</span>
                    </div>

                    <div class="mb-2">
                        <strong>Stock Mínimo:</strong> <span id="preview-stock-minimo" class="badge bg-warning">{{ $producto->stock_minimo ? number_format($producto->stock_minimo) : '-' }}</span>
                    </div>

                    <div class="mb-2">
                        <strong>Stock Máximo:</strong> <span id="preview-stock-maximo" class="badge bg-success">{{ $producto->stock_maximo ? number_format($producto->stock_maximo) : '-' }}</span>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <strong>Costo Unitario:</strong> <span id="preview-costo" class="text-success fw-bold">${{ number_format($producto->costo, 2) }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Estado:</strong> <span id="preview-status" class="badge bg-{{ \App\Models\Producto::getStatusColor($producto->status) }}">{{ $producto->status }}</span>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            La información se actualiza automáticamente mientras completas el formulario.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-info" id="previewBtn">
                            <i class="bi bi-eye"></i> Vista Previa
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/product-management/ui/product-form-helpers.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const form = document.getElementById('productoForm');
    const previewElements = {
        codigo: document.getElementById('preview-codigo'),
        nombre: document.getElementById('preview-nombre'),
        categoria: document.getElementById('preview-categoria'),
        ubicacion: document.getElementById('preview-ubicacion'),
        unidad: document.getElementById('preview-unidad'),
        stockInicial: document.getElementById('preview-stock-inicial'),
        stockActual: document.getElementById('preview-stock-actual'), // Add stock actual for edit
        stockMinimo: document.getElementById('preview-stock-minimo'),
        stockMaximo: document.getElementById('preview-stock-maximo'),
        costo: document.getElementById('preview-costo'),
        status: document.getElementById('preview-status'),
        valorTotal: document.getElementById('valorTotal')
    };

    // Función para actualizar la vista previa
    function updatePreview() {
        // Actualizar textos simples
        previewElements.codigo.textContent = document.getElementById('codigo').value || '-';
        previewElements.nombre.textContent = document.getElementById('nombre').value || '-';
        previewElements.unidad.textContent = document.getElementById('unidad').value || '-';

        // Actualizar selects con texto de la opción seleccionada
        const categoriaSelect = document.getElementById('categoria_id');
        previewElements.categoria.textContent = categoriaSelect.options[categoriaSelect.selectedIndex]?.text || '-';

        const ubicacionSelect = document.getElementById('ubicacion_id');
        previewElements.ubicacion.textContent = ubicacionSelect.options[ubicacionSelect.selectedIndex]?.text || '-';

        // Actualizar stocks
        const stockInicial = parseInt(document.getElementById('stock_inicial').value) || 0;
        // For edit, stock_actual should primarily reflect the current value, but can be influenced by changes to stock_inicial
        // If you have total_entradas/salidas on the form, you'd calculate: stock_actual = stock_inicial + total_entradas - total_salidas
        // For simplicity here, assuming stock_actual is directly from the product, but if stock_inicial changes, the actual stock might also change
        // In a real scenario, stock_actual usually comes from movements, not directly editable as 'inicial'.
        // For this form, we'll keep it simple and just show the provided stock_actual or recalculate if logic allows.
        previewElements.stockInicial.textContent = stockInicial.toLocaleString();
        previewElements.stockActual.textContent = (stockInicial + {{ $producto->total_entradas }} - {{ $producto->total_salidas }}).toLocaleString(); // Example recalculation
        
        const stockMinimo = parseInt(document.getElementById('stock_minimo').value);
        const stockMaximo = parseInt(document.getElementById('stock_maximo').value);

        previewElements.stockMinimo.textContent = !isNaN(stockMinimo) ? stockMinimo.toLocaleString() : '-';
        previewElements.stockMaximo.textContent = !isNaN(stockMaximo) ? stockMaximo.toLocaleString() : '-';

        // Actualizar costo y valor total
        const costo = parseFloat(document.getElementById('costo').value) || 0;
        previewElements.costo.textContent = '$' + costo.toFixed(2);

        // Use the displayed current stock for value calculation in preview, not just initial
        const currentStockForValue = parseInt(previewElements.stockActual.textContent.replace(/,/g, '')) || 0;
        const valorTotal = currentStockForValue * costo;
        previewElements.valorTotal.textContent = '$' + valorTotal.toFixed(2);

        // Actualizar estado
        const status = document.getElementById('status').value;
        previewElements.status.textContent = status;
        previewElements.status.className = 'badge bg-' + getStatusColor(status);
    }

    // Función para obtener el color del estado (debe ser consistente con Producto.php)
    function getStatusColor(status) {
        const colors = {
            'Activo': 'success',
            'Inactivo': 'secondary',
            'Obsoleto': 'dark',
            'Stock Optimo': 'success',
            'Stock Bajo': 'warning',
            'Fuera de Stock': 'danger',
            'Sobre Stock': 'info',
            'En Reorden': 'primary'
        };
        return colors[status] || 'primary'; // Default color
    }

    // Validaciones de stock (misma lógica que en create)
    function validateStocks() {
        const stockMinimoInput = document.getElementById('stock_minimo');
        const stockMaximoInput = document.getElementById('stock_maximo');
        const stockSeguridadInput = document.getElementById('stock_seguridad');

        const stockMinimo = parseInt(stockMinimoInput.value);
        const stockMaximo = parseInt(stockMaximoInput.value);
        const stockSeguridad = parseInt(stockSeguridadInput.value);

        stockMinimoInput.setCustomValidity(''); // Clear previous validation
        stockMaximoInput.setCustomValidity('');
        stockSeguridadInput.setCustomValidity('');

        // Validar que stock mínimo no sea mayor al máximo
        if (!isNaN(stockMinimo) && !isNaN(stockMaximo) && stockMinimo > stockMaximo) {
            stockMinimoInput.setCustomValidity('El stock mínimo no puede ser mayor al stock máximo');
        }

        // Validar que stock de seguridad no sea mayor al máximo
        if (!isNaN(stockSeguridad) && !isNaN(stockMaximo) && stockSeguridad > stockMaximo) {
            stockSeguridadInput.setCustomValidity('El stock de seguridad no puede ser mayor al stock máximo');
        }

        // Apply Bootstrap validation styles
        stockMinimoInput.reportValidity();
        stockMaximoInput.reportValidity();
        stockSeguridadInput.reportValidity();
    }

    // Event listeners para actualizar vista previa
    const fieldsToWatch = [
        'codigo', 'nombre', 'especificacion', 'categoria_id', 'marca_id', 'proveedor_id',
        'ubicacion_id', 'unidad', 'nivel',
        'stock_inicial', 'stock_minimo', 'stock_maximo', 'stock_seguridad',
        'duracion_inventario', 'vida_util',
        'costo', 'status', 'observaciones'
    ];

    fieldsToWatch.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updatePreview);
            field.addEventListener('change', updatePreview);
        }
    });

    // Event listeners para validaciones
    ['stock_minimo', 'stock_maximo', 'stock_seguridad'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', validateStocks);
            field.addEventListener('input', validateStocks);
        }
    });

    // Inicializar vista previa con los datos existentes
    updatePreview();

    // Validación del formulario antes del envío
    form.addEventListener('submit', function(e) {
        validateStocks(); // Re-validate before submit

        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }

        form.classList.add('was-validated');
    });

    // Botón de vista previa
    document.getElementById('previewBtn')?.addEventListener('click', function() {
        alert('Funcionalidad de vista previa - por implementar');
    });
});
</script>
@endpush