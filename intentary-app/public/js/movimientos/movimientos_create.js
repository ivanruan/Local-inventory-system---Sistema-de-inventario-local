document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo');
    const productoSelect = document.getElementById('producto_id');
    const cantidadInput = document.getElementById('cantidad');
    const camposEntrada = document.getElementById('campos-entrada');
    const camposSalida = document.getElementById('campos-salida');
    const infoProducto = document.getElementById('info-producto');
    const sinProducto = document.getElementById('sin-producto');
    const alertaStock = document.getElementById('alerta-stock');
    const confirmacionSalida = document.getElementById('confirmacion-salida');

    // Manejar cambio de tipo de movimiento
    tipoSelect.addEventListener('change', function() {
        if (this.value === 'entrada') {
            camposEntrada.classList.remove('d-none');
            camposSalida.classList.add('d-none');
            // Limpiar campos de salida
            document.getElementById('proyecto_id').value = '';
            document.getElementById('usuario_destino').value = '';
        } else if (this.value === 'salida') {
            camposSalida.classList.remove('d-none');
            camposEntrada.classList.add('d-none');
            // Limpiar campos de entrada
            document.getElementById('proveedor_id').value = '';
            document.getElementById('precio_unitario').value = '';
        } else {
            camposEntrada.classList.add('d-none');
            camposSalida.classList.add('d-none');
        }
        verificarStock();
    });

    // Manejar cambio de producto
    productoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const stock = parseFloat(selectedOption.dataset.stock) || 0;
            const unidad = selectedOption.dataset.unidad || 'unidades';
            const codigo = selectedOption.text.split(' - ')[0];
            const nombre = selectedOption.text.split(' - ')[1];
            
            // Mostrar información del producto
            document.getElementById('producto-codigo').textContent = codigo;
            document.getElementById('producto-nombre').textContent = nombre;
            document.getElementById('producto-stock').textContent = stock.toLocaleString();
            document.getElementById('producto-unidad').textContent = unidad;
            document.getElementById('unidad-display').textContent = unidad;
            
            // Determinar estado del stock
            const stockStatus = document.getElementById('stock-status');
            if (stock <= 0) {
                stockStatus.textContent = 'Sin Stock';
                stockStatus.className = 'badge bg-danger';
            } else if (stock <= 10) { // Assuming 10 is your low stock threshold
                stockStatus.textContent = 'Stock Bajo';
                stockStatus.className = 'badge bg-warning';
            } else {
                stockStatus.textContent = 'Stock Normal';
                stockStatus.className = 'badge bg-success';
            }
            
            infoProducto.classList.remove('d-none');
            sinProducto.classList.add('d-none');
            
            // Mostrar alerta si stock bajo (but not zero)
            if (stock <= 10 && stock > 0) {
                alertaStock.classList.remove('d-none');
            } else {
                alertaStock.classList.add('d-none');
            }
        } else {
            infoProducto.classList.add('d-none');
            sinProducto.classList.remove('d-none');
            alertaStock.classList.add('d-none');
            document.getElementById('unidad-display').textContent = 'Unidad';
        }
        verificarStock();
    });

    // Verificar stock cuando cambia la cantidad
    cantidadInput.addEventListener('input', verificarStock);

    function verificarStock() {
        const tipo = tipoSelect.value;
        const selectedOption = productoSelect.options[productoSelect.selectedIndex];
        const cantidad = parseFloat(cantidadInput.value) || 0;
        
        // Only perform stock check for 'salida' type and if a product is selected
        if (tipo === 'salida' && selectedOption && selectedOption.value) {
            const stock = parseFloat(selectedOption.dataset.stock) || 0;
            
            if (cantidad > stock) {
                confirmacionSalida.classList.remove('d-none');
                cantidadInput.classList.add('is-invalid');
            } else {
                confirmacionSalida.classList.add('d-none');
                cantidadInput.classList.remove('is-invalid');
            }
        } else {
            // Hide alert and remove invalid class if not a 'salida' or no product
            confirmacionSalida.classList.add('d-none');
            cantidadInput.classList.remove('is-invalid');
        }
    }

    // Inicializar campos según el valor seleccionado (útil para old values en caso de validación fallida)
    // Disparar los eventos 'change' para que la lógica se aplique al cargar la página si hay valores antiguos.
    if (tipoSelect.value) {
        tipoSelect.dispatchEvent(new Event('change'));
    }
    if (productoSelect.value) {
        productoSelect.dispatchEvent(new Event('change'));
    }
});