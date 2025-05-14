<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Inventario')</title>

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Íconos de Bootstrap (opcional pero útil) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Estilos personalizados --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- Navbar --}}
    @include('partials.navbar')

    <div class="container-fluid">
        <div class="row">

            {{-- Sidebar (opcional) --}}
            @hasSection('sidebar')
                <aside class="col-md-3 col-lg-2 bg-light p-3 border-end">
                    @yield('sidebar')
                </aside>
            @endif

            {{-- Contenido principal --}}
            <main class="@hasSection('sidebar') col-md-9 col-lg-10 @else col-12 @endif py-4">
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
        document.getElementById('imagen').addEventListener('change', function(e) {
            const preview = document.getElementById('imagen-preview');
            if (preview) {
                preview.src = URL.createObjectURL(e.target.files[0]);
            }
        }); 
    </script>

    @stack('scripts')
</body>
</html>

