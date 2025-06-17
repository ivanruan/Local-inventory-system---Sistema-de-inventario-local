@extends('layouts.app')

@section('title', 'Listado de Productos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
@endpush

@section('content')
    {{-- Encabezado Principal de la Página (Título, Barra de Acciones Masivas, Botones) --}}
    @include('productos.partials._page_header')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Panel de Filtros --}}
    @include('productos.partials._filters_panel', [
        'categorias' => $categorias, 
        'marcas' => $marcas
    ])

    {{-- Estadísticas Rápidas --}}
    @include('productos.partials._product_stats', [
        'productos' => $productos, 
        'stockBajo' => $stockBajo, 
        'fueraStock' => $fueraStock, 
        'valorTotal' => $valorTotal, 
        'sobreStock' => $sobreStock
    ])

    {{-- Incluir el parcial de la tabla --}}
    @include('productos.partials._product_table', ['productos' => $productos])

    {{-- Modales (fuera del contenedor principal para un correcto posicionamiento) --}}
    @include('productos.modals._movimientos_modal')
    @include('productos.modals._quick_stock_modal')

@endsection

@push('scripts')
<script src="{{ asset('js/product-management/core/product-manager-core.js') }}"></script>
<script src="{{ asset('js/product-management/events/product-event-handlers.js') }}"></script>
<script src="{{ asset('js/product-management/features/product-table-features.js') }}"></script>
<script src="{{ asset('js/product-management/features/product-stock-management.js') }}"></script>
<script src="{{ asset('js/product-management/features/product-bulk-operations.js') }}"></script>
<script src="{{ asset('js/product-management/ui/product-form-helpers.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>

<script>
// Inicialización después de que ProductManager esté listo
function initProductManagerFeatures() {
    // Inicializar características de tabla si el módulo está disponible
    if (window.productManager?.modules?.tableFeatures) {
        window.productManager.modules.tableFeatures.initializeTableFeatures();
    }
    
    // Configurar eventos de UI
    document.getElementById('toggleFiltersBtn')?.addEventListener('click', () => {
        const panel = document.getElementById('filtrosPanel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    });
    
    // Delegación de eventos para movimientos
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.view-movements');
        if (btn) {
            const productId = btn.dataset.productId;
            if (productId && window.productManager?.modules?.stockManagement) {
                window.productManager.modules.stockManagement.verMovimientos(productId);
            }
        }
    });
    
    // Delegación de eventos para acciones masivas
    document.getElementById('bulkActionBar')?.addEventListener('click', (e) => {
        const actionBtn = e.target.closest('[data-action]');
        if (!actionBtn) return;
        
        const action = actionBtn.dataset.action;
        if (!window.productManager?.modules?.bulkOperations) return;
        
        switch(action) {
            case 'bulk-export':
                window.productManager.modules.bulkOperations.bulkExport();
                break;
            case 'bulk-delete':
                window.productManager.modules.bulkOperations.bulkDelete();
                break;
            case 'clear-selection':
                if (window.productManager?.modules?.tableFeatures) {
                    window.productManager.modules.tableFeatures.clearSelection();
                }
                break;
        }
    });
}

// Esperar a que ProductManager esté disponible
if (window.productManager) {
    initProductManagerFeatures();
} else {
    // Crear un observador para detectar cuando esté listo
    const observer = new MutationObserver(() => {
        if (window.productManager) {
            observer.disconnect();
            initProductManagerFeatures();
        }
    });
    
    observer.observe(document, {
        childList: true,
        subtree: true
    });
}
</script>
@endpush