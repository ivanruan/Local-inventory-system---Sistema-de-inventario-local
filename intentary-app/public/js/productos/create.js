// Product Form JavaScript - public/js/product-form.js

class ProductFormManager {
    constructor() {
        this.init();
    }

    init() {
        this.addCSRFToken();
        this.bindEvents();
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
        // Bind Enter key events
        document.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.handleEnterKey(e);
            }
        });
    }

    // Handle Enter key presses
    handleEnterKey(e) {
        if (e.target.matches('#nueva_marca, #nueva_categoria, #nueva_ubicacion')) {
            e.preventDefault();
            const inputId = e.target.id;
            const tipo = inputId.replace('nueva_', '');
            
            if (tipo === 'marca') this.agregarMarca();
            else if (tipo === 'categoria') this.agregarCategoria();
            else if (tipo === 'ubicacion') this.agregarUbicacion();
        }
    }

    // Toggle input visibility based on select value
    toggleInput(selectId, inputId, buttonId) {
        const select = document.getElementById(selectId);
        const inputOrContainer = document.getElementById(inputId);
        const button = document.getElementById(buttonId);

        if (!inputOrContainer || !button) return;

        if (select.value === 'nueva') {
            inputOrContainer.style.display = 'flex';
            button.style.display = 'block';
            
            // Focus on the input field
            inputOrContainer.focus();
        } else {
            inputOrContainer.style.display = 'none';
            button.style.display = 'none';
            
            // Clear input values
            inputOrContainer.value = '';
        }
    }

    // Add new marca
    agregarMarca() {
        this.agregarElemento('marca');
    }

    // Add new categoria
    agregarCategoria() {
        this.agregarElemento('categoria');
    }

    // Add new ubicacion
    agregarUbicacion() {
        const input = document.getElementById('nueva_ubicacion');
        const valor = input.value.trim();

        if (!valor) {
            alert('Por favor ingresa el código y nivel de la ubicación (ej: A1 - 2).');
            input.focus();
            return;
        }

        // Parse the input to extract codigo and nivel
        // Expected format: "A1 - 2" or "A1-2" or similar
        const parts = valor.split(/\s*-\s*/);
        if (parts.length !== 2) {
            alert('Por favor usa el formato: Código - Nivel (ej: A1 - 2)');
            input.focus();
            return;
        }

        const codigo = parts[0].trim();
        const nivel = parseInt(parts[1].trim());

        if (!codigo || isNaN(nivel)) {
            alert('Por favor verifica el formato: Código - Nivel (ej: A1 - 2)');
            input.focus();
            return;
        }

        // Show loading state
        const button = document.getElementById('btn_agregar_ubicacion');
        const originalText = button.textContent;
        button.textContent = 'Agregando...';
        button.disabled = true;

        fetch('/ubicaciones', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json"
            },
            body: JSON.stringify({ 
                codigo: codigo,
                nivel: nivel
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            // Create display text from the data
            const displayText = `${data.codigo} - Nivel ${data.nivel}`;
            const itemId = data.id;
            
            // Add new option to select
            this.addOptionToSelect('ubicacion_id', displayText, itemId);
            
            // Hide input and button
            input.style.display = 'none';
            button.style.display = 'none';
            input.value = '';

            alert(`Ubicación "${displayText}" agregada exitosamente.`);
        })
        .catch(error => {
            console.error('Error:', error);
            this.handleError(error, 'ubicación');
        })
        .finally(() => {
            // Reset button state
            button.textContent = originalText;
            button.disabled = false;
        });
    }

    // Generic function to add elements (marca, categoria)
    agregarElemento(tipo) {
        const input = document.getElementById(`nueva_${tipo}`);
        const valor = input.value.trim();

        if (!valor) {
            alert(`Por favor ingresa un nombre para la ${tipo}.`);
            input.focus();
            return;
        }

        // Show loading state
        const button = document.getElementById(`btn_agregar_${tipo}`);
        const originalText = button.textContent;
        button.textContent = 'Agregando...';
        button.disabled = true;

        // Determine the correct route
        const routes = {
            'marca': '/marcas',
            'categoria': '/categorias',
            'ubicacion': '/ubicaciones'
        };

        fetch(routes[tipo], {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json"
            },
            body: JSON.stringify({ nombre: valor })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            // Add new option to select
            this.addOptionToSelect(`${tipo}_id`, data.nombre, data.id);
            
            // Hide input and button
            input.style.display = 'none';
            button.style.display = 'none';
            input.value = '';

            alert(`${tipo.charAt(0).toUpperCase() + tipo.slice(1)} "${data.nombre}" agregada exitosamente.`);
        })
        .catch(error => {
            console.error('Error:', error);
            this.handleError(error, tipo);
        })
        .finally(() => {
            // Reset button state
            button.textContent = originalText;
            button.disabled = false;
        });
    }

    // Helper function to add option to select
    addOptionToSelect(selectId, text, value) {
        const select = document.getElementById(selectId);
        
        // Remove the "Agregar nuevo" option temporarily
        const addNewOption = select.querySelector('option[value="nueva"]');
        addNewOption.remove();
        
        // Add the new option
        const newOption = new Option(text, value, true, true);
        select.add(newOption);
        
        // Re-add the "Agregar nuevo" option at the end
        select.add(addNewOption);
    }

    // Helper function to handle errors
    handleError(error, tipo) {
        let errorMessage = `Error al agregar la ${tipo}.`;
        
        if (error.errors) {
            errorMessage += ' ' + Object.values(error.errors).flat().join(' ');
        } else if (error.message) {
            errorMessage += ' ' + error.message;
        }
        
        alert(errorMessage);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.productFormManager = new ProductFormManager();
});

// Global functions for onclick handlers (backward compatibility)
function toggleInput(selectId, inputId, buttonId) {
    window.productFormManager.toggleInput(selectId, inputId, buttonId);
}

function agregarMarca() {
    window.productFormManager.agregarMarca();
}

function agregarCategoria() {
    window.productFormManager.agregarCategoria();
}

function agregarUbicacion() {
    window.productFormManager.agregarUbicacion();
}