@extends('layouts.app')

@section('title', 'Mantenimientos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Mantenimientos</h2>
        <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">Nuevo Mantenimiento</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Fecha programada</th>
                <th>Responsable</th>
                <th>Estado</th>
                <th>Costo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mantenimientos as $m)
                <tr>
                    <td>{{ $m->producto->nombre }}</td>
                    <td>{{ ucfirst($m->tipo) }}</td>
                    <td>{{ $m->fecha_programada->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->responsable }}</td>
                    <td>
                        <span class="badge 
                            @if ($m->status == 'pendiente') bg-warning
                            @elseif ($m->status == 'completado') bg-success
                            @else bg-secondary @endif">
                            {{ ucfirst($m->status) }}
                        </span>
                    </td>
                    <td>${{ number_format($m->costo, 2) }}</td>
                    <td>
                        <a href="{{ route('mantenimientos.show', $m) }}" class="btn btn-sm btn-outline-info">Ver</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
