// Product Manager Core - Funcionalidad Principal y Gestión de Estado Mejorado
class ProductManagerCore {
    constructor() {
        this.modules = new Map();
        this.state = {
            selectedProducts: new Set(),
            filters: {}, // Aquí guardaremos los filtros activos
            sortConfig: null,
            lastAction: null,
            isLoading: false,
            bulkOperationInProgress: false
        };

        this.config = {
            debounceDelay: 500,
            requestTimeout: 30000,
            maxRetries: 3,
            toastDuration: 3000,
            animationDuration: 300,
            bulk: {
                confirmationRequired: true,
                autoRefresh: true,
                showProgress: true,
                maxSelectionWarning: 1000
            },
            // Nueva configuración: selectores para las partes de la página a actualizar
            selectors: {
                productTableContainer: '#product-table-container', // Contenedor de la tabla (ej: div que envuelve _product_table)
                productStatsContainer: '#product-stats-container', // Contenedor de estadísticas (ej: div que envuelve _product_stats)
                paginationContainer: '.pagination-container', // Contenedor de la paginación
                mainContentArea: '#main-content-area' // Contenedor principal para recargar, si aplica
            }
        };

        this.initializeCore();
    }

    // === INICIALIZACIÓN ===
    initializeCore() {
        this.ensureCSRFToken();
        this.setupGlobalErrorHandling();
        this.initializeNotificationSystem();
        this.setupBulkOperationHandlers();
    }

    ensureCSRFToken() {
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = document.querySelector('input[name="_token"]')?.value || '';
            document.head.appendChild(meta);
        }
    }

    setupGlobalErrorHandling() {
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled promise rejection:', event.reason);
            this.handleError(event.reason, 'Unhandled Promise');
        });

        window.addEventListener('error', (event) => {
            console.error('Global error:', event.error);
            this.handleError(event.error, 'Global Error');
        });
    }

    // === NUEVO: CONFIGURACIÓN ESPECÍFICA PARA BULK OPERATIONS ===
    setupBulkOperationHandlers() {
        // Escuchar eventos de bulk operations
        document.addEventListener('bulkOperationStarted', (event) => {
            this.updateState('bulkOperationInProgress', true);
            this.updateState('lastAction', event.detail);
        });

        document.addEventListener('bulkOperationCompleted', (event) => {
            this.updateState('bulkOperationInProgress', false);
            if (event.detail.success && this.config.bulk.autoRefresh) {
                setTimeout(() => this.refreshComponents(), 1500);
            }
        });

        // Atajos de teclado globales para bulk operations
        document.addEventListener('keydown', (event) => {
            if (event.ctrlKey || event.metaKey) {
                if (this.getSelectedIds().length === 0) return;
                
                switch(event.key.toLowerCase()) {
                    case 'u':
                        event.preventDefault();
                        this.bulkUpdateStatus();
                        break;
                    case 'e':
                        event.preventDefault();
                        this.bulkExport();
                        break;
                    case 'delete':
                        event.preventDefault();
                        this.bulkDelete();
                        break;
                    case 'd':
                        if (event.shiftKey) {
                            event.preventDefault();
                            this.bulkDuplicate();
                        }
                        break;
                }
            }
        });
    }

    // === GESTIÓN DE MÓDULOS MEJORADA ===
    /**
     * Registra un módulo y lo inicializa.
     * @param {string} name
     * @param {object} moduleInstance
     */
    registerModule(name, moduleInstance) {
        if (this.modules.has(name)) {
            console.warn(`Module "${name}" already registered.`);
            return;
        }
        this.modules.set(name, moduleInstance);
        // Pasa una referencia al core si el módulo la necesita para interacción
        if (typeof moduleInstance.setCore === 'function') {
            moduleInstance.setCore(this);
        }
        // Si el módulo tiene un método initialize, llamarlo
        if (typeof moduleInstance.initialize === 'function') {
            moduleInstance.initialize();
        }
        console.debug(`Module registered: ${name}`);
    }

    getModule(name) {
        return this.modules.get(name);
    }

    hasModule(name) {
        return this.modules.has(name);
    }

    initializeModules() {
        this.modules.forEach((module, name) => {
            if (typeof module.initialize === 'function') {
                try {
                    module.initialize();
                    console.debug(`Module initialized: ${name}`);
                } catch (error) {
                    console.error(`Failed to initialize module ${name}:`, error);
                    this.handleError(error, `Module initialization: ${name}`);
                }
            }
        });
        return this;
    }

    isReady() {
        return this.modules.size > 0;
    }

    // === MÉTODOS DELEGADOS MEJORADOS PARA BULK OPERATIONS ===

    async bulkExport() {
        const bulkOperations = this.getModule('bulkOperations');
        if (!bulkOperations) {
            this.showNotification('error', 'Módulo de operaciones masivas no disponible');
            return;
        }

        if (!this.validateBulkOperation('export')) return;

        try {
            this.dispatchBulkEvent('bulkOperationStarted', { 
                type: 'export', 
                count: this.getSelectedIds().length 
            });
            
            await bulkOperations.bulkExport();
            
            this.dispatchBulkEvent('bulkOperationCompleted', { 
                success: true, 
                type: 'export' 
            });
        } catch (error) {
            this.handleError(error, 'BulkOperations.bulkExport');
            this.dispatchBulkEvent('bulkOperationCompleted', { 
                success: false, 
                error: error.message 
            });
        }
    }

    async bulkDelete() {
        const bulkOperations = this.getModule('bulkOperations');
        if (!bulkOperations) {
            this.showNotification('error', 'Módulo de operaciones masivas no disponible');
            return;
        }

        if (!this.validateBulkOperation('delete')) return;

        try {
            this.dispatchBulkEvent('bulkOperationStarted', { 
                type: 'delete', 
                count: this.getSelectedIds().length 
            });
            
            await bulkOperations.bulkDelete();
        } catch (error) {
            this.handleError(error, 'BulkOperations.bulkDelete');
            this.dispatchBulkEvent('bulkOperationCompleted', { 
                success: false, 
                error: error.message 
            });
        }
    }

    async bulkExportWithOptions() {
        const bulkOperations = this.getModule('bulkOperations');
        if (!bulkOperations) {
            this.showNotification('error', 'Módulo de operaciones masivas no disponible');
            return;
        }

        if (!this.validateBulkOperation('export')) return;

        try {
            await bulkOperations.bulkExportWithOptions();
        } catch (error) {
            this.handleError(error, 'BulkOperations.bulkExportWithOptions');
        }
    }

    // === NUEVOS MÉTODOS DE VALIDACIÓN PARA BULK OPERATIONS ===
    validateBulkOperation(operation) {
        const selectedIds = this.getSelectedIds();
        
        if (!selectedIds || selectedIds.length === 0) {
            this.showNotification('warning', `Selecciona al menos un producto para ${operation}`);
            return false;
        }

        // Advertencia para operaciones masivas muy grandes
        if (selectedIds.length > this.config.bulk.maxSelectionWarning) {
            const proceed = confirm(
                `Has seleccionado ${selectedIds.length} productos. ` +
                `Esta operación puede tardar varios minutos. ¿Continuar?`
            );
            if (!proceed) return false;
        }

        // Verificar si hay otra operación bulk en progreso
        if (this.state.bulkOperationInProgress) {
            this.showNotification('warning', 'Ya hay una operación masiva en progreso');
            return false;
        }

        return true;
    }

    dispatchBulkEvent(eventName, detail) {
        const event = new CustomEvent(eventName, { detail });
        document.dispatchEvent(event);
    }

    // === GESTIÓN DE SELECCIÓN MEJORADA ===
    clearSelection() {
        const tableFeatures = this.getModule('tableFeatures');
        if (tableFeatures && typeof tableFeatures.clearSelection === 'function') {
            tableFeatures.clearSelection();
            this.updateState('selectedProducts', new Set());
            return true;
        } else {
            console.error('TableFeatures module not found or method not available');
            return false;
        }
    }

    getSelectedIds() {
        const tableFeatures = this.getModule('tableFeatures');
        if (tableFeatures && typeof tableFeatures.getSelectedIds === 'function') {
            return tableFeatures.getSelectedIds();
        } else {
            // Fallback al estado interno
            return Array.from(this.state.selectedProducts);
        }
    }

    getSelectedCount() {
        return this.getSelectedIds().length;
    }

    selectAll() {
        const tableFeatures = this.getModule('tableFeatures');
        if (tableFeatures && typeof tableFeatures.selectAll === 'function') {
            return tableFeatures.selectAll();
        }
    }

    updateSelectedProducts(selectedIds) {
        const newSet = new Set(selectedIds);
        this.updateState('selectedProducts', newSet);
        return this;
    }

    // === GESTIÓN DE ESTADO MEJORADA ===
    setState(newState) {
        const oldState = { ...this.state };
        this.state = { ...this.state, ...newState };
        // Disparar un evento global de cambio de estado
        document.dispatchEvent(new CustomEvent('productManagerStateChange', {
            detail: { oldState, newState: this.state }
        }));
        console.debug('Core state updated:', this.state);
    }


    
    updateState(key, value) {
        const oldValue = this.state[key];
        this.state[key] = value;
        
        if (oldValue !== value) {
            this.notifyStateChange(key, value, oldValue);
        }
        
        return this;
    }

    getState(key) {
        return key ? this.state[key] : { ...this.state };
    }

    notifyStateChange(key, newValue, oldValue) {
        const event = new CustomEvent('productManagerStateChange', {
            detail: { 
                key, 
                newValue, 
                oldValue, 
                state: { ...this.state },
                timestamp: Date.now()
            }
        });
        
        document.dispatchEvent(event);
        
        // Notificar módulos específicos
        this.modules.forEach((module, name) => {
            if (typeof module.onStateChange === 'function') {
                try {
                    module.onStateChange(key, newValue, oldValue);
                } catch (error) {
                    console.error(`State change notification failed for module ${name}:`, error);
                }
            }
        });
    }

    // === CONFIGURACIÓN MEJORADA ===
    setConfig(newConfig) {
        this.config = this.deepMerge(this.config, newConfig);
        this.notifyConfigChange();
        return this;
    }

    getConfig(key) {
        if (!key) return { ...this.config };
        
        // Soporte para notación de puntos: 'bulk.confirmationRequired'
        const keys = key.split('.');
        let value = this.config;
        
        for (const k of keys) {
            value = value?.[k];
            if (value === undefined) break;
        }
        
        return value;
    }

    notifyConfigChange() {
        this.modules.forEach((module) => {
            if (typeof module.onConfigChange === 'function') {
                try {
                    module.onConfigChange(this.config);
                } catch (error) {
                    console.error(`Config change notification failed for module:`, error);
                }
            }
        });
    }

    // === UTILIDAD PARA MERGE PROFUNDO ===
    deepMerge(target, source) {
        const result = { ...target };
        
        for (const key in source) {
            if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                result[key] = this.deepMerge(target[key] || {}, source[key]);
            } else {
                result[key] = source[key];
            }
        }
        
        return result;
    }

    // === UTILIDADES DE TOKEN Y SEGURIDAD ===
    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    // === UTILIDADES TEMPORALES ===
    debounce(func, wait = null) {
        const delay = wait || this.config.debounceDelay;
        let timeout;
        
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    throttle(func, limit = 100) {
        let inThrottle;
        return (...args) => {
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // === GESTIÓN DE CARGA MEJORADA ===
    showSpinner() {
        let spinner = document.getElementById('product-loading-spinner');
        if (!spinner) {
            spinner = document.createElement('div');
            spinner.id = 'product-loading-spinner';
            spinner.className = 'spinner-overlay'; // Clase CSS para overlay y centrado
            spinner.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            `;
            document.body.appendChild(spinner);
        }
        spinner.style.display = 'flex'; // Usar flex para centrado
    }

    hideSpinner() {
        const spinner = document.getElementById('product-loading-spinner');
        if (spinner) {
            spinner.style.display = 'none';
        }
    }

    // === GESTIÓN DE URL ===
    
    /**
     * Obtiene los parámetros de la URL actual.
     * @returns {URLSearchParams}
     */
    getCurrentURLParams() {
        return new URLSearchParams(window.location.search);
    }
    
    
    /**
     * Actualiza la URL y recarga los datos de los productos vía AJAX.
     * Esto es el corazón del filtrado dinámico.
     * @param {Object} newFilters Un objeto con los nuevos filtros a aplicar.
     * Ej: { search: 'texto', categoria: '1' }
     * @param {boolean} pushState Indica si se debe agregar una nueva entrada al historial del navegador (true)
     * o reemplazar la actual (false). Por defecto true.
     */
    async updateURL(newFilters, pushState = true) {
        if (this.state.isLoading) {
            console.warn('Ya hay una petición en curso. Ignorando updateURL.');
            return;
        }

        this.setState({ isLoading: true });
        this.showSpinner(); // Mostrar un spinner mientras se carga

        const currentParams = this.getCurrentURLParams();

        // Aplicar los nuevos filtros a los parámetros existentes
        // Los filtros vacíos (null, undefined, '') deben eliminarse de la URL
        for (const key in newFilters) {
            if (newFilters.hasOwnProperty(key)) {
                if (newFilters[key] !== null && newFilters[key] !== undefined && newFilters[key] !== '') {
                    currentParams.set(key, newFilters[key]);
                } else {
                    currentParams.delete(key);
                }
            }
        }

        // Actualizar el estado de los filtros en el core
        this.setState({ filters: Object.fromEntries(currentParams.entries()) });

        const newPath = window.location.pathname + (currentParams.toString() ? `?${currentParams.toString()}` : '');

        // 1. Actualizar la URL en la barra de direcciones sin recargar la página
        if (pushState) {
            window.history.pushState(this.state.filters, '', newPath);
        } else {
            window.history.replaceState(this.state.filters, '', newPath);
        }
        
        console.debug('URL actualizada a:', newPath);

        // 2. Realizar la petición AJAX para obtener los nuevos productos
        try {
            const response = await fetch(newPath, { // Usamos newPath para la petición AJAX
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Indica que es una petición AJAX
                    'Accept': 'application/json', // Esperamos una respuesta JSON
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Tu token CSRF
                }
            });

            if (!response.ok) {
                // Si la respuesta no es 2xx, lanza un error
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }

            const data = await response.json(); // Esperamos un JSON con los parciales HTML

            // 3. Renderizar los datos recibidos en las secciones correspondientes del DOM
            this.renderProductData(data);

            this.showToast('Productos actualizados.', 'success');

        } catch (error) {
            console.error('Error al cargar productos vía AJAX:', error);
            this.showToast('Error al cargar productos.', 'danger');
            // Opcional: recargar la página si falla el AJAX para evitar estado inconsistente
            // window.location.reload();
        } finally {
            this.setState({ isLoading: false });
            this.hideSpinner(); // Ocultar el spinner
        }
    }

    getURLParams() {
        const params = new URLSearchParams(window.location.search);
        const result = {};
        
        for (const [key, value] of params) {
            result[key] = value;
        }
        
        return result;
    }

    /**
     * Renderiza el HTML recibido en las secciones correspondientes de la página.
     * @param {Object} data Objeto JSON con el HTML de los parciales.
     * Ej: { product_table: '...', product_stats: '...', pagination: '...' }
     */
    renderProductData(data) {
        const { selectors } = this.config;

        if (data.product_table && document.querySelector(selectors.productTableContainer)) {
            document.querySelector(selectors.productTableContainer).innerHTML = data.product_table;
            console.debug('Product table updated.');
            // Re-bindear eventos de la tabla si es necesario (ej: selección de filas, botones de acción)
            if (this.modules.has('tableFeatures')) {
                this.modules.get('tableFeatures').initializeTableFeatures(); // Re-inicializar características
                this.modules.get('tableFeatures').updateBulkActions(); // Actualizar barra de acciones masivas
            }
        } else {
            console.warn('Product table container or data not found.');
        }

        if (data.product_stats && document.querySelector(selectors.productStatsContainer)) {
            document.querySelector(selectors.productStatsContainer).innerHTML = data.product_stats;
            console.debug('Product stats updated.');
        } else {
            console.warn('Product stats container or data not found.');
        }

        // Si tienes paginación, también podrías actualizarla
        if (data.pagination && document.querySelector(selectors.paginationContainer)) {
            document.querySelector(selectors.paginationContainer).innerHTML = data.pagination;
            console.debug('Pagination updated.');
            // Re-bindear eventos de paginación si están en un módulo separado
            if (this.modules.has('eventHandlers')) {
                this.modules.get('eventHandlers').bindNavigationEvents(); // Re-bindear eventos de paginación
            }
        } else {
            console.warn('Pagination container or data not found.');
        }

        // Asegurarse de que el panel de filtros se mantenga sincronizado si la URL cambia
        this.syncFilterFormWithURL();
    }

    /**
     * Sincroniza los valores del formulario de filtros con los parámetros actuales de la URL.
     * Útil después de una carga AJAX o si el usuario navega con las flechas del navegador.
     */
    syncFilterFormWithURL() {
        const filtersForm = document.getElementById('filtersForm');
        if (!filtersForm) return;

        const urlParams = this.getCurrentURLParams();

        filtersForm.querySelectorAll('input[name], select[name]').forEach(element => {
            const paramName = element.name;
            const paramValue = urlParams.get(paramName);

            if (element.tagName === 'INPUT' && element.type === 'text') {
                element.value = paramValue || '';
            } else if (element.tagName === 'SELECT') {
                element.value = paramValue || ''; // Selecciona la opción que coincida
                // Si la opción no existe, la selección será la primera o vacía
            }
        });
        console.debug('Filter form synced with URL parameters.');
    }

    // === CLIENTE API MEJORADO ===
    async makeRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'X-CSRF-TOKEN': this.getCSRFToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            timeout: this.config.requestTimeout
        };

        const requestOptions = { ...defaultOptions, ...options };
        
        // Merge headers correctamente
        if (options.headers) {
            requestOptions.headers = { ...defaultOptions.headers, ...options.headers };
        }

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), requestOptions.timeout);
            
            const response = await fetch(url, {
                ...requestOptions,
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP ${response.status}: ${response.statusText}${errorText ? ` - ${errorText}` : ''}`);
            }
            
            const contentType = response.headers.get('content-type');
            
            if (contentType?.includes('application/json')) {
                return await response.json();
            } else if (contentType?.includes('text/')) {
                return { success: true, data: await response.text() };
            } else {
                return { success: true, data: response };
            }
            
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('Request timeout');
            }
            
            console.error('API Request failed:', error);
            throw error;
        }
    }

    async makeRequestWithRetry(url, options = {}, maxRetries = null) {
        const retries = maxRetries || this.config.maxRetries;
        let lastError;
        
        for (let attempt = 1; attempt <= retries; attempt++) {
            try {
                return await this.makeRequest(url, options);
            } catch (error) {
                lastError = error;
                
                if (attempt === retries) break;
                
                // No reintentar en errores 4xx (cliente)
                if (error.message.includes('HTTP 4')) {
                    throw error;
                }
                
                // Exponential backoff
                const delay = Math.min(1000 * Math.pow(2, attempt - 1), 5000);
                await new Promise(resolve => setTimeout(resolve, delay));
                
                console.warn(`Request attempt ${attempt} failed, retrying in ${delay}ms...`);
                
                // Mostrar progreso en reintentos para operaciones bulk
                if (this.state.bulkOperationInProgress) {
                    this.showSpinner(`Reintentando... (${attempt}/${retries})`);
                }
            }
        }
        
        throw lastError;
    }

    // === SISTEMA DE NOTIFICACIONES MEJORADO ===
    initializeNotificationSystem() {
        if (!document.getElementById('productManagerToastContainer')) {
            const container = document.createElement('div');
            container.id = 'productManagerToastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1080';
            document.body.appendChild(container);
        }
    }

    showNotification(type, message, duration = null) {
        const toastDuration = duration || this.config.toastDuration;
        const toastId = `productManagerToast_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        
        const typeConfig = {
            success: { class: 'text-bg-success', icon: 'bi-check-circle' },
            warning: { class: 'text-bg-warning', icon: 'bi-exclamation-triangle' },
            error: { class: 'text-bg-danger', icon: 'bi-x-circle' },
            info: { class: 'text-bg-info', icon: 'bi-info-circle' }
        };
        
        const config = typeConfig[type] || typeConfig.info;
        
        const toastHtml = `
            <div id="${toastId}" class="toast ${config.class}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bi ${config.icon} me-2"></i>
                    <strong class="me-auto">Sistema</strong>
                    <small class="text-muted">${new Date().toLocaleTimeString()}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;

        const container = document.getElementById('productManagerToastContainer');
        container.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            delay: toastDuration,
            autohide: toastDuration > 0
        });
        
        toast.show();

        // Cleanup automático
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });

        return toast;
    }



    // === MANEJO DE ERRORES MEJORADO ===
    handleError(error, context = 'Unknown') {
        console.error(`Error in ${context}:`, error);
        
        let message = 'Ha ocurrido un error inesperado';
        let type = 'error';
        
        if (error instanceof TypeError && error.message.includes('fetch')) {
            message = 'Error de conexión. Verifica tu conexión a internet.';
        } else if (error.message) {
            message = error.message;
        }
        
        // Ocultar spinner si hay error
        if (this.state.isLoading) {
            this.hideSpinner();
        }
        
        // Log error para debugging
        this.logError(error, context);
        
        // Mostrar notificación
        this.showNotification(type, message, 5000);
        
        // Notificar módulos del error
        this.notifyModulesOfError(error, context);
    }

    logError(error, context) {
        const errorInfo = {
            message: error.message,
            stack: error.stack,
            context,
            timestamp: new Date().toISOString(),
            url: window.location.href,
            userAgent: navigator.userAgent,
            state: { ...this.state },
            modules: Array.from(this.modules.keys())
        };
        
        // Aquí podrías enviar el error a un servicio de logging
        console.error('Detailed error log:', errorInfo);
        
        // En desarrollo, podrías enviar a un endpoint de logging
        if (window.location.hostname === 'localhost') {
            // this.makeRequest('/api/log-error', { method: 'POST', body: JSON.stringify(errorInfo) })
            //     .catch(e => console.warn('Failed to log error to server:', e));
        }
    }

    notifyModulesOfError(error, context) {
        this.modules.forEach((module, name) => {
            if (typeof module.onError === 'function') {
                try {
                    module.onError(error, context);
                } catch (moduleError) {
                    console.error(`Error notification failed for module ${name}:`, moduleError);
                }
            }
        });
    }

    // === UTILIDADES DE VALIDACIÓN MEJORADAS ===
    validateInput(input, rules = {}) {
        let isValid = true;
        const errors = [];
        const value = input.value.trim();

        // Limpiar estado anterior
        input.classList.remove('is-valid', 'is-invalid');
        
        // Validación requerido
        if (rules.required && !value) {
            isValid = false;
            errors.push('Este campo es requerido');
        }

        // Solo validar el resto si hay valor o es requerido
        if (value || rules.required) {
            // Validación longitud
            if (rules.minLength && value.length < rules.minLength) {
                isValid = false;
                errors.push(`Mínimo ${rules.minLength} caracteres`);
            }

            if (rules.maxLength && value.length > rules.maxLength) {
                isValid = false;
                errors.push(`Máximo ${rules.maxLength} caracteres`);
            }

            // Validación numérica
            if (rules.numeric && value && isNaN(Number(value))) {
                isValid = false;
                errors.push('Debe ser un número válido');
            }

            // Validación rango numérico
            if (rules.min !== undefined && Number(value) < rules.min) {
                isValid = false;
                errors.push(`Valor mínimo: ${rules.min}`);
            }

            if (rules.max !== undefined && Number(value) > rules.max) {
                isValid = false;
                errors.push(`Valor máximo: ${rules.max}`);
            }

            // Validación email
            if (rules.email && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                isValid = false;
                errors.push('Email no válido');
            }

            // Validación personalizada
            if (rules.custom && typeof rules.custom === 'function') {
                const customResult = rules.custom(value);
                if (customResult !== true) {
                    isValid = false;
                    errors.push(customResult || 'Valor no válido');
                }
            }
        }

        // Aplicar clases de validación
        input.classList.add(isValid ? 'is-valid' : 'is-invalid');

        // Mostrar errores si existen
        if (!isValid && errors.length > 0) {
            this.showFieldErrors(input, errors);
        } else {
            this.clearFieldErrors(input);
        }

        return { isValid, errors };
    }

    showFieldErrors(input, errors) {
        this.clearFieldErrors(input);
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = errors[0]; // Mostrar solo el primer error
        
        input.parentNode.appendChild(feedback);
    }

    clearFieldErrors(input) {
        const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
    }

    // === UTILIDADES DE FORMATO ===
    formatNumber(num) {
        return parseInt(num).toLocaleString('es-MX');
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(amount);
    }

    formatDate(date, options = {}) {
        const defaultOptions = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        
        return new Intl.DateTimeFormat('es-MX', { ...defaultOptions, ...options })
            .format(new Date(date));
    }

    // === MÉTODOS DE LIMPIEZA Y DESTRUCCIÓN ===
    refreshComponents() {
        // Notificar a todos los módulos para que se refresquen
        this.modules.forEach((module, name) => {
            if (typeof module.refresh === 'function') {
                try {
                    module.refresh();
                    console.debug(`Module refreshed: ${name}`);
                } catch (error) {
                    console.error(`Failed to refresh module ${name}:`, error);
                }
            }
        });
    }

    destroy() {
        // Destruir módulos
        this.modules.forEach((module, name) => {
            if (typeof module.destroy === 'function') {
                try {
                    module.destroy();
                    console.debug(`Module destroyed: ${name}`);
                } catch (error) {
                    console.error(`Failed to destroy module ${name}:`, error);
                }
            }
        });

        // Limpiar elementos del DOM
        this.hideSpinner();
        document.getElementById('productManagerToastContainer')?.remove();

        // Limpiar estado
        this.modules.clear();
        this.state = {};
        
        console.debug('ProductManagerCore destroyed');
    }

    // === MÉTODOS DE INFORMACIÓN Y DEBUG ===
    getInfo() {
        return {
            version: '2.0.0',
            modules: Array.from(this.modules.keys()),
            state: { ...this.state },
            config: { ...this.config },
            isReady: this.modules.size > 0
        };
    }

    debugMode(enabled = true) {
        if (enabled) {
            window.productManagerDebug = this;
            console.log('Debug mode enabled. Access via window.productManagerDebug');
            console.log('Available modules:', Array.from(this.modules.keys()));
        } else {
            delete window.productManagerDebug;
        }
    }
}

// Export para sistemas de módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductManagerCore;
}

// Global instance for browser
window.productManager = new ProductManagerCore();