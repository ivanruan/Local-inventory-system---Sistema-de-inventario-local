// main.js - Integración completa de ProductManager con todos los módulos
document.addEventListener('DOMContentLoaded', () => {
    
         // Verificar que FormHelpers esté disponible
    if (typeof FormHelpers === 'undefined') {
        console.error('FormHelpers no está definido!');
        return;
    }
    
    // Instanciar el núcleo
    const core = new ProductManagerCore();

    // === Módulo de eventos globales ===
    const eventHandlers = new ProductEventHandlers(core);
    core.registerModule('eventHandlers', eventHandlers);

    // === Módulo de operaciones masivas ===
    const bulkOperations = new ProductBulkOperations(core);
    core.registerModule('bulkOperations', bulkOperations);

    // === Módulo de características de tabla ===
    const tableFeatures = new ProductTableFeatures(core);
    core.registerModule('tableFeatures', tableFeatures);
    tableFeatures.initializeTableFeatures(); // Inicializa efectos y controles en la tabla

    // === Módulo de gestión de stock ===
    const stockManagement = new ProductStockManagement(core);
    core.registerModule('stockManagement', stockManagement);

    // === Módulo de Form Helpers ===
    // Pasa el core para que FormHelpers pueda usar sus utilidades (e.g., makeRequest, showToast)
    const formHelpers = new FormHelpers(core);
    core.registerModule('formHelpers', formHelpers);
    formHelpers.initializeEventListeners(); // Asegúrate de inicializar los listeners

    // Opcional: Sobreescribir la instancia global si quieres que apunte a la del core
    // Esto es útil si otras partes de tu código ya están usando window.formHelpers
    // y quieres que esa instancia tenga acceso al core.
    window.formHelpers = formHelpers;

    // Modo depuración (opcional)
    // core.debugMode(true);

    console.log('Product Manager completamente inicializado');
});