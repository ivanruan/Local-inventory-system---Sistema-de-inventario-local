@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Producto</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <label for="codigo">Código</label>
                <input type="text" name="codigo" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="especificacion">Especificación</label>
            <textarea name="especificacion" class="form-control"></textarea>
        </div>

        <!-- Marca -->
        <div class="form-group mt-3">
            <label for="marca_id">Marca</label>
            <div class="d-flex">
                <select name="marca_id" id="marca_id" class="form-control me-2" onchange="toggleInput('marca_id', 'nueva_marca', 'btn_agregar_marca')">
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                    @endforeach
                    <option value="nueva">Agregar nuevo</option>
                </select>
                <input type="text" name="nueva_marca" id="nueva_marca" class="form-control me-2" placeholder="Nueva marca" style="display:none;">
                <button type="button" class="btn btn-outline-primary" id="btn_agregar_marca" style="display:none;" onclick="agregarMarca()">Agregar</button>
            </div>
        </div>

        <!-- Categoría -->
        <div class="form-group mt-3">
            <label for="categoria_id">Categoría</label>
            <div class="d-flex">
                <select name="categoria_id" id="categoria_id" class="form-control me-2" onchange="toggleInput('categoria_id', 'nueva_categoria', 'btn_agregar_categoria')">
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                    <option value="nueva">Agregar nuevo</option>
                </select>
                <input type="text" name="nueva_categoria" id="nueva_categoria" class="form-control me-2" placeholder="Nueva categoría" style="display:none;">
                <button type="button" class="btn btn-outline-primary" id="btn_agregar_categoria" style="display:none;" onclick="agregarCategoria()">Agregar</button>
            </div>
        </div>

        <!-- Ubicación -->
        <div class="form-group mt-3">
            <label for="ubicacion_id">Ubicación</label>
            <div class="d-flex">
                <select name="ubicacion_id" id="ubicacion_id" class="form-control me-2" onchange="toggleInput('ubicacion_id', 'nueva_ubicacion_container', 'btn_agregar_ubicacion')">
                    @foreach($ubicaciones as $ubicacion)
                        <option value="{{ $ubicacion->id }}">{{ $ubicacion->codigo }}</option>
                    @endforeach
                    <option value="nueva">Agregar nuevo</option>
                </select>
                <div id="nueva_ubicacion_container" style="display:none;" class="d-flex gap-2 flex-grow-1">
                    <input type="text" id="nueva_ubicacion_codigo" class="form-control" placeholder="Código" required>
                    <input type="number" id="nueva_ubicacion_nivel" class="form-control" placeholder="Nivel" required>
                </div>
                <button type="button" class="btn btn-outline-primary" id="btn_agregar_ubicacion" style="display:none;" onclick="agregarUbicacion()">Agregar</button>
            </div>
        </div>

        <!-- Otros campos -->
        <div class="row mt-3">        
            <div class="col-md-4">
                <label for="unidad">Unidad</label>
                <input type="text" name="unidad" class="form-control" required>
            </div>
            
            <div class="col-md-4">
                <label for="stock_actual">Stock actual</label>
                <input type="number" name="stock_actual" class="form-control" required>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <label for="stock_minimo">Stock mínimo</label>
                <input type="number" name="stock_minimo" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="stock_maximo">Stock máximo</label>
                <input type="number" name="stock_maximo" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="stock_seguridad">Stock seguridad</label>
                <input type="number" name="stock_seguridad" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="duracion_inventario">Duración Inventario</label>
                <input type="number" name="duracion_inventario" class="form-control">
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="costo">Costo</label>
            <input type="number" name="costo" step="0.01" class="form-control">
        </div>

        <div class="form-group mt-3">
            <label for="vida_util">Vida útil</label>
            <input type="number" name="vida_util" class="form-control">
        </div>

        <div class="form-group mt-3">
            <label for="status">Estado</label>
            <select name="status" class="form-control">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success mt-4">Guardar Producto</button>
    </form>
</div>

<!-- JS para agregar nuevas opciones -->
<script>
    // Add CSRF token to meta tag if not already present
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }

    function toggleInput(selectId, inputId, buttonId) {
        const select = document.getElementById(selectId);
        const inputOrContainer = document.getElementById(inputId);
        const button = document.getElementById(buttonId);

        if (!inputOrContainer || !button) return;

        if (select.value === 'nueva') {
            inputOrContainer.style.display = 'block';
            button.style.display = 'block';
            
            // Focus on the first input field
            if (selectId === 'ubicacion_id') {
                document.getElementById('nueva_ubicacion_codigo').focus();
            } else {
                inputOrContainer.focus();
            }
        } else {
            inputOrContainer.style.display = 'none';
            button.style.display = 'none';
            
            // Clear input values
            if (selectId === 'ubicacion_id') {
                document.getElementById('nueva_ubicacion_codigo').value = '';
                document.getElementById('nueva_ubicacion_nivel').value = '';
            } else {
                inputOrContainer.value = '';
            }
        }
    }

    // Specific functions for each type
    function agregarMarca() {
        agregarElemento('marca');
    }

    function agregarCategoria() {
        agregarElemento('categoria');
    }

    function agregarUbicacion() {
        const codigo = document.getElementById('nueva_ubicacion_codigo').value.trim();
        const nivel = document.getElementById('nueva_ubicacion_nivel').value.trim();

        if (!codigo || !nivel) {
            alert('Por favor completa todos los campos: código y nivel.');
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
                nivel: parseInt(nivel)
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug line
            
            // Create display text from the data
            const displayText = `${data.codigo} - Nivel ${data.nivel}`;
            const itemId = data.id;
            
            // Add new option to select
            const select = document.getElementById('ubicacion_id');
            
            // Remove the "Agregar nuevo" option temporarily
            const addNewOption = select.querySelector('option[value="nueva"]');
            addNewOption.remove();
            
            // Add the new option
            const newOption = new Option(displayText, itemId, true, true);
            select.add(newOption);
            
            // Re-add the "Agregar nuevo" option at the end
            select.add(addNewOption);
            
            // Hide inputs and button
            document.getElementById('nueva_ubicacion_container').style.display = 'none';
            button.style.display = 'none';
            
            // Clear input values
            document.getElementById('nueva_ubicacion_codigo').value = '';
            document.getElementById('nueva_ubicacion_nivel').value = '';

            alert(`Ubicación "${displayText}" agregada exitosamente.`);
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'Error al agregar la ubicación.';
            
            if (error.errors) {
                errorMessage += ' ' + Object.values(error.errors).flat().join(' ');
            } else if (error.message) {
                errorMessage += ' ' + error.message;
            }
            
            alert(errorMessage);
        })
        .finally(() => {
            // Reset button state
            button.textContent = originalText;
            button.disabled = false;
        });
    }

    function agregarElemento(tipo) {
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
        let route;
        switch(tipo) {
            case 'marca':
                route = '/marcas';
                break;
            case 'categoria':
                route = '/categorias';
                break;
            case 'ubicacion':
                route = '/ubicaciones';
                break;
        }

        fetch(route, {
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
            const select = document.getElementById(`${tipo}_id`);
            
            // Remove the "Agregar nuevo" option temporarily
            const addNewOption = select.querySelector('option[value="nueva"]');
            addNewOption.remove();
            
            // Add the new option
            const newOption = new Option(data.nombre, data.id, true, true);
            select.add(newOption);
            
            // Re-add the "Agregar nuevo" option at the end
            select.add(addNewOption);
            
            // Hide input and button
            input.style.display = 'none';
            button.style.display = 'none';
            input.value = '';

            alert(`${tipo.charAt(0).toUpperCase() + tipo.slice(1)} "${data.nombre}" agregada exitosamente.`);
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'Error al agregar la ' + tipo + '.';
            
            if (error.errors) {
                errorMessage += ' ' + Object.values(error.errors).flat().join(' ');
            } else if (error.message) {
                errorMessage += ' ' + error.message;
            }
            
            alert(errorMessage);
        })
        .finally(() => {
            // Reset button state
            button.textContent = originalText;
            button.disabled = false;
        });
    }

    // Allow Enter key to trigger the add function
    document.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            if (e.target.matches('#nueva_marca, #nueva_categoria')) {
                e.preventDefault();
                const inputId = e.target.id;
                const tipo = inputId.replace('nueva_', '');
                
                if (tipo === 'marca') agregarMarca();
                else if (tipo === 'categoria') agregarCategoria();
            } else if (e.target.matches('#nueva_ubicacion_nombre, #nueva_ubicacion_codigo, #nueva_ubicacion_nivel')) {
                e.preventDefault();
                agregarUbicacion();
            }
        }
    });
</script>
@endsection