// Product Event Handlers - Gestión de Eventos Globales y Punto de Entrada
class ProductEventHandlers {
    constructor(core) {
        this.core = core;
        this.isInitialized = false;
        this.eventListeners = new Map();
        this.activityTimer = null;
        this.lastActivity = Date.now();
        
        this.initialize();
    }

    // === INICIALIZACIÓN ===
    initialize() {
        if (this.isInitialized) return;
        
        this.bindGlobalEvents();
        this.bindSearchEvents();
        this.bindFilterEvents();
        this.bindFormEvents();
        this.bindNavigationEvents();
        this.setupActivityTracking();
        this.setupPageUnloadHandlers();
        
        this.isInitialized = true;
        console.debug('ProductEventHandlers initialized');
    }

    // === EVENTOS GLOBALES ===
    bindGlobalEvents() {
        // Evento de cambio de estado del core
        this.addEventListener(document, 'productManagerStateChange', (e) => {
            this.handleStateChange(e.detail);
        });

        // Eventos de teclado globales
        this.addEventListener(document, 'keydown', (e) => {
            this.handleGlobalKeydown(e);
        });

        // Eventos de visibilidad de página
        this.addEventListener(document, 'visibilitychange', () => {
            this.handleVisibilityChange();
        });

        // Eventos de resize
        this.addEventListener(window, 'resize', this.core.throttle(() => {
            this.handleWindowResize();
        }, 250));
    }

    // === EVENTOS DE BÚSQUEDA ===
    bindSearchEvents() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            // Búsqueda con debounce
            this.addEventListener(searchInput, 'input', this.core.debounce((e) => {
                this.handleSearchInput(e);
            }, this.core.getConfig('debounceDelay')));

            // Limpiar búsqueda con Escape
            this.addEventListener(searchInput, 'keydown', (e) => {
                if (e.key === 'Escape') {
                    this.clearSearch();
                }
            });

            // Indicador visual de búsqueda activa
            this.addEventListener(searchInput, 'focus', () => {
                searchInput.parentElement.classList.add('search-active');
            });

            this.addEventListener(searchInput, 'blur', () => {
                searchInput.parentElement.classList.remove('search-active');
            });
        }
    }

    // === EVENTOS DE FILTROS ===
    bindFilterEvents() {
        const filterSelects = document.querySelectorAll(
            'select[name="categoria"], select[name="marca"], select[name="status"], select[name="stock_filter"]'
        );

        filterSelects.forEach(select => {
            this.addEventListener(select, 'change', (e) => {
                this.handleFilterChange(e);
            });

            // Indicador visual para filtros activos
            this.addEventListener(select, 'change', () => {
                this.updateFilterIndicators();
            });
        });

        // Botón de limpiar filtros
        const clearFiltersBtn = document.querySelector('[data-action="clear-filters"]');
        if (clearFiltersBtn) {
            this.addEventListener(clearFiltersBtn, 'click', () => {
                this.clearAllFilters();
            });
        }
    }

    // === EVENTOS DE FORMULARIOS ===
    bindFormEvents() {
        // Validación en tiempo real
        const formInputs = document.querySelectorAll('input[data-validate], select[data-validate], textarea[data-validate]');
        formInputs.forEach(input => {
            this.addEventListener(input, 'blur', () => {
                this.validateField(input);
            });

            this.addEventListener(input, 'input', this.core.debounce(() => {
                if (input.classList.contains('is-invalid')) {
                    this.validateField(input);
                }
            }, 300));
        });

        // Auto-guardado en formularios largos
        const autoSaveForms = document.querySelectorAll('form[data-auto-save]');
        autoSaveForms.forEach(form => {
            this.addEventListener(form, 'input', this.core.debounce(() => {
                this.autoSaveForm(form);
            }, 2000));
        });

        // Prevenir envío accidental de formularios
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            this.addEventListener(form, 'submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    // === EVENTOS DE NAVEGACIÓN ===
    bindNavigationEvents() {
        // Navegación con teclado en tablas
        const tableRows = document.querySelectorAll('tbody tr[data-id]');
        tableRows.forEach((row, index) => {
            this.addEventListener(row, 'keydown', (e) => {
                this.handleTableNavigation(e, row, index);
            });
        });

        // Links de paginación
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            this.addEventListener(link, 'click', (e) => {
                if (this.core.getState('isLoading')) {
                    e.preventDefault();
                    return false;
                }
                this.core.showSpinner('Cargando página...');
            });
        });

        // Botones de acción con confirmación
        const dangerousActions = document.querySelectorAll('[data-confirm]');
        dangerousActions.forEach(button => {
            this.addEventListener(button, 'click', (e) => {
                if (!confirm(button.dataset.confirm)) {
                    e.preventDefault();
                }
            });
        });
    }

    // === MANEJADORES DE EVENTOS ===
    handleStateChange(detail) {
        const { key, newValue, oldValue } = detail;
        
        switch (key) {
            case 'selectedProducts':
                this.updateBulkActionsVisibility();
                break;
            case 'filters':
                this.updateFilterIndicators();
                break;
            case 'isLoading':
                this.updateLoadingState(newValue);
                break;
        }
        
        console.debug(`State changed: ${key}`, { newValue, oldValue });
    }

    handleGlobalKeydown(e) {
        // Ctrl/Cmd + A para seleccionar todo
        if ((e.ctrlKey || e.metaKey) && e.key === 'a' && this.isInProductTable()) {
            e.preventDefault();
            this.selectAllProducts();
        }

        // Escape para limpiar selecciones
        if (e.key === 'Escape') {
            this.clearSelections();
        }

        // Ctrl/Cmd + F para enfocar búsqueda
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        }
    }

    handleVisibilityChange() {
        if (document.hidden) {
            this.pauseActivityTracking();
        } else {
            this.resumeActivityTracking();
            this.refreshDataIfStale();
        }
    }

    handleWindowResize() {
        // Notificar a módulos que manejan UI responsive
        this.core.modules.forEach((module) => {
            if (typeof module.onWindowResize === 'function') {
                module.onWindowResize();
            }
        });
    }

    handleSearchInput(e) {
        const term = e.target.value.trim();
        const minLength = 3;
        
        if (term.length >= minLength || term.length === 0) {
            this.core.updateURL({ search: term || null });
        }
        
        // Actualizar contador de caracteres si existe
        this.updateSearchCounter(term, minLength);
    }

    handleFilterChange(e) {
        const filterName = e.target.name;
        const filterValue = e.target.value;
        
        this.core.updateURL({ [filterName]: filterValue || null });
        
        // Guardar preferencias de filtro
        this.saveFilterPreferences(filterName, filterValue);
    }

    handleTableNavigation(e, row, index) {
        const rows = document.querySelectorAll('tbody tr[data-id]');
        
        switch (e.key) {
            case 'ArrowUp':
                e.preventDefault();
                if (index > 0) {
                    rows[index - 1].focus();
                }
                break;
            case 'ArrowDown':
                e.preventDefault();
                if (index < rows.length - 1) {
                    rows[index + 1].focus();
                }
                break;
            case 'Enter':
            case ' ':
                e.preventDefault();
                this.toggleRowSelection(row);
                break;
        }
    }

    // === UTILIDADES DE MANEJO DE EVENTOS ===
    addEventListener(element, event, handler, options = {}) {
        const wrappedHandler = (e) => {
            this.trackActivity();
            try {
                handler(e);
            } catch (error) {
                this.core.handleError(error, `Event: ${event}`);
            }
        };

        element.addEventListener(event, wrappedHandler, options);
        
        // Guardar referencia para limpieza posterior
        const key = `${element.tagName}-${event}-${Date.now()}`;
        this.eventListeners.set(key, {
            element,
            event,
            handler: wrappedHandler,
            options
        });
    }

    // === SEGUIMIENTO DE ACTIVIDAD ===
    setupActivityTracking() {
        this.activityTimer = setInterval(() => {
            const inactiveTime = Date.now() - this.lastActivity;
            const maxInactiveTime = 5 * 60 * 1000; // 5 minutos
            
            if (inactiveTime > maxInactiveTime) {
                this.handleInactivity();
            }
        }, 60000); // Revisar cada minuto
    }

    trackActivity() {
        this.lastActivity = Date.now();
    }

    pauseActivityTracking() {
        if (this.activityTimer) {
            clearInterval(this.activityTimer);
            this.activityTimer = null;
        }
    }

    resumeActivityTracking() {
        if (!this.activityTimer) {
            this.setupActivityTracking();
        }
        this.trackActivity();
    }

    handleInactivity() {
        console.debug('User inactive, considering data refresh');
        // Aquí podrías implementar lógica para refrescar datos automáticamente
    }

    // === FUNCIONES DE UTILIDAD ===
    updateSearchCounter(term, minLength) {
        const counter = document.querySelector('.search-counter');
        if (counter) {
            const remaining = Math.max(0, minLength - term.length);
            counter.textContent = remaining > 0 ? 
                `${remaining} caracteres más` : 
                `${term.length} caracteres`;
        }
    }

    updateBulkActionsVisibility() {
        const selectedCount = this.core.getState('selectedProducts')?.size || 0;
        const bulkActions = document.querySelector('.bulk-actions');
        
        if (bulkActions) {
            bulkActions.style.display = selectedCount > 0 ? 'block' : 'none';
            
            const countElement = bulkActions.querySelector('.selected-count');
            if (countElement) {
                countElement.textContent = selectedCount;
            }
        }
    }

    updateFilterIndicators() {
        const activeFilters = this.getActiveFilters();
        const indicator = document.querySelector('.active-filters-indicator');
        
        if (indicator) {
            indicator.style.display = activeFilters.length > 0 ? 'inline' : 'none';
            indicator.textContent = activeFilters.length;
        }
    }

    updateLoadingState(isLoading) {
        const buttons = document.querySelectorAll('button[type="submit"], .btn-primary');
        buttons.forEach(btn => {
            btn.disabled = isLoading;
        });
        
        const form = document.querySelector('form');
        if (form) {
            form.style.opacity = isLoading ? '0.7' : '1';
        }
    }

    // === ACCIONES DE UI ===
    clearSearch() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.value = '';
            this.core.updateURL({ search: null });
        }
    }

    clearAllFilters() {
        const filterSelects = document.querySelectorAll(
            'select[name="categoria"], select[name="marca"], select[name="status"], select[name="stock_filter"]'
        );
        
        const clearParams = {};
        filterSelects.forEach(select => {
            select.value = '';
            clearParams[select.name] = null;
        });
        
        this.core.updateURL(clearParams);
    }

    clearSelections() {
        this.core.updateState('selectedProducts', new Set());
        
        const checkboxes = document.querySelectorAll('input[type="checkbox"][data-product-id]');
        checkboxes.forEach(cb => cb.checked = false);
    }

    selectAllProducts() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][data-product-id]');
        const selectedIds = new Set();
        
        checkboxes.forEach(cb => {
            cb.checked = true;
            selectedIds.add(cb.dataset.productId);
        });
        
        this.core.updateState('selectedProducts', selectedIds);
    }

    toggleRowSelection(row) {
        const checkbox = row.querySelector('input[type="checkbox"][data-product-id]');
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change'));
        }
    }

    // === VALIDACIÓN Y FORMULARIOS ===
    validateField(input) {
        const rules = this.parseValidationRules(input);
        return this.core.validateInput(input, rules);
    }

    validateForm(form) {
        const inputs = form.querySelectorAll('input[data-validate], select[data-validate], textarea[data-validate]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!this.validateField(input).isValid) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    parseValidationRules(input) {
        const rules = {};
        const validateAttr = input.dataset.validate;
        
        if (validateAttr) {
            validateAttr.split('|').forEach(rule => {
                const [name, value] = rule.split(':');
                rules[name] = value ? (isNaN(value) ? value : Number(value)) : true;
            });
        }
        
        return rules;
    }

    autoSaveForm(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Guardar en localStorage como backup
        localStorage.setItem(`form_backup_${form.id}`, JSON.stringify({
            data,
            timestamp: Date.now()
        }));
        
        console.debug('Form auto-saved', form.id);
    }

    // === UTILIDADES ===
    getActiveFilters() {
        const filters = [];
        const filterSelects = document.querySelectorAll(
            'select[name="categoria"], select[name="marca"], select[name="status"], select[name="stock_filter"]'
        );
        
        filterSelects.forEach(select => {
            if (select.value) {
                filters.push({
                    name: select.name,
                    value: select.value,
                    text: select.options[select.selectedIndex].text
                });
            }
        });
        
        return filters;
    }

    saveFilterPreferences(filterName, filterValue) {
        const preferences = JSON.parse(localStorage.getItem('product_filter_preferences') || '{}');
        preferences[filterName] = filterValue;
        localStorage.setItem('product_filter_preferences', JSON.stringify(preferences));
    }

    isInProductTable() {
        return document.querySelector('.product-table') !== null;
    }

    refreshDataIfStale() {
        const lastRefresh = localStorage.getItem('last_data_refresh');
        const staleTime = 10 * 60 * 1000; // 10 minutos
        
        if (!lastRefresh || Date.now() - parseInt(lastRefresh) > staleTime) {
            this.core.refreshComponents();
            localStorage.setItem('last_data_refresh', Date.now().toString());
        }
    }

    // === MANEJADORES DE DESCARGA DE PÁGINA ===
    setupPageUnloadHandlers() {
        // Limpiar antes de salir
        this.addEventListener(window, 'beforeunload', () => {
            this.cleanup();
        });

        // Advertir sobre cambios no guardados
        this.addEventListener(window, 'beforeunload', (e) => {
            if (this.hasUnsavedChanges()) {
                e.preventDefault();
                e.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
                return e.returnValue;
            }
        });
    }

    hasUnsavedChanges() {
        const forms = document.querySelectorAll('form[data-track-changes]');
        return Array.from(forms).some(form => {
            return form.dataset.hasChanges === 'true';
        });
    }

    // === LIMPIEZA ===
    cleanup() {
        // Limpiar event listeners
        this.eventListeners.forEach(({ element, event, handler, options }) => {
            try {
                element.removeEventListener(event, handler, options);
            } catch (error) {
                console.warn('Error removing event listener:', error);
            }
        });
        
        this.eventListeners.clear();
        
        // Limpiar timers
        if (this.activityTimer) {
            clearInterval(this.activityTimer);
            this.activityTimer = null;
        }
        
        console.debug('ProductEventHandlers cleaned up');
    }

    destroy() {
        this.cleanup();
        this.isInitialized = false;
        this.core = null;
    }

    // === INTERFACE PARA EL CORE ===
    onStateChange(key, newValue, oldValue) {
        // Implementación del interface requerido por el core
        this.handleStateChange({ key, newValue, oldValue });
    }

    onError(error, context) {
        console.error(`Event handler error in ${context}:`, error);
    }

    refresh() {
        if (!this.isInitialized) {
            this.initialize();
        }
    }
}

// Export para sistemas de módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductEventHandlers;
}