{{-- resources/views/productos/partials/_page_header.blade.php --}}
<div class="d-flex flex-column mb-3">
    {{-- Título arriba --}}
    <h1 class="h3 mb-3">Listado de Productos</h1>

    {{-- Fila: barra de acciones y botones --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        
        {{-- Barra de acciones masivas --}}
        <div id="bulkActionBar" 
            class="bg-white border rounded-3 px-3 py-2 d-flex align-items-center justify-content-between flex-grow-1"
            style="display: none;">

            {{-- Texto a la izquierda con etiqueta --}}
            <div class="text-muted small">
                <strong>ACCIONES MASIVAS:</strong> 
                <span id="selectedCount">0</span> productos seleccionados
            </div>

            {{-- Botones alineados a la derecha --}}
            <div class="d-flex gap-3 ms-auto">
                <button class="btn btn-outline-success btn-sm" data-action="bulk-export">
                    <i class="bi bi-download"></i> Exportar
                </button>
                <button class="btn btn-outline-danger btn-sm" data-action="bulk-delete">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="d-flex gap-2">
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Producto
            </a>
            <button class="btn btn-outline-secondary" id="toggleFiltersBtn">
                <i class="bi bi-funnel"></i> Busqueda y Filtros
            </button>
        </div>
    </div>
</div>