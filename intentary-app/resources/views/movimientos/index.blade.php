@extends('layouts.app')

@section('title', 'Movimientos de Inventario')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Movimientos</h2>
        <a href="{{ route('movimientos.create') }}" class="btn btn-success">Nuevo Movimiento</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Responsable</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movimientos as $mov)
                <tr>
                    <td>{{ $mov->producto->nombre }}</td>
                    <td>
                        <span class="badge {{ $mov->tipo === 'entrada' ? 'bg-primary' : 'bg-danger' }}">
                            {{ ucfirst($mov->tipo) }}
                        </span>
                    </td>
                    <td>{{ $mov->cantidad }}</td>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $mov->usuario->name ?? 'N/A' }}</td>
                    <td>
                        {{-- Si decides permitir eliminar: --}}
                        <form action="{{ route('movimientos.destroy', $mov) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
