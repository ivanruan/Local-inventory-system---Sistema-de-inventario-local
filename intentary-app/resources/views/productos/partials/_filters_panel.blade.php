{{-- Panel de Filtros --}}
    <div id="filtrosPanel" class="card mb-3" style="display: none;">
        <div class="card-body">
            <form method="GET" action="{{ route('productos.index') }}" class="row g-3" id="filtersForm">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" placeholder="Código, nombre, especificación...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas</option>
                        @foreach($categorias ?? [] as $categoria)
                            <option value="{{ $categoria->id }}" 
                                {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Marca</label>
                    <select name="marca" class="form-select">
                        <option value="">Todas</option>
                        @foreach($marcas ?? [] as $marca)
                            <option value="{{ $marca->id }}" 
                                {{ request('marca') == $marca->id ? 'selected' : '' }}>
                                {{ $marca->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Producto::getStatusOptions() as $status)
                            <option value="{{ $status }}" 
                                {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Stock</label>
                    <select name="stock_filter" class="form-select">
                        <option value="">Todos</option>
                        <option value="bajo" {{ request('stock_filter') == 'bajo' ? 'selected' : '' }}>Stock Bajo</option>
                        <option value="fuera" {{ request('stock_filter') == 'fuera' ? 'selected' : '' }}>Fuera de Stock</option>
                        <option value="sobre" {{ request('stock_filter') == 'sobre' ? 'selected' : '' }}>Sobre Stock</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
