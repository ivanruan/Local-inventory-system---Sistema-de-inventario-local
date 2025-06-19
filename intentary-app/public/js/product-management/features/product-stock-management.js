// Product Stock Management - Gestión de Inventario
class ProductStockManagement {
    constructor(productManager) {
        this.productManager = productManager;
        this.activeModals = new Set();
        this.init();
    }

    init() {
        this.bindStockEvents();
        this.setupStockObserver();
    }

    // Event Binding
    bindStockEvents() {
        // Delegar eventos de stock a nivel documento
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="quick-stock"]')) {
                e.preventDefault();
                const btn = e.target;
                const productoId = btn.dataset.productoId;
                const currentStock = btn.dataset.currentStock;
                this.showQuickStockModal(productoId, currentStock);
            }
        });

        // Escuchar cambios en inputs de stock
        document.addEventListener('input', (e) => {
            if (e.target.matches('input[data-stock-input]')) {
                this.validateStockInput(e.target);
            }
        });

        // Limpiar modales al cerrar
        document.addEventListener('hidden.bs.modal', (e) => {
            if (e.target.id === 'quickStockModal') {
                this.cleanupStockModal();
            }
        });
    }

    setupStockObserver() {
        // Observer para cambios dinámicos en la tabla
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1 && node.querySelector) {
                                this.initializeStockElements(node);
                            }
                        });
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    initializeStockElements(container = document) {
        // Inicializar tooltips para elementos de stock
        container.querySelectorAll('[data-stock-tooltip]').forEach(el => {
            if (!el._tooltipInitialized) {
                new bootstrap.Tooltip(el);
                el._tooltipInitialized = true;
            }
        });
    }

    // Modal Management
    showQuickStockModal(productoId, currentStock) {
        this.removeExistingModal('quickStockModal');
        
        const modalHtml = this.generateStockModalHTML(productoId, currentStock);
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        const modal = new bootstrap.Modal(document.getElementById('quickStockModal'));
        this.activeModals.add('quickStockModal');
        
        // Configurar eventos específicos del modal
        this.setupModalEvents(productoId);
        
        modal.show();
        
        // Focus en el primer campo
        setTimeout(() => {
            document.querySelector('#quickStockModal select[name="tipo"]')?.focus();
        }, 100);
    }

    generateStockModalHTML(productoId, currentStock) {
        return `
            <div class="modal fade" id="quickStockModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-boxes me-2"></i>
                                Ajuste Rápido de Stock
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Stock Actual:</strong> ${parseInt(currentStock).toLocaleString()} unidades
                            </div>
                            
                            <form id="quickStockForm" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-exchange-alt me-1"></i>
                                            Tipo de Movimiento *
                                        </label>
                                        <select class="form-select" name="tipo" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="entrada">📥 Entrada (+)</option>
                                            <option value="salida">📤 Salida (-)</option>
                                            <option value="ajuste">⚖️ Ajuste</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor selecciona un tipo de movimiento</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-calculator me-1"></i>
                                            Cantidad *
                                        </label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="cantidad" 
                                               min="1" 
                                               max="999999"
                                               data-stock-input
                                               required>
                                        <div class="invalid-feedback">Ingresa una cantidad válida</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        Observaciones
                                    </label>
                                    <textarea class="form-control" 
                                              name="observaciones" 
                                              rows="3" 
                                              maxlength="500"
                                              placeholder="Motivo del ajuste (opcional)"></textarea>
                                    <div class="form-text">
                                        <span class="char-counter">0</span>/500 caracteres
                                    </div>
                                </div>
                                
                                <div id="stockPreview" class="alert alert-secondary d-none">
                                    <strong>Vista previa:</strong>
                                    <span id="previewText"></span>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </button>
                            <button type="button" 
                                    class="btn btn-primary" 
                                    onclick="productManager.stockManager.submitQuickStock(${productoId})">
                                <i class="fas fa-save me-1"></i>
                                Actualizar Stock
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    setupModalEvents(productoId) {
        const modal = document.getElementById('quickStockModal');
        const form = modal.querySelector('#quickStockForm');
        const tipoSelect = form.querySelector('select[name="tipo"]');
        const cantidadInput = form.querySelector('input[name="cantidad"]');
        const observacionesTextarea = form.querySelector('textarea[name="observaciones"]');
        const charCounter = modal.querySelector('.char-counter');
        const preview = modal.querySelector('#stockPreview');

        // Actualizar contador de caracteres
        observacionesTextarea?.addEventListener('input', (e) => {
            const length = e.target.value.length;
            charCounter.textContent = length;
            charCounter.className = length > 450 ? 'char-counter text-warning' : 'char-counter';
        });

        // Vista previa del cambio
        const updatePreview = () => {
            const tipo = tipoSelect.value;
            const cantidad = parseInt(cantidadInput.value) || 0;
            const currentStock = parseInt(modal.querySelector('.alert-info strong').nextSibling.textContent.replace(/[^\d]/g, ''));

            if (tipo && cantidad > 0) {
                let newStock = currentStock;
                let operacion = '';

                switch (tipo) {
                    case 'entrada':
                        newStock = currentStock + cantidad;
                        operacion = `${currentStock.toLocaleString()} + ${cantidad.toLocaleString()} = ${newStock.toLocaleString()}`;
                        break;
                    case 'salida':
                        newStock = Math.max(0, currentStock - cantidad);
                        operacion = `${currentStock.toLocaleString()} - ${cantidad.toLocaleString()} = ${newStock.toLocaleString()}`;
                        break;
                    case 'ajuste':
                        newStock = cantidad;
                        operacion = `Ajustar a ${newStock.toLocaleString()} unidades`;
                        break;
                }

                preview.querySelector('#previewText').textContent = operacion;
                preview.classList.remove('d-none');
            } else {
                preview.classList.add('d-none');
            }
        };

        tipoSelect?.addEventListener('change', updatePreview);
        cantidadInput?.addEventListener('input', updatePreview);

        // Validación en tiempo real
        form.addEventListener('input', () => {
            this.validateStockForm(form);
        });

        // Envío con Enter
        cantidadInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && this.validateStockForm(form)) {
                this.submitQuickStock(productoId);
            }
        });
    }

    validateStockInput(input) {
        const value = parseInt(input.value);
        const isValid = value > 0 && value <= 999999;
        
        input.classList.toggle('is-invalid', !isValid);
        return isValid;
    }

    validateStockForm(form) {
        const tipo = form.querySelector('select[name="tipo"]').value;
        const cantidad = form.querySelector('input[name="cantidad"]').value;
        const cantidadNum = parseInt(cantidad);

        let isValid = true;

        // Validar tipo
        const tipoSelect = form.querySelector('select[name="tipo"]');
        if (!tipo) {
            tipoSelect.classList.add('is-invalid');
            isValid = false;
        } else {
            tipoSelect.classList.remove('is-invalid');
        }

        // Validar cantidad
        const cantidadInput = form.querySelector('input[name="cantidad"]');
        if (!cantidad || cantidadNum <= 0) {
            cantidadInput.classList.add('is-invalid');
            isValid = false;
        } else {
            cantidadInput.classList.remove('is-invalid');
        }

        return isValid;
    }

    // Stock Operations
    async submitQuickStock(productoId) {
        const form = document.getElementById('quickStockForm');
        
        if (!this.validateStockForm(form)) {
            this.showValidationErrors(form);
            return;
        }

        const formData = new FormData(form);
        const tipo = formData.get('tipo');
        const cantidad = parseInt(formData.get('cantidad'));
        const observaciones = formData.get('observaciones');

        const submitBtn = form.closest('.modal').querySelector('.btn-primary');
        const originalText = submitBtn.innerHTML;
        
        // Deshabilitar botón y mostrar loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualizando...';

        this.productManager.showSpinner();

        try {
            const result = await this.productManager.makeRequest(`/productos/${productoId}/quick-stock`, {
                method: 'POST',
                body: JSON.stringify({
                    tipo: tipo,
                    cantidad: cantidad,
                    observaciones: observaciones || null
                })
            });

            if (result.success) {
                await this.handleSuccessfulStockUpdate(productoId, result);
            } else {
                this.handleStockUpdateError(result.message || 'Error desconocido');
            }
        } catch (error) {
            console.error('Stock update error:', error);
            this.handleStockUpdateError('Error de conexión. Intenta nuevamente.');
        } finally {
            this.productManager.hideSpinner();
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    async handleSuccessfulStockUpdate(productoId, result) {
        // Actualizar display en la tabla
        this.updateStockDisplay(productoId, result.newStock);
        
        // Mostrar notificación de éxito
        this.showStockUpdateNotification('success', result.message || 'Stock actualizado correctamente');
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('quickStockModal'));
        modal?.hide();
        
        // Si hay callback adicional
        if (typeof window.onStockUpdated === 'function') {
            window.onStockUpdated(productoId, result.newStock, result.movimento);
        }
    }

    handleStockUpdateError(message) {
        this.showStockUpdateNotification('error', message);
    }

    showValidationErrors(form) {
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
            firstInvalid.focus();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // UI Updates
    updateStockDisplay(productoId, newStock) {
        // Actualizar en la tabla principal
        const stockCell = document.querySelector(`tr[data-producto-id="${productoId}"] .stock-value`);
        if (stockCell) {
            const oldValue = parseInt(stockCell.textContent.replace(/[^\d]/g, ''));
            stockCell.textContent = parseInt(newStock).toLocaleString();
            
            // Animación de cambio
            this.animateStockChange(stockCell, oldValue, newStock);
        }

        // Actualizar botones con data-current-stock
        document.querySelectorAll(`[data-producto-id="${productoId}"][data-current-stock]`).forEach(btn => {
            btn.dataset.currentStock = newStock;
        });

        // Actualizar indicadores de stock bajo
        this.updateStockIndicators(productoId, newStock);
    }

    animateStockChange(element, oldValue, newValue) {
        const isIncrease = newValue > oldValue;
        const colorClass = isIncrease ? 'text-success' : 'text-warning';
        
        element.classList.add(colorClass, 'fw-bold');
        element.style.transform = 'scale(1.1)';
        
        setTimeout(() => {
            element.classList.remove(colorClass, 'fw-bold');
            element.style.transform = '';
        }, 1500);
    }

    updateStockIndicators(productoId, newStock) {
        const row = document.querySelector(`tr[data-producto-id="${productoId}"]`);
        if (!row) return;

        const stockNum = parseInt(newStock);
        const stockBadge = row.querySelector('.stock-badge');
        
        if (stockBadge) {
            // Remover clases anteriores
            stockBadge.classList.remove('bg-danger', 'bg-warning', 'bg-success');
            
            // Aplicar nueva clase según el stock
            if (stockNum === 0) {
                stockBadge.classList.add('bg-danger');
                stockBadge.textContent = 'Sin Stock';
            } else if (stockNum <= 10) {
                stockBadge.classList.add('bg-warning');
                stockBadge.textContent = 'Stock Bajo';
            } else {
                stockBadge.classList.add('bg-success');
                stockBadge.textContent = 'En Stock';
            }
        }
    }

    showStockUpdateNotification(type, message) {
        // Crear notificación toast
        const toastId = 'stockToast_' + Date.now();
        const toastClass = type === 'success' ? 'text-bg-success' : 'text-bg-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const toastHtml = `
            <div id="${toastId}" class="toast ${toastClass}" role="alert">
                <div class="toast-header">
                    <i class="fas ${icon} me-2"></i>
                    <strong class="me-auto">Stock Management</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;

        // Asegurar que existe el container de toasts
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '1080';
            document.body.appendChild(toastContainer);
        }

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toast = new bootstrap.Toast(document.getElementById(toastId), {
            delay: type === 'success' ? 3000 : 5000
        });
        
        toast.show();

        // Limpiar toast después de que se oculte
        document.getElementById(toastId).addEventListener('hidden.bs.toast', (e) => {
            e.target.remove();
        });
    }

    // Cleanup Methods
    removeExistingModal(modalId) {
        const existingModal = document.getElementById(modalId);
        if (existingModal) {
            const modalInstance = bootstrap.Modal.getInstance(existingModal);
            if (modalInstance) {
                modalInstance.hide();
            }
            existingModal.remove();
        }
        this.activeModals.delete(modalId);
    }

    cleanupStockModal() {
        this.removeExistingModal('quickStockModal');
    }

    // Utility Methods
    formatStock(value) {
        return parseInt(value).toLocaleString();
    }

    calculateStockValue(stock, price) {
        return (parseInt(stock) * parseFloat(price)).toFixed(2);
    }

    getStockStatus(stock) {
        const stockNum = parseInt(stock);
        if (stockNum === 0) return { status: 'sin-stock', label: 'Sin Stock', class: 'bg-danger' };
        if (stockNum <= 10) return { status: 'stock-bajo', label: 'Stock Bajo', class: 'bg-warning' };
        return { status: 'en-stock', label: 'En Stock', class: 'bg-success' };
    }

    // Public API Methods
    openStockModal(productoId, currentStock) {
        this.showQuickStockModal(productoId, currentStock);
    }

    refreshStockDisplay(productoId) {
        // Método para refrescar display desde servidor si es necesario
        this.productManager.makeRequest(`/productos/${productoId}/stock-info`)
            .then(result => {
                if (result.success) {
                    this.updateStockDisplay(productoId, result.stock);
                }
            })
            .catch(error => {
                console.error('Error refreshing stock:', error);
            });
    }

    destroy() {
        // Limpiar todos los modales activos
        this.activeModals.forEach(modalId => {
            this.removeExistingModal(modalId);
        });
        this.activeModals.clear();
    }
}

// Export para sistemas de módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductStockManagement;
}

window.ProductStockManagement = ProductStockManagement;