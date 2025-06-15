// Product Form JavaScript - public/js/product-form.js
// Product Management JavaScript - public/js/productos.js

class ProductManager {
    constructor() {
        this.init();
    }

    init() {
        this.addCSRFToken();
        this.bindEvents();
        this.initializeTooltips();
        this.initializeDataTable();
    }

    // Add CSRF token to meta tag if not already present
    addCSRFToken() {
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = document.querySelector('input[name="_token"]')?.value || '';
            document.getElementsByTagName('head')[0].appendChild(meta);
        }
    }

    // Bind all event listeners
    bindEvents() {
        // Bind search input with debounce
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), 500));
        }

        // Bind filter changes
        const filterSelects = document.querySelectorAll('select[name="categoria"], select[name="marca"], select[name="status"], select[name="stock_filter"]');
        filterSelects.forEach(select => {
            select.addEventListener('change', this.handleFilterChange.bind(this));
        });

        // Bind table row hover effects
        this.bindTableHoverEffects();

        // Bind bulk actions
        this.bindBulkActions();
    }

    // Initialize tooltips
    initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialize enhanced data table features
    initializeDataTable() {
        this.addTableFeatures();
        this.addColumnSorting();
        this.addRowSelection();
    }

    // Add enhanced table features
    addTableFeatures() {
        const table = document.querySelector('.table-responsive table');
        if (!table) return;

        // Add row numbers
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            const firstCell = row.querySelector('td:first-child');
            if (firstCell) {
                const rowNumber = document.createElement('small');
                rowNumber.className = 'text-muted me-2';
                rowNumber.textContent = `#${index + 1}`;
                firstCell.prepend(rowNumber);
            }
        });

        // Add column resizing
        this.addColumnResizing(table);
    }

    // Add column resizing functionality
    addColumnResizing(table) {
        const headers = table.querySelectorAll('th');
        headers.forEach(header => {
            header.style.position = 'relative';
            header.style.cursor = 'col-resize';
            
            const resizer = document.createElement('div');
            resizer.className = 'column-resizer';
            resizer.style.cssText = `
                position: absolute;
                top: 0;
                right: 0;
                width: 3px;
                height: 100%;
                background: rgba(0,0,0,0.1);
                cursor: col-resize;
                opacity: 0;
                transition: opacity 0.2s;
            `;
            
            header.appendChild(resizer);
            
            header.addEventListener('mouseenter', () => {
                resizer.style.opacity = '1';
            });
            
            header.addEventListener('mouseleave', () => {
                resizer.style.opacity = '0';
            });
        });
    }

    // Add column sorting functionality
    addColumnSorting() {
        const sortableHeaders = document.querySelectorAll('th a[href*="sort"]');
        sortableHeaders.forEach(header => {
            header.addEventListener('click', (e) => {
                this.showLoadingSpinner();
            });
        });
    }

    // Add row selection functionality
    addRowSelection() {
        const table = document.querySelector('.table-responsive table');
        if (!table) return;

        // Add select all checkbox to header
        const headerRow = table.querySelector('thead tr');
        const selectAllTh = document.createElement('th');
        selectAllTh.innerHTML = '<input type="checkbox" id="selectAll" class="form-check-input">';
        headerRow.insertBefore(selectAllTh, headerRow.firstChild);

        // Add individual checkboxes to each row
        const bodyRows = table.querySelectorAll('tbody tr');
        bodyRows.forEach(row => {
            const selectTd = document.createElement('td');
            selectTd.innerHTML = `<input type="checkbox" class="form-check-input row-select" value="${row.dataset.id || ''}">`;
            row.insertBefore(selectTd, row.firstChild);
        });

        // Bind select all functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', this.handleSelectAll.bind(this));
        }

        // Bind individual row selection
        const rowCheckboxes = document.querySelectorAll('.row-select');
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', this.handleRowSelection.bind(this));
        });
    }

    // Handle select all functionality
    handleSelectAll(e) {
        const rowCheckboxes = document.querySelectorAll('.row-select');
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
        this.updateBulkActions();
    }

    // Handle individual row selection
    handleRowSelection() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-select');
        const checkedBoxes = document.querySelectorAll('.row-select:checked');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < rowCheckboxes.length;
            selectAllCheckbox.checked = checkedBoxes.length === rowCheckboxes.length;
        }
        
        this.updateBulkActions();
    }

    // Update bulk actions based on selection
    updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.row-select:checked');
        const bulkActionBar = document.getElementById('bulkActionBar');
        
        if (checkedBoxes.length > 0) {
            if (!bulkActionBar) {
                this.createBulkActionBar();
            }
            document.getElementById('selectedCount').textContent = checkedBoxes.length;
            document.getElementById('bulkActionBar').style.display = 'block';
        } else if (bulkActionBar) {
            bulkActionBar.style.display = 'none';
        }
    }

    // Create bulk action bar
    createBulkActionBar() {
        const container = document.querySelector('.container-fluid');
        const actionBar = document.createElement('div');
        actionBar.id = 'bulkActionBar';
        actionBar.className = 'alert alert-info d-flex justify-content-between align-items-center';
        actionBar.style.display = 'none';
        actionBar.innerHTML = `
            <div>
                <strong><span id="selectedCount">0</span></strong> productos seleccionados
            </div>
            <div class="btn-group">
                <button class="btn btn-outline-warning btn-sm" onclick="productManager.bulkUpdateStatus()">
                    <i class="bi bi-pencil"></i> Cambiar Estado
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="productManager.bulkExport()">
                    <i class="bi bi-download"></i> Exportar
                </button>
                <button class="btn btn-outline-danger btn-sm" onclick="productManager.bulkDelete()">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="productManager.clearSelection()">
                    <i class="bi bi-x"></i> Cancelar
                </button>
            </div>
        `;
        
        const firstChild = container.firstElementChild;
        container.insertBefore(actionBar, firstChild.nextSibling);
    }

    // Bind bulk actions
    bindBulkActions() {
        // This will be called after the bulk action bar is created
    }

    // Handle search with debounce
    handleSearch(e) {
        const searchTerm = e.target.value.trim();
        if (searchTerm.length >= 3 || searchTerm.length === 0) {
            this.performSearch(searchTerm);
        }
    }

    // Perform search
    performSearch(term) {
        const url = new URL(window.location.href);
        if (term) {
            url.searchParams.set('search', term);
        } else {
            url.searchParams.delete('search');
        }
        url.searchParams.delete('page'); // Reset pagination
        window.location.href = url.toString();
    }

    // Handle filter changes
    handleFilterChange(e) {
        const url = new URL(window.location.href);
        const filterName = e.target.name;
        const filterValue = e.target.value;
        
        if (filterValue) {
            url.searchParams.set(filterName, filterValue);
        } else {
            url.searchParams.delete(filterName);
        }
        url.searchParams.delete('page'); // Reset pagination
        window.location.href = url.toString();
    }

    // Bind table hover effects
    bindTableHoverEffects() {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.transform = 'scale(1.002)';
                row.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                row.style.transition = 'all 0.2s ease';
            });
            
            row.addEventListener('mouseleave', () => {
                row.style.transform = 'scale(1)';
                row.style.boxShadow = 'none';
            });
        });
    }

    // Show loading spinner
    showLoadingSpinner() {
        const spinner = document.createElement('div');
        spinner.id = 'loadingSpinner';
        spinner.className = 'position-fixed top-50 start-50 translate-middle';
        spinner.style.zIndex = '9999';
        spinner.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        `;
        document.body.appendChild(spinner);
    }

    // Hide loading spinner
    hideLoadingSpinner() {
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) {
            spinner.remove();
        }
    }

    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Bulk actions
    bulkUpdateStatus() {
        const selectedIds = this.getSelectedIds();
        if (selectedIds.length === 0) return;

        const modal = this.createStatusUpdateModal();
        modal.show();
    }

    bulkExport() {
        const selectedIds = this.getSelectedIds();
        if (selectedIds.length === 0) return;

        const url = new URL(window.location.origin + '/productos/export');
        url.searchParams.set('ids', selectedIds.join(','));
        window.open(url.toString(), '_blank');
    }

    bulkDelete() {
        const selectedIds = this.getSelectedIds();
        if (selectedIds.length === 0) return;

        if (confirm(`¿Estás seguro de eliminar ${selectedIds.length} productos seleccionados?`)) {
            this.performBulkDelete(selectedIds);
        }
    }

    clearSelection() {
        const checkboxes = document.querySelectorAll('.row-select, #selectAll');
        checkboxes.forEach(checkbox => checkbox.checked = false);
        this.updateBulkActions();
    }

    // Get selected product IDs
    getSelectedIds() {
        const checkedBoxes = document.querySelectorAll('.row-select:checked');
        return Array.from(checkedBoxes).map(checkbox => checkbox.value).filter(id => id);
    }

    // Create status update modal
    createStatusUpdateModal() {
        const modalHtml = `
            <div class="modal fade" id="statusUpdateModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Actualizar Estado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="bulkStatusForm">
                                <div class="mb-3">
                                    <label class="form-label">Nuevo Estado</label>
                                    <select class="form-select" name="status" required>
                                        <option value="">Seleccionar estado...</option>
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                        <option value="Obsoleto">Obsoleto</option>
                                        <option value="Stock Optimo">Stock Óptimo</option>
                                        <option value="Stock Bajo">Stock Bajo</option>
                                        <option value="Fuera de Stock">Fuera de Stock</option>
                                        <option value="Sobre Stock">Sobre Stock</option>
                                        <option value="En Reorden">En Reorden</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Observaciones (opcional)</label>
                                    <textarea class="form-control" name="observaciones" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="productManager.submitBulkStatusUpdate()">Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('statusUpdateModal');
        if (existingModal) existingModal.remove();

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        return new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    }

    // Submit bulk status update
    submitBulkStatusUpdate() {
        const form = document.getElementById('bulkStatusForm');
        const formData = new FormData(form);
        const selectedIds = this.getSelectedIds();

        if (!formData.get('status')) {
            alert('Por favor selecciona un estado');
            return;
        }

        this.showLoadingSpinner();

        fetch('/productos/bulk-update-status', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                ids: selectedIds,
                status: formData.get('status'),
                observaciones: formData.get('observaciones')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al actualizar productos: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar productos');
        })
        .finally(() => {
            this.hideLoadingSpinner();
            bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
        });
    }

    // Perform bulk delete
    performBulkDelete(selectedIds) {
        this.showLoadingSpinner();

        fetch('/productos/bulk-delete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al eliminar productos: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar productos');
        })
        .finally(() => {
            this.hideLoadingSpinner();
        });
    }

    // Utility functions for stock management
    updateStockDisplay(productoId, newStock) {
        const stockCell = document.querySelector(`tr[data-id="${productoId}"] .stock-actual`);
        if (stockCell) {
            stockCell.textContent = newStock;
            
            // Update row color based on stock level
            const row = stockCell.closest('tr');
            row.classList.remove('table-danger', 'table-warning', 'table-info');
            
            if (newStock === 0) {
                row.classList.add('table-danger');
            } else if (newStock <= parseInt(stockCell.dataset.minStock || 0)) {
                row.classList.add('table-warning');
            }
        }
    }

    // Quick stock adjustment
    showQuickStockModal(productoId, currentStock) {
        const modalHtml = `
            <div class="modal fade" id="quickStockModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajuste Rápido de Stock</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="quickStockForm">
                                <div class="mb-3">
                                    <label class="form-label">Stock Actual: <strong>${currentStock}</strong></label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Movimiento</label>
                                    <select class="form-select" name="tipo" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="entrada">Entrada (+)</option>
                                        <option value="salida">Salida (-)</option>
                                        <option value="ajuste">Ajuste</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" name="cantidad" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" name="observaciones" rows="2"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="productManager.submitQuickStock(${productoId})">Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('quickStockModal');
        if (existingModal) existingModal.remove();

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        const modal = new bootstrap.Modal(document.getElementById('quickStockModal'));
        modal.show();
    }

    // Submit quick stock adjustment
    submitQuickStock(productoId) {
        const form = document.getElementById('quickStockForm');
        const formData = new FormData(form);

        if (!formData.get('tipo') || !formData.get('cantidad')) {
            alert('Por favor completa todos los campos requeridos');
            return;
        }

        this.showLoadingSpinner();

        fetch(`/productos/${productoId}/quick-stock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                tipo: formData.get('tipo'),
                cantidad: parseInt(formData.get('cantidad')),
                observaciones: formData.get('observaciones')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateStockDisplay(productoId, data.newStock);
                bootstrap.Modal.getInstance(document.getElementById('quickStockModal')).hide();
            } else {
                alert('Error al actualizar stock: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar stock');
        })
        .finally(() => {
            this.hideLoadingSpinner();
        });
    }
}

// Global functions for onclick handlers and compatibility
function toggleFilters() {
    const panel = document.getElementById('filtrosPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function verMovimientos(productoId) {
    const modal = new bootstrap.Modal(document.getElementById('movimientosModal'));
    const content = document.getElementById('movimientosContent');
    
    // Show loading
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch movements
    fetch(`/productos/${productoId}/movimientos`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="table-responsive">';
            html += '<table class="table table-sm">';
            html += '<thead><tr><th>Fecha</th><th>Tipo</th><th>Cantidad</th><th>Stock Anterior</th><th>Stock Nuevo</th><th>Observaciones</th></tr></thead>';
            html += '<tbody>';
            
            if (data.length > 0) {
                data.forEach(mov => {
                    html += `<tr>
                        <td>${mov.fecha}</td>
                        <td><span class="badge bg-${mov.tipo === 'Entrada' ? 'success' : 'danger'}">${mov.tipo}</span></td>
                        <td>${mov.cantidad}</td>
                        <td>${mov.stock_anterior || '-'}</td>
                        <td>${mov.stock_nuevo || '-'}</td>
                        <td>${mov.observaciones || '-'}</td>
                    </tr>`;
                });
            } else {
                html += '<tr><td colspan="6" class="text-center text-muted">No hay movimientos registrados</td></tr>';
            }
            
            html += '</tbody></table></div>';
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Error al cargar los movimientos</div>';
            console.error('Error:', error);
        });
}

function exportarExcel() {
    const url = new URL(window.location.href);
    url.pathname = url.pathname.replace('/productos', '/productos/export');
    window.open(url.toString(), '_blank');
}

function quickStockAdjust(productoId, currentStock) {
    window.productManager.showQuickStockModal(productoId, currentStock);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.productManager = new ProductManager();
    
    // Auto-refresh for stock status every 5 minutes
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            // Only refresh if user is actively viewing the page
            const lastActivity = localStorage.getItem('lastActivity');
            const now = Date.now();
            if (!lastActivity || now - parseInt(lastActivity) < 300000) { // 5 minutes
                location.reload();
            }
        }
    }, 300000);
    
    // Track user activity
    document.addEventListener('click', () => {
        localStorage.setItem('lastActivity', Date.now().toString());
    });
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductManager;
}