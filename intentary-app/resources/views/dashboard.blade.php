@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row text-center mb-4">
    <h1 class="display-5">INICIO</h1>
    <p class="text-muted">Modulos del sistema de inventario</p>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-0">
            <div class="card-body d-flex flex-column justify-content-center align-items-center" style="min-height: 400px;"> {{-- Ajustado para un tamaño más dinámico --}}
                {{-- CAMBIO AQUI: Establecemos un tamaño fijo para la imagen y usamos object-fit: cover --}}
                <img id="carouselImage" src="{{ asset('img/default.avif') }}" class="img-fluid rounded mb-3" alt="Imagen del Módulo" style="width: 700px; height: 700px; object-fit: cover;"> {{-- Tamaño fijo y recorte para uniformidad --}}
                <p id="imageDescription" class="text-muted text-center">BIENVENIDO</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="row justify-content-center"> {{-- Centrar las tarjetas dentro de la columna derecha --}}

            <div class="col-md-10 col-lg-8 mb-3">
                <div class="card text-white bg-success module-card" {{-- Cambiado de bg-info a bg-success --}}
                    data-image="{{ asset('img/movimientos.avif') }}"
                    data-description="Registra entradas y salidas de inventario."
                    style="min-height: 150px;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Realizar movimientos</h5>
                            <p class="card-text">Registra entradas y salidas de inventario.</p>
                        </div>
                        <a href="{{ route('movimientos.index') }}" class="btn btn-light align-self-start">Realizar y ver movimientos</a>
                    </div>
                </div>
            </div>


            <div class="col-md-10 col-lg-8 mb-3"> {{-- col-md-10 para menos anchura en pantallas medianas, col-lg-8 para pantallas grandes --}}
                <div class="card text-white bg-primary module-card"
                     data-image="{{ asset('img/inventario.avif') }}"
                     data-description="Gestión completa de tus productos y stock."
                     style="min-height: 150px;"> {{-- Aumentar la altura mínima --}}
                    <div class="card-body d-flex flex-column justify-content-between"> {{-- Cambiado a columna y justificado --}}
                        <div>
                            <h5 class="card-title">Inventario</h5>
                            <p class="card-text">Administra el inventario.</p>
                        </div>
                        <a href="{{ route('productos.index') }}" class="btn btn-light align-self-start">Administrar inventario</a> {{-- Botón a la izquierda --}}
                    </div>
                </div>
            </div>

            <div class="col-md-10 col-lg-8 mb-3">
                <div class="card text-white bg-info module-card"
                     data-image="{{ asset('img/mantenimientos.avif') }}"
                     data-description="Registra y consulta todos los mantenimientos realizados."
                     style="min-height: 150px;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Mantenimientos</h5>
                            <p class="card-text">Registro de mantenimientos</p>
                        </div>
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-light align-self-start">Ver mantenimientos</a>
                    </div>
                </div>
            </div>

            <div class="col-md-10 col-lg-8 mb-3">
                <div class="card text-white bg-dark module-card"
                     data-image="{{ asset('img/usuarios.avif') }}"
                     data-description="Administra los usuarios y sus permisos en el sistema."
                     style="min-height: 150px;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Usuarios</h5>
                            <p class="card-text">Gestión de cuentas del sistema.</p>
                        </div>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-light align-self-start">Ver usuarios</a>
                    </div>
                </div>
            </div>

            <div class="col-md-10 col-lg-8 mb-3">
                <div class="card text-white bg-warning module-card"
                     data-image="{{ asset('img/alertas.avif') }}"
                     data-description="Revisa alertas sobre productos con stock bajo."
                     style="min-height: 150px;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Alertas de stock</h5>
                            <p class="card-text">Revisa productos con stock bajo.</p>
                        </div>
                        <a href="{{ route('alertas.index') }}" class="btn btn-light align-self-start">Ver alertas</a>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carouselImage = document.getElementById('carouselImage');
        const imageDescription = document.getElementById('imageDescription');
        const moduleCards = document.querySelectorAll('.module-card');
        const defaultImage = '{{ asset('img/default.avif') }}';
        const defaultDescription = 'BIENVENIDO';

        // Carga inicial de la imagen y descripción por defecto
        carouselImage.src = defaultImage;
        imageDescription.textContent = defaultDescription;

        moduleCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const imageUrl = this.dataset.image;
                const descriptionText = this.dataset.description;
                carouselImage.src = imageUrl;
                imageDescription.textContent = descriptionText;
            });

            card.addEventListener('mouseleave', function() {
                // Al salir de cualquier tarjeta, vuelve a la imagen y descripción por defecto
                carouselImage.src = defaultImage;
                imageDescription.textContent = defaultDescription;
            });
        });
    });
</script>
@endpush
