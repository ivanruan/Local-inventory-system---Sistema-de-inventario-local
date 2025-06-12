@extends('layouts.app')

@section('title', 'Detalles del Mantenimiento')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-lg">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center rounded-top-lg">
                        <h4 class="mb-0">Detalles del Mantenimiento #{{ $mantenimiento->id }}</h4>
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-light btn-sm">Volver a Mantenimientos</a>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Producto:</p>
                                <p class="ms-3">{{ $mantenimiento->producto->nombre }} ({{ $mantenimiento->producto->codigo }})</p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Tipo:</p>
                                <p class="ms-3">{{ ucfirst($mantenimiento->tipo) }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Fecha Programada:</p>
                                <p class="ms-3">{{ $mantenimiento->fecha_programada->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Fecha de Ejecución:</p>
                                <p class="ms-3">
                                    @if ($mantenimiento->fecha_ejecucion)
                                        {{ $mantenimiento->fecha_ejecucion->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">No ejecutado aún</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Responsable:</p>
                                <p class="ms-3">{{ $mantenimiento->responsable }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Estado:</p>
                                <p class="ms-3">
                                    <span class="badge rounded-pill px-3 py-2
                                        @if ($mantenimiento->status == 'pendiente') bg-warning text-dark
                                        @elseif ($mantenimiento->status == 'completado') bg-success
                                        @elseif ($mantenimiento->status == 'cancelado') bg-danger
                                        @else bg-secondary @endif">
                                        {{ ucfirst($mantenimiento->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="fw-bold mb-1">Costo:</p>
                            <p class="ms-3">
                                @if ($mantenimiento->costo)
                                    ${{ number_format($mantenimiento->costo, 2) }}
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <p class="fw-bold mb-1">Descripción:</p>
                            <p class="ms-3 text-break">{{ $mantenimiento->descripcion ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="fw-bold mb-1">Observaciones:</p>
                            <p class="ms-3 text-break">{{ $mantenimiento->observaciones ?? 'N/A' }}</p>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            {{-- Botones de acción, puedes añadir aquí Editar y Eliminar --}}
                            {{-- <a href="{{ route('mantenimientos.edit', $mantenimiento) }}" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-edit me-2"></i> Editar
                            </a>
                            <form action="{{ route('mantenimientos.destroy', $mantenimiento) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este mantenimiento? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger rounded-pill px-4">
                                    <i class="fas fa-trash-alt me-2"></i> Eliminar
                                </button>
                            </form> --}}
                            <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left me-2"></i> Volver al Listado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endpush