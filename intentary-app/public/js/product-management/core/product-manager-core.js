// Product Manager Core - Funcionalidad Principal y Gestión de Estado Mejorado
class ProductManagerCore {
    constructor() {
        this.modules = new Map();
        this.state = {
            selectedProducts: new Set(),
            filters: {},
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
            // Configuraciones específicas para bulk operations
            bulk: {
                confirmationRequired: true,
                autoRefresh: true,
                showProgress: true,
                maxSelectionWarning: 1000
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
    registerModule(name, moduleInstance) {
        // Verificar que el módulo tenga la estructura correcta
        if (!moduleInstance || typeof moduleInstance.initialize !== 'function') {
            console.warn(`Module ${name} doesn't have required initialize method`);
        }

        // Inyectar referencia del core en el módulo
        if (moduleInstance && typeof moduleInstance === 'object') {
            moduleInstance.core = this;
        }

        this.modules.set(name, moduleInstance);
        console.debug(`Module registered: ${name}`);
        
        // Auto-inicializar si el core ya está listo
        if (this.isReady() && typeof moduleInstance.initialize === 'function') {
            try {
                moduleInstance.initialize();
                console.debug(`Module auto-initialized: ${name}`);
            } catch (error) {
                console.error(`Failed to auto-initialize module ${name}:`, error);
            }
        }

        return this;
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
    async bulkUpdateStatus() {
        const bulkOperations = this.getModule('bulkOperations');
        if (!bulkOperations) {
            this.showNotification('error', 'Módulo de operaciones masivas no disponible');
            return;
        }

        if (!this.validateBulkOperation('update')) return;

        try {
            this.dispatchBulkEvent('bulkOperationStarted', { 
                type: 'statusUpdate', 
                count: this.getSelectedIds().length 
            });
            
            await bulkOperations.bulkUpdateStatus();
        } catch (error) {
            this.handleError(error, 'BulkOperations.bulkUpdateStatus');
            this.dispatchBulkEvent('bulkOperationCompleted', { 
                success: false, 
                error: error.message 
            });
        }
    }

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

    async bulkDuplicate() {
        const bulkOperations = this.getModule('bulkOperations');
        if (!bulkOperations) {
            this.showNotification('error', 'Módulo de operaciones masivas no disponible');
            return;
        }

        if (!this.validateBulkOperation('duplicate')) return;

        try {
            this.dispatchBulkEvent('bulkOperationStarted', { 
                type: 'duplicate', 
                count: this.getSelectedIds().length 
            });
            
            await bulkOperations.bulkDuplicate();
        } catch (error) {
            this.handleError(error, 'BulkOperations.bulkDuplicate');
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
    showSpinner(message = 'Cargando...', persistent = false) {
        if (document.getElementById('productManagerSpinner')) {
            // Actualizar mensaje si ya existe
            const existingMessage = document.querySelector('#productManagerSpinner small');
            if (existingMessage) {
                existingMessage.textContent = message;
            }
            return;
        }
        
        this.updateState('isLoading', true);
        
        const spinner = document.createElement('div');
        spinner.id = 'productManagerSpinner';
        spinner.className = 'position-fixed top-50 start-50 translate-middle d-flex flex-column align-items-center';
        spinner.style.cssText = 'z-index: 9999; background: rgba(255,255,255,0.95); padding: 2rem; border-radius: 0.75rem; box-shadow: 0 8px 25px rgba(0,0,0,0.15); backdrop-filter: blur(10px);';
        
        const closeBtn = persistent ? '' : `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" 
                    onclick="document.getElementById('productManagerSpinner').remove(); 
                             productManager.updateState('isLoading', false);"
                    title="Cancelar operación"></button>
        `;
        
        spinner.innerHTML = `
            ${closeBtn}
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <div class="text-center">
                <div class="fw-semibold mb-1">Procesando</div>
                <small class="text-muted">${message}</small>
            </div>
        `;
        
        document.body.appendChild(spinner);
    }

    hideSpinner() {
        const spinner = document.getElementById('productManagerSpinner');
        if (spinner) {
            spinner.style.opacity = '0';
            spinner.style.transform = 'translate(-50%, -50%) scale(0.9)';
            spinner.style.transition = 'all 0.2s ease-out';
            
            setTimeout(() => {
                spinner.remove();
                this.updateState('isLoading', false);
            }, 200);
        }
    }

    // === GESTIÓN DE URL ===
    updateURL(params, reload = true) {
        const url = new URL(window.location.href);
        
        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });
        
        // Resetear paginación al filtrar
        if (Object.keys(params).some(key => ['search', 'categoria', 'marca', 'status', 'stock_filter'].includes(key))) {
            url.searchParams.delete('page');
        }
        
        if (reload) {
            window.location.href = url.toString();
        } else {
            window.history.pushState({}, '', url.toString());
            
            // Notificar cambio de URL a los módulos
            this.modules.forEach((module) => {
                if (typeof module.onURLChange === 'function') {
                    module.onURLChange(this.getURLParams());
                }
            });
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