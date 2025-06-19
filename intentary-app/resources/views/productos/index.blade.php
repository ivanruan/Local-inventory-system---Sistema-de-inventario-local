@extends('layouts.app')

@section('title', 'Listado de Productos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
<style>
    /* CSS para el Spinner Overlay */
    .spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7); /* Fondo semi-transparente */
        display: flex; /* Para centrar el spinner */
        justify-content: center;
        align-items: center;
        z-index: 1050; /* Asegúrate de que esté por encima de otros elementos */
        display: none; /* Oculto por defecto */
    }

    /* Opcional: Estilos para el spinner de Bootstrap */
    .spinner-border {
        width: 3rem;
        height: 3rem;
        /* Puedes añadir colores, etc. */
    }
</style>
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

    {{-- Contenedor para las Estadísticas Rápidas (será actualizado por AJAX) --}}
    <div id="product-stats-container">
        @include('productos.partials._product_stats', [
            'productos' => $productos,
            'stockBajo' => $stockBajo,
            'fueraStock' => $fueraStock,
            'valorTotal' => $valorTotal,
            'sobreStock' => $sobreStock
        ])
    </div>

    {{-- Contenedor para la tabla de productos (será actualizado por AJAX) --}}
    <div id="product-table-container">
        @include('productos.partials._product_table', ['productos' => $productos])

        {{-- Contenedor para la paginación (si está separada de la tabla) --}}
        {{-- Si $productos->links() se renderiza dentro de _product_table.blade.php,
             asegúrate de que _product_table tenga un contenedor con la clase
             'pagination-container' para que product-manager-core.js lo encuentre.
             De lo contrario, puedes incluir un div aquí como este:
        --}}
        <div class="pagination-container mt-3">
            {{-- La paginación se inyectará aquí via AJAX --}}
            {{ $productos->links() }}
        </div>
    </div>

    {{-- Modales (fuera del contenedor principal para un correcto posicionamiento) --}}
    {{-- No necesitan contenedores específicos para AJAX si no se actualizan dinámicamente --}}
    @include('productos.modals._movimientos_modal')
    @include('productos.modals._quick_stock_modal')

@endsection

@push('scripts')
{{-- Primero el core, luego los módulos --}}
<script src="{{ asset('js/product-management/core/product-manager-core.js') }}"></script>
<script src="{{ asset('js/product-management/events/product-event-handlers.js') }}"></script>
<script src="{{ asset('js/product-management/features/product-table-features.js') }}"></script>
<script src="{{ asset('js/product-management/features/product-stock-management.js') }}"></script>
<script src="{{ asset('js/product-management/features/product-bulk-operations.js') }}"></script>
<script src="{{ asset('js/product-management/ui/product-form-helpers.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script> {{-- Tu archivo main.js si contiene lógica adicional --}}

<script>
document.addEventListener('DOMContentLoaded', () => {
    // SOLUCIÓN INMEDIATA: Vincular el evento del botón de filtros directamente
    const toggleFiltersBtn = document.getElementById('toggleFiltersBtn');
    const filtrosPanel = document.getElementById('filtrosPanel');
    
    if (toggleFiltersBtn && filtrosPanel) {
        toggleFiltersBtn.addEventListener('click', () => {
            console.log('Toggle filters button clicked'); // Para debugging
            if (filtrosPanel.style.display === 'none' || filtrosPanel.style.display === '') {
                filtrosPanel.style.display = 'block';
            } else {
                filtrosPanel.style.display = 'none';
            }
        });
    } else {
        console.error('Toggle button or filters panel not found');
    }

    // RESTO DE LA LÓGICA DEL PRODUCT MANAGER
    // Inicializar ProductManagerCore
    const productManagerCore = window.productManager;

    if (productManagerCore) {
        // Registrar módulos solo si productManagerCore existe
        try {
            productManagerCore.registerModule('eventHandlers', new ProductEventHandlers(productManagerCore));
            productManagerCore.registerModule('tableFeatures', new ProductTableFeatures(productManagerCore));
            productManagerCore.registerModule('stockManagement', new ProductStockManagement(productManagerCore));
            productManagerCore.registerModule('bulkOperations', new ProductBulkOperations(productManagerCore));
            productManagerCore.registerModule('formHelpers', new FormHelpers(productManagerCore));
        } catch (error) {
            console.error('Error registering modules:', error);
        }
    } else {
        console.warn('ProductManager not available');
    }

    // Función que inicializa las características adicionales
    function initProductManagerFeatures() {
        console.log('initProductManagerFeatures called. ProductManager is ready.');

        // Listener global para clics de paginación
        document.addEventListener('click', (e) => {
            const paginationLink = e.target.closest('.pagination a');
            if (paginationLink && paginationLink.href) {
                e.preventDefault();
                const url = new URL(paginationLink.href);
                const params = Object.fromEntries(new URLSearchParams(url.search).entries());
                
                if (window.productManager && window.productManager.state) {
                    const currentFilters = window.productManager.state.filters;
                    window.productManager.updateURL({ ...currentFilters, ...params });
                }
            }
        });

        // Sincronizar filtros con URL
        if (productManagerCore && productManagerCore.syncFilterFormWithURL) {
            productManagerCore.syncFilterFormWithURL();
        }
    }

    // Inicializar features si ProductManager está listo
    if (window.productManager && window.productManager.isInitialized) {
        initProductManagerFeatures();
    } else if (window.productManager) {
        // Esperar a que se inicialice
        setTimeout(() => {
            if (window.productManager && window.productManager.isInitialized) {
                initProductManagerFeatures();
            }
        }, 100);
    }
});

// Listener para el evento popstate
window.addEventListener('popstate', (event) => {
    console.log('Popstate event triggered:', event.state);
    if (window.productManager && window.productManager.updateURL) {
        const newState = event.state || {};
        window.productManager.updateURL(newState, false);
    }
});
</script>
@endpush