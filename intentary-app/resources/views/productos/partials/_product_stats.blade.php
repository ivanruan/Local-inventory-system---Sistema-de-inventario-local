{{-- resources/views/productos/partials/_product_stats.blade.php --}}
<div class="row mb-3">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body p-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-box-seam fs-4 me-2"></i>
                    <div>
                        {{-- total() lo llamas sobre $productos que es un Paginator, lo cual es correcto --}}
                        <div class="fs-6 fw-bold">{{ $productos->total() ?? 0 }}</div> 
                        <div class="small">Total Productos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white">
            <div class="card-body p-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle fs-4 me-2"></i>
                    <div>
                        <div class="fs-6 fw-bold">{{ $stockBajo ?? 0 }}</div>
                        <div class="small">Stock Bajo</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body p-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-x-circle fs-4 me-2"></i>
                    <div>
                        <div class="fs-6 fw-bold">{{ $fueraStock ?? 0 }}</div>
                        <div class="small">Fuera de Stock</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body p-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-currency-dollar fs-4 me-2"></i>
                    <div>
                        <div class="fs-6 fw-bold">${{ number_format($valorTotal ?? 0, 2) }}</div>
                        <div class="small">Valor Total</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white">
            <div class="card-body p-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-arrow-up-down fs-4 me-2"></i>
                    <div>
                        <div class="fs-6 fw-bold">{{ $sobreStock ?? 0 }}</div>
                        <div class="small">Sobre Stock</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>