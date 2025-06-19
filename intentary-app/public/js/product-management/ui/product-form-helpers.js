/**
 * Product Form Helpers Module
 * Maneja toda la funcionalidad relacionada con formularios dinámicos
 * de productos, categorías, marcas y ubicaciones.
 */

class FormHelpers {
    constructor(core = null) {
        this.core = core;
        this.endpoints = {
            categoria: { url: '/categorias', field: 'nombre' },
            marca: { url: '/marcas', field: 'nombre' },
            ubicacion: { url: '/ubicaciones', field: 'codigo' }
        };
    }

    /**
     * Obtiene el token CSRF del documento
     */
    getCSRFToken() {
        return document.querySelector('meta[name="csrf-csrf-token"]')?.getAttribute('content') || '';
    }

    /**
     * Realiza una petición a la API (modificado para usar core.makeRequest si está disponible)
     */
    async makeApiRequest(url, body) {
        if (this.core && typeof this.core.makeRequest === 'function') {
            return this.core.makeRequest(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            });
        } else {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            });
        }
    }

    /**
     * Muestra mensaje de error (modificado para usar el sistema de notificación del core)
     */
    showError(message) {
        if (this.core && typeof this.core.showToast === 'function') {
            this.core.showToast('Error', message, 'danger');
        } else {
            alert(message);
            console.error('FormHelpers Error:', message);
        }
    }

    /**
     * Muestra mensaje de éxito (modificado para usar el sistema de notificación del core)
     */
    showSuccess(message) {
        if (this.core && typeof this.core.showToast === 'function') {
            this.core.showToast('Éxito', message, 'success');
        } else {
            console.log('FormHelpers Success:', message);
        }
    }

    // --- MÉTODOS EXISTENTES DE TU FORMHELPERS (mantener como estén) ---
    // Ejemplos:
    toggleInput(selectId, inputId, buttonId) {
        const select = document.getElementById(selectId);
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);

        if (select && input && button) {
            if (select.value === 'otra') {
                input.style.display = 'block';
                input.focus();
                button.textContent = 'Guardar';
            } else {
                input.style.display = 'none';
                input.value = '';
                button.textContent = 'Añadir';
            }
        }
    }

    async agregarItem(type, value) {
        if (!value.trim()) {
            this.showError(`Por favor, introduce un nombre para la ${type}.`);
            return;
        }

        const endpointInfo = this.endpoints[type];
        if (!endpointInfo) {
            this.showError(`Tipo de item desconocido: ${type}`);
            return;
        }

        try {
            const response = await this.makeApiRequest(endpointInfo.url, {
                [endpointInfo.field]: value
            });
            const data = await response.json();

            if (response.ok) {
                this.showSuccess(`${type.charAt(0).toUpperCase() + type.slice(1)} "${data[endpointInfo.field]}" agregada correctamente.`);
                // Lógica para actualizar el select (puedes necesitar recargar o añadir la opción)
                // Esto podría ser un evento o un método del core si lo manejas centralmente
                this.updateSelectOptions(type, data.id, data[endpointInfo.field]);
            } else {
                this.showError(data.message || `Error al agregar la ${type}.`);
            }
        } catch (error) {
            this.showError(`Error de red o servidor: ${error.message}`);
        }
    }
    
    // Método de ejemplo para actualizar los selectores después de agregar un item
    updateSelectOptions(type, id, name) {
        const selectElement = document.querySelector(`select[name="${type}"]`); // Asumiendo que el select tiene name="categoria", "marca", "ubicacion"
        if (selectElement) {
            const newOption = new Option(name, id, true, true); // text, value, defaultSelected, setSelected
            selectElement.add(newOption);
            // Si hay un campo de "nueva_categoria", limpiarlo y ocultarlo
            const newInputField = document.getElementById(`nueva_${type}`);
            if (newInputField) {
                newInputField.value = '';
                newInputField.style.display = 'none';
                // Si hay un botón para alternar, resetearlo si es necesario
                const toggleBtn = document.querySelector(`[data-target-input="nueva_${type}"]`);
                if (toggleBtn) {
                     toggleBtn.textContent = 'Añadir'; // O el texto original
                }
            }
        }
    }
    
    // Método de ejemplo para generar código (si lo tienes)
    generarCodigo() {
        // Implementa tu lógica para generar el código aquí
        console.log('Generando código...');
        const codigoInput = document.getElementById('codigo'); // Asumiendo ID 'codigo' para el input
        if (codigoInput) {
            codigoInput.value = 'COD-' + Math.random().toString(36).substr(2, 6).toUpperCase();
            this.showSuccess('Código generado.');
        } else {
            this.showError('Elemento de código no encontrado.');
        }
    }
    // --- FIN MÉTODOS EXISTENTES ---

    /**
     * Inicializa los event listeners para los elementos del formulario.
     * ESTE ES EL MÉTODO QUE FALTABA
     */
    initializeEventListeners() {
        console.log('FormHelpers: Inicializando event listeners...');

        // Event listener para el botón de alternar filtros (si está en FormHelpers)
        // Aunque esto se delegó al initProductManagerFeatures en index.blade.php,
        // si FormHelpers fuera el único responsable, iría aquí.
        // const toggleFiltersBtn = document.getElementById('toggleFiltersBtn');
        // if (toggleFiltersBtn) {
        //     toggleFiltersBtn.addEventListener('click', () => {
        //         const panel = document.getElementById('filtrosPanel');
        //         if (panel) {
        //             panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        //         }
        //     });
        // }

        // Event listeners para los botones de "toggle input"
        // Asumiendo que tus botones tienen un data-target-select, data-target-input, y su propio ID
        document.querySelectorAll('[data-form-helper-toggle="true"]').forEach(button => {
            button.addEventListener('click', (e) => {
                const selectId = e.target.dataset.targetSelect;
                const inputId = e.target.dataset.targetInput;
                // El ID del botón es opcional si toggleInput no lo usa
                this.toggleInput(selectId, inputId, e.target.id);
            });
        });

        // Event listener para el botón de "Generar Código"
        const generarCodigoBtn = document.getElementById('generarCodigoBtn'); // Asegúrate que este ID existe en tu HTML
        if (generarCodigoBtn) {
            generarCodigoBtn.addEventListener('click', () => {
                this.generarCodigo();
            });
        }

        // Event listeners para los botones de "Agregar Item" (Categoría, Marca, Ubicación)
        // Asumiendo IDs como 'agregarCategoriaBtn', 'agregarMarcaBtn', 'agregarUbicacionBtn'
        const addCategoryBtn = document.getElementById('agregarCategoriaBtn');
        if (addCategoryBtn) {
            addCategoryBtn.addEventListener('click', () => {
                const input = document.getElementById('nueva_categoria');
                if (input) {
                    this.agregarItem('categoria', input.value);
                }
            });
        }

        const addMarcaBtn = document.getElementById('agregarMarcaBtn');
        if (addMarcaBtn) {
            addMarcaBtn.addEventListener('click', () => {
                const input = document.getElementById('nueva_marca');
                if (input) {
                    this.agregarItem('marca', input.value);
                }
            });
        }

        const addUbicacionBtn = document.getElementById('agregarUbicacionBtn');
        if (addUbicacionBtn) {
            addUbicacionBtn.addEventListener('click', () => {
                const input = document.getElementById('nueva_ubicacion');
                if (input) {
                    this.agregarItem('ubicacion', input.value);
                }
            });
        }
        
        // ... (otros event listeners específicos de formularios que quieras manejar aquí) ...
    }

    /**
     * Limpia el módulo.
     */
    destroy() {
        console.debug('FormHelpers: Destroying module');
        // Aquí podrías remover event listeners si los registraste de forma personalizada
        // this.cleanupEventListeners();
        this.core = null;
        if (window.formHelpers === this) {
            delete window.formHelpers;
        }
    }
}

// Creación de instancia global por defecto para compatibilidad.
// Si tu main.js ya lo instancia y lo asigna a productManager.modules.formHelpers,
// podrías considerar eliminar esta línea si no necesitas la instancia global `window.formHelpers`.
window.formHelpers = new FormHelpers();

// Funciones wrapper globales para compatibilidad con código HTML existente
// que podría llamar a estas funciones directamente (ej. onclick="toggleInput(...)")
function toggleInput(selectId, inputId, buttonId) {
    // Si usas el core, podrías acceder a la instancia así:
    // window.productManager.modules.formHelpers.toggleInput(selectId, inputId, buttonId);
    // Pero si mantienes window.formHelpers, sigue siendo directo.
    window.formHelpers.toggleInput(selectId, inputId, buttonId);
}

function generarCodigo() {
    window.formHelpers.generarCodigo();
}

function agregarCategoria() {
    const input = document.getElementById('nueva_categoria');
    if (input) {
        window.formHelpers.agregarItem('categoria', input.value);
    }
}

function agregarMarca() {
    const input = document.getElementById('nueva_marca');
    if (input) {
        window.formHelpers.agregarItem('marca', input.value);
    }
}

function agregarUbicacion() {
    const input = document.getElementById('nueva_ubicacion');
    if (input) {
        window.formHelpers.agregarItem('ubicacion', input.value);
    }
}

// Inicialización automática cuando el DOM esté listo
// Esta es la llamada que disparó el error, ahora debería funcionar
document.addEventListener('DOMContentLoaded', function() {
    window.formHelpers.initializeEventListeners();
});

// Exportación para sistemas de módulos (si usas Webpack, Vite, etc.)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FormHelpers;
}

window.FormHelpers = FormHelpers;