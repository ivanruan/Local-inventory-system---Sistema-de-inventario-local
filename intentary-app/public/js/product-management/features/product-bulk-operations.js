// Product Bulk Operations - Operaciones masivas integradas con Core
class ProductBulkOperations {
    constructor(core) {
        this.core = core;
        this.name = 'bulkOperations';
        
        // Configuración específica del módulo
        this.config = {
            confirmationRequired: true,
            autoRefresh: true,
            showProgress: true
        };
        
        this.isInitialized = false;
    }

    // === INICIALIZACIÓN DEL MÓDULO ===
    initialize() {
        if (this.isInitialized) return;
        
        console.debug('Initializing BulkOperations module');
        this.setupEventListeners();
        this.isInitialized = true;
        
        return this;
    }

    setupEventListeners() {
        // Escuchar cambios de selección
        document.addEventListener('productManagerStateChange', (event) => {
            if (event.detail.key === 'selectedProducts') {
                this.onSelectionChange(event.detail.newValue);
            }
        });

        // Escuchar eventos de teclado para atajos
        document.addEventListener('keydown', (event) => {
            if (event.ctrlKey || event.metaKey) {
                switch(event.key) {
                    case 'u':
                        event.preventDefault();
                        this.bulkUpdateStatus();
                        break;
                    case 'e':
                        event.preventDefault();
                        this.bulkExport();
                        break;
                    case 'Delete':
                        event.preventDefault();
                        this.bulkDelete();
                        break;
                }
            }
        });
    }

    // === OPERACIONES MASIVAS PRINCIPALES ===
    async bulkUpdateStatus() {
        const selectedIds = this.getSelectedIds();
        
        if (!this.validateSelection(selectedIds, 'actualizar estado')) {
            return;
        }

        try {
            const modal = this.showStatusModal();
            modal.show();
        } catch (error) {
            this.core.handleError(error, 'BulkOperations.bulkUpdateStatus');
        }
    }

    async bulkExport() {
        const selectedIds = this.getSelectedIds();
        
        if (!this.validateSelection(selectedIds, 'exportar')) {
            return;
        }

        try {
            this.core.showSpinner('Preparando exportación...');
            
            const url = new URL('/productos/export', window.location.origin);
            url.searchParams.set('ids', selectedIds.join(','));
            
            // Agregar filtros actuales si existen
            const currentFilters = this.core.getURLParams();
            Object.entries(currentFilters).forEach(([key, value]) => {
                if (['search', 'categoria', 'marca', 'status'].includes(key)) {
                    url.searchParams.set(key, value);
                }
            });
            
            window.open(url.toString(), '_blank');
            
            this.core.showNotification('success', `Exportando ${selectedIds.length} productos`);
            
        } catch (error) {
            this.core.handleError(error, 'BulkOperations.bulkExport');
        } finally {
            this.core.hideSpinner();
        }
    }

    async bulkDelete() {
        const selectedIds = this.getSelectedIds();
        
        if (!this.validateSelection(selectedIds, 'eliminar')) {
            return;
        }

        if (!this.confirmAction(`¿Estás seguro de eliminar ${selectedIds.length} productos seleccionados? Esta acción no se puede deshacer.`)) {
            return;
        }

        try {
            this.core.showSpinner('Eliminando productos...');
            
            const result = await this.core.makeRequestWithRetry('/productos/bulk-delete', {
                method: 'POST',
                body: JSON.stringify({ ids: selectedIds })
            });

            if (result.success) {
                this.core.showNotification('success', `${selectedIds.length} productos eliminados correctamente`);
                this.handleSuccessfulOperation();
            } else {
                throw new Error(result.message || 'Error desconocido al eliminar productos');
            }
            
        } catch (error) {
            this.core.handleError(error, 'BulkOperations.bulkDelete');
        } finally {
            this.core.hideSpinner();
        }
    }

    async bulkDuplicate() {
        const selectedIds = this.getSelectedIds();
        
        if (!this.validateSelection(selectedIds, 'duplicar')) {
            return;
        }

        if (!this.confirmAction(`¿Duplicar ${selectedIds.length} productos seleccionados?`)) {
            return;
        }

        try {
            this.core.showSpinner('Duplicando productos...');
            
            const result = await this.core.makeRequestWithRetry('/productos/bulk-duplicate', {
                method: 'POST',
                body: JSON.stringify({ ids: selectedIds })
            });

            if (result.success) {
                this.core.showNotification('success', `${selectedIds.length} productos duplicados correctamente`);
                this.handleSuccessfulOperation();
            } else {
                throw new Error(result.message || 'Error desconocido al duplicar productos');
            }
            
        } catch (error) {
            this.core.handleError(error, 'BulkOperations.bulkDuplicate');
        } finally {
            this.core.hideSpinner();
        }
    }

    // === GESTIÓN DE MODALES ===
    showStatusModal() {
        const modalId = 'statusUpdateModal';
        this.removeExistingModal(modalId);
        
        const selectedIds = this.getSelectedIds();
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-pencil-square me-2"></i>
                                Actualizar Estado de ${selectedIds.length} Productos
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="bulkStatusForm" novalidate>
                                <div class="mb-3">
                                    <label class="form-label">Nuevo Estado <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status" required>
                                        <option value="">Seleccionar estado...</option>
                                        <optgroup label="Estados Generales">
                                            <option value="Activo">Activo</option>
                                            <option value="Inactivo">Inactivo</option>
                                            <option value="Obsoleto">Obsoleto</option>
                                        </optgroup>
                                        <optgroup label="Estados de Stock">
                                            <option value="Stock Optimo">Stock Óptimo</option>
                                            <option value="Stock Bajo">Stock Bajo</option>
                                            <option value="Fuera de Stock">Fuera de Stock</option>
                                            <option value="Sobre Stock">Sobre Stock</option>
                                            <option value="En Reorden">En Reorden</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Observaciones (opcional)</label>
                                    <textarea class="form-control" name="observaciones" rows="3" 
                                              placeholder="Motivo del cambio de estado..." maxlength="500"></textarea>
                                    <div class="form-text">Máximo 500 caracteres</div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Se actualizarán <strong>${selectedIds.length}</strong> productos con el nuevo estado.
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" id="submitBulkStatus">
                                <i class="bi bi-check-circle me-1"></i>Actualizar Productos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Configurar validación del formulario
        this.setupFormValidation('bulkStatusForm');
        
        // Configurar evento del botón
        document.getElementById('submitBulkStatus').addEventListener('click', () => {
            this.submitBulkStatusUpdate();
        });
        
        return new bootstrap.Modal(document.getElementById(modalId));
    }

    setupFormValidation(formId) {
        const form = document.getElementById(formId);
        const statusSelect = form.querySelector('select[name="status"]');
        const observacionesTextarea = form.querySelector('textarea[name="observaciones"]');

        // Validación en tiempo real
        statusSelect.addEventListener('change', () => {
            this.core.validateInput(statusSelect, { required: true });
        });

        if (observacionesTextarea) {
            observacionesTextarea.addEventListener('input', () => {
                this.core.validateInput(observacionesTextarea, { maxLength: 500 });
            });
        }
    }

    async submitBulkStatusUpdate() {
        const form = document.getElementById('bulkStatusForm');
        const formData = new FormData(form);
        const status = formData.get('status')?.trim();
        const observaciones = formData.get('observaciones')?.trim();

        // Validar formulario
        const statusValid = this.core.validateInput(form.querySelector('select[name="status"]'), { required: true });
        const observacionesValid = this.core.validateInput(form.querySelector('textarea[name="observaciones"]'), { maxLength: 500 });

        if (!statusValid.isValid || !observacionesValid.isValid) {
            this.core.showNotification('warning', 'Por favor corrige los errores en el formulario');
            return;
        }

        const selectedIds = this.getSelectedIds();
        
        try {
            this.core.showSpinner('Actualizando productos...');
            
            const result = await this.core.makeRequestWithRetry('/productos/bulk-update-status', {
                method: 'POST',
                body: JSON.stringify({
                    ids: selectedIds,
                    status: status,
                    observaciones: observaciones
                })
            });

            if (result.success) {
                this.core.showNotification('success', `${selectedIds.length} productos actualizados correctamente`);
                bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
                this.handleSuccessfulOperation();
            } else {
                throw new Error(result.message || 'Error desconocido al actualizar productos');
            }
            
        } catch (error) {
            this.core.handleError(error, 'BulkOperations.submitBulkStatusUpdate');
        } finally {
            this.core.hideSpinner();
        }
    }

    // === EXPORTACIÓN AVANZADA ===
    async bulkExportWithOptions() {
        const selectedIds = this.getSelectedIds();
        
        if (!this.validateSelection(selectedIds, 'exportar')) {
            return;
        }

        try {
            const modal = this.showExportOptionsModal();
            modal.show();
        } catch (error) {
            this.core.handleError(error, 'BulkOperations.bulkExportWithOptions');
        }
    }

    showExportOptionsModal() {
        const modalId = 'exportOptionsModal';
        this.removeExistingModal(modalId);
        
        const selectedIds = this.getSelectedIds();
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-download me-2"></i>
                                Opciones de Exportación
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="exportOptionsForm" novalidate>
                                <div class="mb-3">
                                    <label class="form-label">Formato de Exportación <span class="text-danger">*</span></label>
                                    <select class="form-select" name="format" required>
                                        <option value="excel">Excel (.xlsx)</option>
                                        <option value="csv">CSV (.csv)</option>
                                        <option value="pdf">PDF (.pdf)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Columnas a Incluir <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="codigo" checked>
                                                <label class="form-check-label">Código</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="nombre" checked>
                                                <label class="form-check-label">Nombre</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="categoria">
                                                <label class="form-check-label">Categoría</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="marca">
                                                <label class="form-check-label">Marca</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="precio" checked>
                                                <label class="form-check-label">Precio</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="stock" checked>
                                                <label class="form-check-label">Stock</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="status">
                                                <label class="form-check-label">Estado</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="created_at">
                                                <label class="form-check-label">Fecha Creación</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllColumns">
                                            Seleccionar Todo
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllColumns">
                                            Limpiar Todo
                                        </button>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Se exportarán <strong>${selectedIds.length}</strong> productos con las columnas seleccionadas.
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="processExport">
                                <i class="bi bi-download me-1"></i>Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Configurar eventos
        this.setupExportModalEvents();
        
        return new bootstrap.Modal(document.getElementById(modalId));
    }

    setupExportModalEvents() {
        // Botones de selección de columnas
        document.getElementById('selectAllColumns').addEventListener('click', () => {
            document.querySelectorAll('.column-checkbox').forEach(cb => cb.checked = true);
        });

        document.getElementById('clearAllColumns').addEventListener('click', () => {
            document.querySelectorAll('.column-checkbox').forEach(cb => cb.checked = false);
        });

        // Botón de exportación
        document.getElementById('processExport').addEventListener('click', () => {
            this.processExportWithOptions();
        });
    }

    async processExportWithOptions() {
        const form = document.getElementById('exportOptionsForm');
        const formData = new FormData(form);
        const format = formData.get('format');
        const columns = formData.getAll('columns[]');

        if (columns.length === 0) {
            this.core.showNotification('warning', 'Por favor selecciona al menos una columna para exportar');
            return;
        }

        try {
            this.core.showSpinner('Preparando exportación personalizada...');
            
            const url = new URL('/productos/export-advanced', window.location.origin);
            url.searchParams.set('ids', this.getSelectedIds().join(','));
            url.searchParams.set('format', format);
            url.searchParams.set('columns', columns.join(','));

            bootstrap.Modal.getInstance(document.getElementById('exportOptionsModal')).hide();
            window.open(url.toString(), '_blank');
            
            this.core.showNotification('success', 'Exportación iniciada');
            
        } catch (error) {
            this.core.handleError(error, 'BulkOperations.processExportWithOptions');
        } finally {
            this.core.hideSpinner();
        }
    }

    // === UTILIDADES Y VALIDACIONES ===
    getSelectedIds() {
        const tableFeatures = this.core.getModule('tableFeatures');
        return tableFeatures ? tableFeatures.getSelectedIds() : [];
    }

    validateSelection(selectedIds, action) {
        if (!selectedIds || selectedIds.length === 0) {
            this.core.showNotification('warning', `Por favor selecciona al menos un producto para ${action}`);
            return false;
        }
        return true;
    }

    confirmAction(message) {
        return this.config.confirmationRequired ? confirm(message) : true;
    }

    handleSuccessfulOperation() {
        // Limpiar selección
        const tableFeatures = this.core.getModule('tableFeatures');
        if (tableFeatures && typeof tableFeatures.clearSelection === 'function') {
            tableFeatures.clearSelection();
        }
        
        // Refrescar componentes si está configurado
        if (this.config.autoRefresh) {
            setTimeout(() => {
                if (this.core.hasModule('tableFeatures')) {
                    location.reload();
                } else {
                    this.core.refreshComponents();
                }
            }, 1500);
        }
    }

    removeExistingModal(modalId) {
        const existingModal = document.getElementById(modalId);
        if (existingModal) {
            // Cerrar modal si está abierto
            const modalInstance = bootstrap.Modal.getInstance(existingModal);
            if (modalInstance) {
                modalInstance.hide();
            }
            existingModal.remove();
        }
    }

    onSelectionChange(selectedProducts) {
        // Actualizar UI basado en la selección
        const count = selectedProducts ? selectedProducts.size : 0;
        
        // Aquí podrías habilitar/deshabilitar botones de operaciones masivas
        document.querySelectorAll('.bulk-action-btn').forEach(btn => {
            btn.disabled = count === 0;
            
            // Actualizar texto de botones si tienen contador
            const countElement = btn.querySelector('.selected-count');
            if (countElement) {
                countElement.textContent = count;
            }
        });
    }

    // === MÉTODOS DEL CICLO DE VIDA DEL MÓDULO ===
    onStateChange(key, newValue, oldValue) {
        console.debug(`BulkOperations: State changed - ${key}:`, newValue);
        
        if (key === 'selectedProducts') {
            this.onSelectionChange(newValue);
        }
    }

    onConfigChange(newConfig) {
        this.config = { ...this.config, ...newConfig };
        console.debug('BulkOperations: Config updated');
    }

    onError(error, context) {
        console.error(`BulkOperations: Error in ${context}:`, error);
        
        // Limpiar cualquier modal abierto en caso de error crítico
        ['statusUpdateModal', 'exportOptionsModal', 'categoryUpdateModal'].forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        });
    }

    refresh() {
        console.debug('BulkOperations: Refreshing module');
        // Actualizar estado de botones basado en selección actual
        this.onSelectionChange(this.core.getState('selectedProducts'));
    }

    destroy() {
        console.debug('BulkOperations: Destroying module');
        
        // Remover modales
        ['statusUpdateModal', 'exportOptionsModal', 'categoryUpdateModal'].forEach(modalId => {
            this.removeExistingModal(modalId);
        });
        
        // Limpiar referencias
        this.core = null;
        this.isInitialized = false;
    }

    // === MÉTODOS DE INFORMACIÓN ===
    getInfo() {
        return {
            name: this.name,
            isInitialized: this.isInitialized,
            config: { ...this.config },
            selectedCount: this.getSelectedIds().length
        };
    }
}

// Export para sistemas de módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductBulkOperations;
}