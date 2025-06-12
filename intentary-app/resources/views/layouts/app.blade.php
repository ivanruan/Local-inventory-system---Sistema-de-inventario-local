<!DOCTYPE html>
<html lang="es" class="h-100"> {{-- ¡Importante! Añadir 'h-100' aquí --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Inventario')</title>

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Íconos de Bootstrap (opcional pero útil) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Font Awesome CSS - si usas iconos de Font Awesome, también lo necesitas --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    {{-- Estilos personalizados --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    {{--Nuestro logo en el tab--}}
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100"> {{-- 'd-flex flex-column min-vh-100' ya está genial aquí --}}

    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Contenedor principal que se expandirá --}}
    <div class="container-fluid flex-grow-1"> {{-- ¡Importante! Añadir 'flex-grow-1' aquí --}}
        <div class="row">

            {{-- Sidebar (opcional) --}}
            @hasSection('sidebar')
                <aside class="col-md-3 col-lg-2 bg-light p-3 border-end">
                    @yield('sidebar')
                </aside>
            @endif

            {{-- Contenido principal --}}
            <main class="@hasSection('sidebar') col-md-9 col-lg-10 @else col-12 @endif py-4">
                {{-- Mensajes de Sesión (Éxito o Error) - Sugerencia: Puedes centralizar esto en un layout principal --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Bootstrap Bundle (incluye Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Scripts personalizados --}}
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imagenInput = document.getElementById('imagen');
            if (imagenInput) {
                imagenInput.addEventListener('change', function(e) {
                    const preview = document.getElementById('imagen-preview');
                    if (preview && e.target.files && e.target.files[0]) {
                        preview.src = URL.createObjectURL(e.target.files[0]);
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
