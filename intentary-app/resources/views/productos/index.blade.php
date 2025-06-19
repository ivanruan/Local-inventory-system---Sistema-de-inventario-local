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
    // Inicializar ProductManagerCore
    const productManagerCore = window.productManager;

    // Registrar módulos. Asegúrate de que los constructores de tus módulos
    // puedan aceptar el core o tener un método setCore si lo necesitan.
    // También asegúrate de que tus módulos tienen un método initialize()
    // si tienen lógica que debe ejecutarse al ser registrados.
    productManagerCore.registerModule('eventHandlers', new ProductEventHandlers(productManagerCore));
    productManagerCore.registerModule('tableFeatures', new ProductTableFeatures(productManagerCore));
    productManagerCore.registerModule('stockManagement', new ProductStockManagement(productManagerCore));
    productManagerCore.registerModule('bulkOperations', new ProductBulkOperations(productManagerCore));
    productManagerCore.registerModule('formHelpers', new FormHelpers(productManagerCore));


    // Función que inicializa las características adicionales después de que productManager y sus módulos estén listos
    function initProductManagerFeatures() {
        console.log('initProductManagerFeatures called. ProductManager is ready.');

        // Vinculación de eventos que no son manejados por los módulos registrados automáticamente
        // Ej: Toggle del panel de filtros (esto estaba en tu index.blade.php original)
        document.getElementById('toggleFiltersBtn')?.addEventListener('click', () => {
            const panel = document.getElementById('filtrosPanel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        });

        // La lógica de vinculación de paginación y otros eventos delegados
        // debería estar en ProductEventHandlers o ProductTableFeatures.
        // Asegúrate de que esos módulos tienen métodos que se llaman
        // después de que el DOM de la tabla se actualiza.
        // ProductTableFeatures.initializeTableFeatures() ya se llama en renderProductData
        // de ProductManagerCore, lo cual es correcto para re-bindear.
        // Asegúrate que ProductEventHandlers también tenga un método para re-bindear
        // eventos de navegación/paginación si los elementos de paginación son reemplazados.
        // Si no tienes lógica de navegación de paginación en ProductEventHandlers,
        // podrías agregarla aquí, o en product-event-handlers.js en un método como bindNavigationEvents().
        // La paginación de Laravel ya usa links, y al ser inyectados, los eventos de click
        // pueden ser capturados por un listener delegado si se implementa.
        // Por ahora, asumimos que ProductEventHandlers.bindNavigationEvents() ya lo maneja
        // y se llama en su initialize(). Si los enlaces de paginación son reemplazados,
        // este método debería llamarse de nuevo después de la actualización AJAX.

        // Listener global para clics de paginación para manejarlo vía AJAX
        // Esto es un ejemplo de cómo podrías delegar eventos para la paginación.
        // Este listener debe estar fuera de `initProductManagerFeatures` o ser robusto
        // para no duplicarse.
        document.addEventListener('click', (e) => {
            const paginationLink = e.target.closest('.pagination a');
            if (paginationLink && paginationLink.href) {
                e.preventDefault(); // Prevenir la recarga de la página
                const url = new URL(paginationLink.href);
                // Obtener solo los parámetros de la URL del enlace de paginación
                const params = Object.fromEntries(new URLSearchParams(url.search).entries());

                // Fusionar con los filtros actuales para mantenerlos al cambiar de página
                const currentFilters = window.productManager.state.filters;
                window.productManager.updateURL({ ...currentFilters, ...params });
            }
        });


        // Sincronizar el formulario de filtros con la URL al cargar la página
        // Esto es útil si el usuario llega con una URL con filtros o usa el botón de atrás/adelante.
        productManagerCore.syncFilterFormWithURL();
    }

    // Esperar a que ProductManager esté completamente inicializado
    // `window.productManager` se define en product-manager-core.js.
    // La propiedad `isInitialized` de ProductManagerCore debería ser `true`
    // después de que todos los módulos se hayan registrado.
    if (window.productManager && window.productManager.isInitialized) {
        initProductManagerFeatures();
    } else {
        // Fallback: Si por alguna razón productManager no está listo,
        // usar el observador para esperar a que `isInitialized` sea true.
        const observer = new MutationObserver((mutationsList, observer) => {
            if (window.productManager && window.productManager.isInitialized) {
                observer.disconnect();
                initProductManagerFeatures();
            }
        });
        // Observa cambios en el body (donde se añaden elementos o scripts pueden cargarse)
        observer.observe(document.body, { childList: true, subtree: true });
    }
});

// Listener para el evento popstate (botones atrás/adelante del navegador)
// Esto debe estar fuera de DOMContentLoaded si se espera que los listeners
// de nivel superior persistan a través de actualizaciones AJAX.
window.addEventListener('popstate', (event) => {
    console.log('Popstate event triggered:', event.state);
    if (window.productManager) {
        // El estado del historial guardado es nulo si el usuario navegó a una página externa
        // o si es la entrada inicial. Si hay un estado, úsalo.
        const newState = event.state || {}; // Si event.state es null, usa un objeto vacío para limpiar filtros
        window.productManager.updateURL(newState, false); // false para reemplazar el estado, no crear uno nuevo
    }
});
</script>
@endpush