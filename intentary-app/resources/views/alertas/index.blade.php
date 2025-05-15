@extends('layouts.app')

@section('title', 'Alertas de Stock')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Alertas de Stock</h2>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Producto</th>
                <th>Tipo de Alerta</th>
                <th>Nivel Crítico</th>
                <th>Fecha de Generación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alertas as $alerta)
                <tr>
                    <td>{{ $alerta->producto->nombre }}</td>
                    <td>{{ $alerta->tipo_alerta }}</td>
                    <td>
                        <span class="badge 
                            @if($alerta->nivel_critico == 'Alto') bg-danger 
                            @elseif($alerta->nivel_critico == 'Medio') bg-warning 
                            @else bg-info @endif">
                            {{ $alerta->nivel_critico }}
                        </span>
                    </td>
                    <td>{{ $alerta->fecha_generacion->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($alerta->resuelta)
                            <span class="badge bg-success">Resuelta</span>
                        @else
                            <span class="badge bg-secondary">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        @if(!$alerta->resuelta)
                            <form action="{{ route('alertas.resolver', $alerta->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-success">Marcar como resuelta</button>
                            </form>
                        @else
                            <small class="text-muted">
                                {{ $alerta->resuelta_en->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No hay alertas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
