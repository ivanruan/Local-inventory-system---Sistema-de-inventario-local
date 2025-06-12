@extends('layouts.app')

@section('title', 'Editar Mantenimiento')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-lg">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-lg">
                        <h4 class="mb-0">Editar Mantenimiento #{{ $mantenimiento->id }}</h4>
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-light btn-sm">Volver a Mantenimientos</a>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('mantenimientos.update', $mantenimiento) }}" method="POST">
                            @csrf
                            @method('PUT') {{-- Importante para indicar que es una petición PUT/PATCH --}}

                            {{-- Campo Producto --}}
                            <div class="mb-3">
                                <label for="producto_id" class="form-label fw-bold">Producto:</label>
                                <select class="form-select form-select-lg rounded-pill @error('producto_id') is-invalid @enderror" id="producto_id" name="producto_id" required>
                                    <option value="" disabled>Selecciona un producto</option>
                                    @foreach ($productos as $producto)
                                        {{-- El valor 'old' tiene prioridad, si no, se usa el valor actual del mantenimiento --}}
                                        <option value="{{ $producto->id }}" {{ old('producto_id', $mantenimiento->producto_id) == $producto->id ? 'selected' : '' }}>
                                            {{ $producto->nombre }} ({{ $producto->codigo }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('producto_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo Fecha Programada --}}
                            <div class="mb-3">
                                <label for="fecha_programada" class="form-label fw-bold">Fecha Programada:</label>
                                <input type="datetime-local" class="form-control form-control-lg rounded-pill @error('fecha_programada') is-invalid @enderror" id="fecha_programada" name="fecha_programada" value="{{ old('fecha_programada', $mantenimiento->fecha_programada->format('Y-m-d\TH:i')) }}" required>
                                @error('fecha_programada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo Fecha Ejecución (opcional) --}}
                            <div class="mb-3">
                                <label for="fecha_ejecucion" class="form-label fw-bold">Fecha de Ejecución (Opcional):</label>
                                <input type="datetime-local" class="form-control form-control-lg rounded-pill @error('fecha_ejecucion') is-invalid @enderror" id="fecha_ejecucion" name="fecha_ejecucion" value="{{ old('fecha_ejecucion', $mantenimiento->fecha_ejecucion ? $mantenimiento->fecha_ejecucion->format('Y-m-d\TH:i') : '') }}">
                                @error('fecha_ejecucion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo Tipo --}}
                            <div class="mb-3">
                                <label for="tipo" class="form-label fw-bold">Tipo de Mantenimiento:</label>
                                <select class="form-select form-select-lg rounded-pill @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                                    <option value="" disabled>Selecciona un tipo</option>
                                    {{-- Asegúrate de que estas opciones coincidan con las reglas de validación en Store/UpdateMantenimientoRequest --}}
                                    <option value="preventivo" {{ old('tipo', $mantenimiento->tipo) == 'preventivo' ? 'selected' : '' }}>Preventivo</option>
                                    <option value="correctivo" {{ old('tipo', $mantenimiento->tipo) == 'correctivo' ? 'selected' : '' }}>Correctivo</option>
                                    <option value="predictivo" {{ old('tipo', $mantenimiento->tipo) == 'predictivo' ? 'selected' : '' }}>Predictivo</option>
                                    <option value="rutinario" {{ old('tipo', $mantenimiento->tipo) == 'rutinario' ? 'selected' : '' }}>Rutinario</option>
                                    <option value="emergencia" {{ old('tipo', $mantenimiento->tipo) == 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo Descripción --}}
                            <div class="mb-3">
                                <label for="descripcion" class="form-label fw-bold">Descripción:</label>
                                <textarea class="form-control form-control-lg rounded @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" placeholder="Detalles del mantenimiento">{{ old('descripcion', $mantenimiento->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo Responsable --}}
                            <div class="mb-3">
                                <label for="responsable" class="form-label fw-bold">Responsable:</label>
                                <input type="text" class="form-control form-control-lg rounded-pill @error('responsable') is-invalid @enderror" id="responsable" name="responsable" value="{{ old('responsable', $mantenimiento->responsable) }}" placeholder="Nombre del responsable" required>
                                @error('responsable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo Status --}}
                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">Estado:</label>
                                <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="pendiente" {{ old('status', $mantenimiento->status) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="completado" {{ old('status', $mantenimiento->status) == 'completado' ? 'selected' : '' }}>Completado</option>
                                    <option value="cancelado" {{ old('status', $mantenimiento->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo Costo --}}
                            <div class="mb-3">
                                <label for="costo" class="form-label fw-bold">Costo (Opcional):</label>
                                <input type="number" step="0.01" class="form-control form-control-lg rounded-pill @error('costo') is-invalid @enderror" id="costo" name="costo" value="{{ old('costo', $mantenimiento->costo) }}" placeholder="Ej: 150.75">
                                @error('costo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo Observaciones --}}
                            <div class="mb-3">
                                <label for="observaciones" class="form-label fw-bold">Observaciones (Opcional):</label>
                                <textarea class="form-control form-control-lg rounded @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" rows="3" placeholder="Notas adicionales">{{ old('observaciones', $mantenimiento->observaciones) }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 me-md-2"
                                        onclick="return confirm('¿Estás seguro de que deseas actualizar este mantenimiento? Esta acción no se puede deshacer.')">
                                    <i class="fas fa-save me-2"></i> Actualizar Mantenimiento
                                </button>
                                <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary btn-lg rounded-pill px-4">
                                    <i class="fas fa-times-circle me-2"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endpush
