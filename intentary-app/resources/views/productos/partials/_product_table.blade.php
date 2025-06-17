{{-- resources/views/productos/partials/_product_table.blade.php --}}

@if($productos->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-info-circle"></i> No hay productos registrados que coincidan con los criterios de búsqueda.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle table-sm product-table table-striped">
            <thead class="table-dark sticky-top">
                <tr>
                    <th style="min-width: 40px;">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    <th style="min-width: 80px;">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                           class="text-white text-decoration-none">
                            Fecha <i class="bi bi-arrow-down-up"></i>
                        </a>
                    </th>
                    <th style="min-width: 100px;">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'codigo', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                           class="text-white text-decoration-none">
                            Código <i class="bi bi-arrow-down-up"></i>
                        </a>
                    </th>
                    <th style="min-width: 150px;">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nombre', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                           class="text-white text-decoration-none">
                            Producto <i class="bi bi-arrow-down-up"></i>
                        </a>
                    </th>
                    <th style="min-width: 120px;">Especificación</th>
                    <th style="min-width: 80px;">Marca</th>
                    <th style="min-width: 80px;">Categoría</th>
                    <th style="min-width: 80px;">Ubicación</th>
                    <th style="min-width: 60px;">Nivel</th>
                    <th style="min-width: 60px;">Unidad</th>
                    <th style="min-width: 80px;">Stock Inicial</th>
                    <th style="min-width: 70px;">Entradas</th>
                    <th style="min-width: 70px;">Salidas</th>
                    <th style="min-width: 80px;">Stock Mín.</th>
                    <th style="min-width: 80px;">Stock Actual</th>
                    <th style="min-width: 80px;">Stock Máx.</th>
                    <th style="min-width: 80px;">Stock Seg.</th>
                    <th style="min-width: 80px;">Duración Inv.</th>
                    <th style="min-width: 100px;">Status</th>
                    <th style="min-width: 100px;">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'costo', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                           class="text-white text-decoration-none">
                            Valor Unitario <i class="bi bi-arrow-down-up"></i>
                        </a>
                    </th>
                    <th style="min-width: 120px;">Proveedor</th>
                    <th style="min-width: 150px;">Observaciones</th>
                    <th style="min-width: 120px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                    <tr class="{{ $producto->es_fuera_de_stock ? 'table-danger' : ($producto->es_stock_bajo ? 'table-warning' : ($producto->es_sobre_stock ? 'table-info' : '')) }}"
                        data-id="{{ $producto->id }}"
                        data-stock="{{ $producto->stock_actual }}"
                        data-min-stock="{{ $producto->stock_minimo }}">
                        <td>
                            <input type="checkbox" class="form-check-input row-select" value="{{ $producto->id }}">
                        </td>
                        <td class="text-nowrap">
                            {{ $producto->created_at ? $producto->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-nowrap">
                            {{ $producto->codigo }}
                        </td>
                        <td>
                            {{ $producto->nombre }}
                        </td>
                        <td>
                            {{ Str::limit($producto->especificacion, 30) }}
                        </td>
                        <td>
                            {{ $producto->marca->nombre ?? '-' }}
                        </td>
                        <td>
                            {{ $producto->categoria->nombre ?? '-' }}
                        </td>
                        <td>
                            {{ $producto->ubicacion->codigo ?? '-' }}
                        </td>
                        <td class="text-center">
                            {{ $producto->nivel }}
                        </td>
                        <td class="text-center">
                            {{ $producto->unidad }}
                        </td>
                        <td class="text-center">
                            {{ number_format($producto->stock_inicial) }}
                        </td>
                        <td class="text-center">
                            {{ number_format($producto->total_entradas) }}
                        </td>
                        <td class="text-center">
                            {{ number_format($producto->total_salidas) }}
                        </td>
                        <td class="text-center">
                            {{ $producto->stock_minimo ? number_format($producto->stock_minimo) : '-' }}
                        </td>
                        <td class="text-center stock-actual">
                            {{ number_format($producto->stock_actual) }}
                        </td>
                        <td class="text-center">
                            {{ $producto->stock_maximo ? number_format($producto->stock_maximo) : '-' }}
                        </td>
                        <td class="text-center">
                            {{ $producto->stock_seguridad ? number_format($producto->stock_seguridad) : '-' }}
                        </td>
                        <td class="text-center">
                            {{ $producto->duracion_inventario ? $producto->duracion_inventario . ' días' : '-' }}
                        </td>
                        <td>
                            {{ $producto->status }}
                        </td>
                        <td class="text-end">
                            ${{ number_format($producto->valor_unitario, 2) }}
                        </td>
                        <td>
                            {{ $producto->proveedor->nombre ?? '-' }}
                        </td>
                        <td>
                            {{ $producto->observaciones ? Str::limit($producto->observaciones, 40) : '-' }}
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('productos.show', $producto) }}" 
                                   class="btn btn-outline-info" title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('productos.edit', $producto) }}" 
                                   class="btn btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button class="btn btn-outline-secondary view-movements" 
                                        data-product-id="{{ $producto->id }}" 
                                        title="Ver movimientos">
                                    <i class="bi bi-arrow-left-right"></i>
                                </button>
                                <form action="{{ route('productos.destroy', $producto) }}" 
                                      method="POST" class="d-inline" 
                                      onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Información de paginación --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} 
            de {{ $productos->total() }} resultados
        </div>
        <div>
            {{ $productos->appends(request()->query())->links() }}
        </div>
    </div>
@endif