
// Product Table Features - Funcionalidades específicas de la tabla
class ProductTableFeatures {
    constructor(productManager) {
        this.productManager = productManager;
    }

    // Inicialización de características de tabla
    initializeTableFeatures() {
        this.addRowHoverEffects();
        this.addRowSelection();
        this.addSortingIndicators();
        this.addTableEnhancements();
    }

    // Mejoras generales de tabla
    addTableEnhancements() {
        const table = document.querySelector('.table-responsive table');
        if (!table) return;

        this.addRowNumbers();
        this.addColumnResizing(table);
    }

    addRowNumbers() {
        document.querySelectorAll('tbody tr').forEach((row, index) => {
            const firstCell = row.querySelector('td:first-child');
            if (firstCell && !firstCell.querySelector('.row-number')) {
                const rowNumber = document.createElement('small');
                rowNumber.className = 'text-muted me-2 row-number';
                rowNumber.textContent = `#${index + 1}`;
                firstCell.prepend(rowNumber);
            }
        });
    }

    addColumnResizing(table) {
        table.querySelectorAll('th').forEach(header => {
            if (header.querySelector('.column-resizer')) return;

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
            
            header.style.position = 'relative';
            header.appendChild(resizer);
            
            header.addEventListener('mouseenter', () => resizer.style.opacity = '1');
            header.addEventListener('mouseleave', () => resizer.style.opacity = '0');
        });
    }

    // Efectos visuales de filas
    addRowHoverEffects() {
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.cssText += 'transform: scale(1.002); box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: all 0.2s ease;';
            });
            
            row.addEventListener('mouseleave', () => {
                row.style.cssText = row.style.cssText.replace(/transform:[^;]*;?/g, '').replace(/box-shadow:[^;]*;?/g, '');
            });
        });
    }

    // Indicadores de ordenamiento
    addSortingIndicators() {
        document.querySelectorAll('th a[href*="sort"]').forEach(link => {
            link.addEventListener('click', () => this.productManager.showSpinner());
        });
    }

    // Sistema de selección de filas
    addRowSelection() {
        const table = document.querySelector('.table-responsive table');
        if (!table || table.querySelector('.row-select')) return;

        this.createSelectionColumn(table);
        this.bindSelectionEvents();
    }

    createSelectionColumn(table) {
        // Add header checkbox
        const headerRow = table.querySelector('thead tr');
        const selectAllTh = document.createElement('th');
        selectAllTh.innerHTML = '<input type="checkbox" id="selectAll" class="form-check-input">';
        headerRow.insertBefore(selectAllTh, headerRow.firstChild);

        // Add row checkboxes
        table.querySelectorAll('tbody tr').forEach(row => {
            const selectTd = document.createElement('td');
            selectTd.innerHTML = `<input type="checkbox" class="form-check-input row-select" value="${row.dataset.id || ''}">`;
            row.insertBefore(selectTd, row.firstChild);
        });
    }

    bindSelectionEvents() {
        document.getElementById('selectAll')?.addEventListener('change', e => {
            document.querySelectorAll('.row-select').forEach(cb => cb.checked = e.target.checked);
            this.updateBulkActions();
        });

        document.querySelectorAll('.row-select').forEach(cb => {
            cb.addEventListener('change', () => {
                const all = document.querySelectorAll('.row-select');
                const checked = document.querySelectorAll('.row-select:checked');
                const selectAll = document.getElementById('selectAll');
                
                if (selectAll) {
                    selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
                    selectAll.checked = checked.length === all.length;
                }
                this.updateBulkActions();
            });
        });
    }

    // Gestión de acciones masivas (interfaz con bulk operations)
    updateBulkActions() {
        const selected = document.querySelectorAll('.row-select:checked');
        let actionBar = document.getElementById('bulkActionBar');
        
        if (selected.length > 0) {
            if (!actionBar) actionBar = this.createBulkActionBar();
            document.getElementById('selectedCount').textContent = selected.length;
            actionBar.style.display = 'block';
        } else if (actionBar) {
            actionBar.style.display = 'none';
        }
    }

    createBulkActionBar() {
        const actionBar = document.createElement('div');
        actionBar.id = 'bulkActionBar';
        actionBar.className = 'alert alert-info d-flex justify-content-between align-items-center';
        actionBar.style.display = 'none';
        actionBar.innerHTML = `
            <div><strong><span id="selectedCount">0</span></strong> productos seleccionados</div>
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
                <button class="btn btn-outline-info btn-sm" onclick="productManager.bulkOperations?.bulkDuplicate()">
                    <i class="bi bi-copy"></i> Duplicar
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="productManager.clearSelection()">
                    <i class="bi bi-x"></i> Cancelar
                </button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(actionBar, container.firstElementChild.nextSibling);
        return actionBar;
    }

    // Utilidades para selección
    getSelectedIds() {
        return Array.from(document.querySelectorAll('.row-select:checked'))
            .map(cb => cb.value)
            .filter(id => id);
    }

    clearSelection() {
        document.querySelectorAll('.row-select, #selectAll').forEach(cb => cb.checked = false);
        this.updateBulkActions();
    }

    // Actualización de visualización de stock
    updateStockDisplay(productoId, newStock) {
        const stockCell = document.querySelector(`tr[data-id="${productoId}"] .stock-actual`);
        if (!stockCell) return;

        stockCell.textContent = newStock;
        const row = stockCell.closest('tr');
        row.classList.remove('table-danger', 'table-warning', 'table-info');
        
        if (newStock === 0) {
            row.classList.add('table-danger');
        } else if (newStock <= parseInt(stockCell.dataset.minStock || 0)) {
            row.classList.add('table-warning');
        }
    }
}

// Export para sistemas de módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductTableFeatures;
}