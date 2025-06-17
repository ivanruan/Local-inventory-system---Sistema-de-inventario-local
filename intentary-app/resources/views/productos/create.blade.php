@extends('layouts.app')

@section('title', 'Crear Producto')

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
            <h1 class="h3 mb-1">Crear Nuevo Producto</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
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

    <form action="{{ route('productos.store') }}" method="POST" id="productoForm" novalidate>
        @csrf
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
                                   value="{{ old('codigo') }}" 
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
                                   value="{{ old('nombre') }}" 
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
                                      placeholder="Descripción detallada, características técnicas, etc.">{{ old('especificacion') }}</textarea>
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
                                        {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
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
                                        {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
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
                                        {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
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
                                        {{ old('ubicacion_id') == $ubicacion->id ? 'selected' : '' }}>
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
                                <option value="PZA" {{ old('unidad') == 'PZA' ? 'selected' : '' }}>Pieza (PZA)</option>
                                <option value="KG" {{ old('unidad') == 'KG' ? 'selected' : '' }}>Kilogramo (KG)</option>
                                <option value="LT" {{ old('unidad') == 'LT' ? 'selected' : '' }}>Litro (LT)</option>
                                <option value="MT" {{ old('unidad') == 'MT' ? 'selected' : '' }}>Metro (MT)</option>
                                <option value="M2" {{ old('unidad') == 'M2' ? 'selected' : '' }}>Metro Cuadrado (M2)</option>
                                <option value="M3" {{ old('unidad') == 'M3' ? 'selected' : '' }}>Metro Cúbico (M3)</option>
                                <option value="CAJA" {{ old('unidad') == 'CAJA' ? 'selected' : '' }}>Caja</option>
                                <option value="PAQ" {{ old('unidad') == 'PAQ' ? 'selected' : '' }}>Paquete (PAQ)</option>
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
                                   value="{{ old('nivel', 0) }}" 
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
                                   value="{{ old('stock_inicial', 0) }}" 
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
                                   value="{{ old('stock_minimo') }}" 
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
                                   value="{{ old('stock_maximo') }}" 
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
                                   value="{{ old('stock_seguridad') }}" 
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
                                       value="{{ old('duracion_inventario') }}" 
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
                                       value="{{ old('vida_util') }}" 
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
                                       value="{{ old('costo', '0.00') }}" 
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
                                @foreach(\App\Models\Producto::getStatusOptions() as $status)
                                    <option value="{{ $status }}" 
                                        {{ old('status', 'Activo') == $status ? 'selected' : '' }}>
                                        {{ $status }}
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
                                $0.00
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
                                      placeholder="Información adicional, notas de mantenimiento, instrucciones especiales, etc.">{{ old('observaciones') }}</textarea>
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
                        <strong>Código:</strong> <span id="preview-codigo" class="text-muted">-</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Nombre:</strong> <span id="preview-nombre" class="text-muted">-</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Categoría:</strong> <span id="preview-categoria" class="text-muted">-</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Ubicación:</strong> <span id="preview-ubicacion" class="text-muted">-</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Unidad:</strong> <span id="preview-unidad" class="text-muted">-</span>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-2">
                        <strong>Stock Inicial:</strong> <span id="preview-stock-inicial" class="badge bg-info">0</span>
                    </div>
                    
                    <div class="mb-2">
                        <strong>Stock Mínimo:</strong> <span id="preview-stock-minimo" class="badge bg-warning">-</span>
                    </div>
                    
                    <div class="mb-2">
                        <strong>Stock Máximo:</strong> <span id="preview-stock-maximo" class="badge bg-success">-</span>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-2">
                        <strong>Costo Unitario:</strong> <span id="preview-costo" class="text-success fw-bold">$0.00</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Estado:</strong> <span id="preview-status" class="badge bg-success">Activo</span>
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
                        <button type="submit" class="btn btn-primary" name="action" value="save">
                            <i class="bi bi-check-lg"></i> Guardar Producto
                        </button>
                        <button type="submit" class="btn btn-success" name="action" value="save_and_continue">
                            <i class="bi bi-plus-circle"></i> Guardar y Crear Otro
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
        const stockMinimo = parseInt(document.getElementById('stock_minimo').value);
        const stockMaximo = parseInt(document.getElementById('stock_maximo').value);
        
        previewElements.stockInicial.textContent = stockInicial.toLocaleString();
        previewElements.stockMinimo.textContent = stockMinimo >= 0 ? stockMinimo.toLocaleString() : '-'; // Handle null/undefined
        previewElements.stockMaximo.textContent = stockMaximo >= 0 ? stockMaximo.toLocaleString() : '-'; // Handle null/undefined
        
        // Actualizar costo y valor total
        const costo = parseFloat(document.getElementById('costo').value) || 0;
        previewElements.costo.textContent = '$' + costo.toFixed(2);
        
        const valorTotal = stockInicial * costo;
        previewElements.valorTotal.textContent = '$' + valorTotal.toFixed(2);
        
        // Actualizar estado
        const status = document.getElementById('status').value;
        previewElements.status.textContent = status;
        previewElements.status.className = 'badge bg-' + getStatusColor(status);
    }

    // Función para obtener el color del estado
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
        return colors[status] || 'primary';
    }

    // Validaciones de stock
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
        'codigo', 'nombre', 'categoria_id', 'ubicacion_id', 'unidad',
        'stock_inicial', 'stock_minimo', 'stock_maximo', 'costo', 'status'
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
            field.addEventListener('input', validateStocks); // Also validate on input for real-time feedback
        }
    });

    // Inicializar vista previa
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
        // This is a placeholder. For a real preview, you'd likely open a modal
        // or navigate to a temporary preview route with the form data.
        alert('Funcionalidad de vista previa - por implementar');
    });

    // Auto-generar código si está vacío
    document.getElementById('nombre')?.addEventListener('blur', function() {
        const codigoField = document.getElementById('codigo');
        if (!codigoField.value && this.value) {
            // Generate a code based on the product name
            const cleanedName = this.value.toUpperCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // Remove accents
                .replace(/[^A-Z0-9\s]/g, '') // Remove special characters except spaces
                .replace(/\s+/g, '-'); // Replace spaces with hyphens
            
            let generatedCode = 'PROD-' + cleanedName;

            // Limit code length to maximum 50 characters (including 'PROD-')
            if (generatedCode.length > 50) {
                generatedCode = generatedCode.substring(0, 50);
            }

            codigoField.value = generatedCode;
            updatePreview();
        }
    });
});
</script>
@endpush