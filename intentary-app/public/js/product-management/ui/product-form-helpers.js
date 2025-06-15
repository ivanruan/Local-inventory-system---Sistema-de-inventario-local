/**
 * Product Form Helpers Module
 * Maneja toda la funcionalidad relacionada con formularios dinámicos
 * de productos, categorías, marcas y ubicaciones.
 */

class FormHelpers {
    constructor(core = null) { // Aceptar el core, hacerlo opcional para compatibilidad
        this.core = core; // Guardar referencia al core
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
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    // ... (El resto de tus métodos de FormHelpers permanecen igual) ...

    /**
     * Realiza una petición a la API (modificado para usar core.makeRequest si está disponible)
     */
    async makeApiRequest(url, body) {
        if (this.core && typeof this.core.makeRequest === 'function') {
            // Si el core tiene un método makeRequest, úsalo para centralizar las peticiones
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
            // Fallback si no hay core o no tiene makeRequest
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
            alert(message); // Fallback básico
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
    
    // ... (Resto de métodos y el initializaEventListeners, etc.) ...

    // Añadir método destroy si lo vas a registrar con el core
    destroy() {
        console.debug('FormHelpers: Destroying module');
        // Aquí podrías limpiar event listeners si los registraste de forma personalizada
        // this.cleanupEventListeners(); // Asumiendo que tienes un método así
        this.core = null;
        // Eliminar la referencia global si fue creada por el módulo
        if (window.formHelpers === this) {
            delete window.formHelpers;
        }
    }
}

// Mantener funciones wrapper globales para compatibilidad existente
// Asegúrate de que apunten a la instancia correcta si FormHelpers
// se va a usar de ambas maneras (via core o globalmente)
// Si solo se usa via core, estas funciones globales podrían ser eliminadas
// o adaptadas para obtener la instancia del core si es necesario.
// Por ahora, las dejo apuntando a la instancia global que se creará si no se pasa el core.
window.formHelpers = new FormHelpers(); // Instancia global por defecto

function toggleInput(selectId, inputId, buttonId) {
    window.formHelpers.toggleInput(selectId, inputId, buttonId);
}

function generarCodigo() {
    window.formHelpers.generarCodigo();
}

function agregarCategoria() {
    const input = document.getElementById('nueva_categoria');
    if (input) {
        // Asegúrate de usar la instancia global o la del core si existe
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
document.addEventListener('DOMContentLoaded', function() {
    // Si FormHelpers es instanciado por main.js, esta línea no es necesaria
    // porque main.js se encarga de la inicialización al registrar el módulo.
    // Sin embargo, si se mantiene la instancia global, es importante que se inicialice.
    window.formHelpers.initializeEventListeners();
});

// Export para sistemas de módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FormHelpers;
}