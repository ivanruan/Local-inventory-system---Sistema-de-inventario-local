@extends('layouts.app')

@section('title', 'Detalles de Alerta de Stock')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-lg">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center rounded-top-lg">
                        <h4 class="mb-0">Detalles de Alerta #{{ $alertaStock->id }}</h4>
                        <a href="{{ route('alertas.index') }}" class="btn btn-light btn-sm">Volver a Alertas</a>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Producto:</p>
                                <p class="ms-3">{{ $alertaStock->producto->nombre }} ({{ $alertaStock->producto->codigo }})</p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Tipo de Alerta:</p>
                                <p class="ms-3">{{ ucfirst(str_replace('_', ' ', $alertaStock->tipo_alerta)) }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Nivel Crítico:</p>
                                <p class="ms-3">{{ $alertaStock->nivel_critico ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Estado:</p>
                                <p class="ms-3">
                                    <span class="badge rounded-pill px-3 py-2
                                        @if (!$alertaStock->resuelta) bg-danger
                                        @else bg-success @endif">
                                        {{ $alertaStock->resuelta ? 'Resuelta' : 'Activa' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Fecha de Generación:</p>
                                <p class="ms-3">{{ $alertaStock->fecha_generacion->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold mb-1">Fecha de Resolución:</p>
                                <p class="ms-3">
                                    @if ($alertaStock->resuelta_en)
                                        {{ $alertaStock->resuelta_en->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Pendiente de resolución</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            @if (!$alertaStock->resuelta) {{-- Solo mostrar el botón Resolver si la alerta NO está resuelta --}}
                                <form action="{{ route('alertas.resolver', $alertaStock) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success rounded-pill px-4"
                                            onclick="return confirm('¿Estás seguro de marcar esta alerta como resuelta?')">
                                        <i class="fas fa-check-circle me-2"></i> Marcar como Resuelta
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('alertas.index') }}" class="btn btn-primary rounded-pill px-4">
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
