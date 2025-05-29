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