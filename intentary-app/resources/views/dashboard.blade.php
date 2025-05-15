@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row text-center mb-4">
    <h1 class="display-5">Panel de Control</h1>
    <p class="text-muted">Resumen general del sistema de inventario</p>
</div>

<div class="row">
    <!-- Productos -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Productos</h5>
                <p class="card-text">Administra el catálogo de productos.</p>
                <a href="{{ route('productos.index') }}" class="btn btn-light">Ver productos</a>
            </div>
        </div>
    </div>

     <!-- Manteminimientos -->
     <div class="col-md-6 mb-4">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Mantenimientos</h5>
                <p class="card-text">Registro de mantenimientos</p>
                <a href="{{ route('mantenimientos.index') }}" class="btn btn-light">Ver mantenimientos</a>
            </div>
        </div>
    </div>

    <!-- Usuarios -->
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-dark h-100">
            <div class="card-body">
                <h5 class="card-title">Usuarios</h5>
                <p class="card-text">Gestión de cuentas del sistema.</p>
                <a href="{{ route('usuarios.index') }}" class="btn btn-light">Ver usuarios</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Alertas -->
    <div class="col-md-6 mb-4">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Alertas de stock</h5>
                <p class="card-text">Revisa productos con stock bajo.</p>
                <a href="{{ route('alertas.index') }}" class="btn btn-light">Ver alertas</a>
            </div>
        </div>
    </div>

    <!-- Movimientos -->
    <div class="col-md-6 mb-4">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Movimientos</h5>
                <p class="card-text">Registra entradas y salidas de inventario.</p>
                <a href="{{ route('movimientos.index') }}" class="btn btn-light">Ver movimientos</a>
            </div>
        </div>
    </div>
</div>
    
@endsection
