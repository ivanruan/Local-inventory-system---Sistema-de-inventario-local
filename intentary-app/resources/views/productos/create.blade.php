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
                <select name="marca_id" id="marca_id" class="form-control me-2" onchange="toggleMarcaInput(this)">
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
                <select name="marca_id" id="marca_id" class="form-control me-2" onchange="toggleMarcaInput(this)">
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                    @endforeach
                    <option value="nueva">Agregar nuevo</option>
                </select>
                <input type="text" name="nueva_marca" id="nueva_marca" class="form-control me-2" placeholder="Nueva marca" style="display:none;">
                <button type="button" class="btn btn-outline-primary" id="btn_agregar_marca" style="display:none;">Agregar</button>
            </div>
        </div>

        <!-- Ubicación -->
        <div class="form-group mt-3">
            <label for="ubicacion_id">Ubicación</label>
            <div class="d-flex">
                <select name="ubicacion_id" id="ubicacion_id" class="form-control me-2">
                    @foreach($ubicaciones as $ubicacion)
                        <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                    @endforeach
                </select>
                <input type="text" id="nueva_ubicacion" class="form-control me-2" placeholder="Nueva ubicación">
                <button type="button" class="btn btn-outline-primary" onclick="agregarUbicacion()">Agregar</button>
            </div>
        </div>

        <!-- Otros campos -->
        <div class="row mt-3">
            <div class="col-md-4">
                <label for="unidad">Unidad</label>
                <input type="text" name="unidad" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="nivel">Nivel</label>
                <input type="number" name="nivel" class="form-control" required>
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
    // Función para agregar nueva marca
    function agregarMarca() {
        var nombreMarca = document.getElementById('nueva_marca').value.trim();
        
        if (!nombreMarca) {
            alert("Por favor ingrese un nombre para la nueva marca");
            return;
        }
        
        // Aquí iría tu llamada AJAX para guardar la marca
        console.log("Marca a agregar:", nombreMarca);
        // fetch(...) tu código AJAX aquí
    }

    function agregarCategoria() {
        const nombre = document.getElementById('nueva_categoria').value;
        if (!nombre) return;

        fetch('{{ route("categorias.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nombre })
        })
        .then(res => res.json())
        .then(data => {
            let select = document.getElementById('categoria_id');
            let option = new Option(data.nombre, data.id, true, true);
            select.append(option);
            document.getElementById('nueva_categoria').value = '';
        });
    }

    function agregarUbicacion() {
        const nombre = document.getElementById('nueva_ubicacion').value;
        if (!nombre) return;

        fetch('{{ route("ubicaciones.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nombre })
        })
        .then(res => res.json())
        .then(data => {
            let select = document.getElementById('ubicacion_id');
            let option = new Option(data.nombre, data.id, true, true);
            select.append(option);
            document.getElementById('nueva_ubicacion').value = '';
        });
    }
    
    function toggleMarcaInput(select) {
        console.log("Valor seleccionado:", select.value); // Para depuración
        
        // Obtener elementos de forma segura
        var marcaInput = document.getElementById('nueva_marca');
        var agregarBtn = document.getElementById('btn_agregar_marca');
        
        // Verificar que los elementos existen
        if (!marcaInput || !agregarBtn) {
            console.error("Error: No se encontraron los elementos del formulario");
            return;
        }
        
        // Mostrar/ocultar según la selección
        if (select.value === "nueva") {
            marcaInput.style.display = "block";
            agregarBtn.style.display = "block";
        } else {
            marcaInput.style.display = "none";
            agregarBtn.style.display = "none";
        }
    }
</script>
@endsection

