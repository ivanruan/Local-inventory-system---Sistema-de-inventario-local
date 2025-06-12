<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            {{-- Aquí va el logo --}}
            <img src="{{ asset('img/logo.avif') }}" alt="Logo" class="d-inline-block me-2" width="38" height="38">
            EFFESUS | Sistema de Inventario
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}"
                       href="{{ route('productos.index') }}">
                        <i class="fas fa-boxes me-1"></i>Inventario
                    </a>
                </li>

                {{-- Cerrar sesion --}}
                @auth
                    <li class="nav-item dropdown"> {{-- **CORRECCIÓN: Se añade el <li> para envolver el dropdown** --}}
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false"> {{-- Agregado aria-expanded para accesibilidad --}}
                            <i class="fas fa-user me-1"></i>{{ auth()->user()->nombre }}
                            <span class="badge bg-secondary ms-1">{{ ucfirst(auth()->user()->rol) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end"> {{-- **CORRECCIÓN: Se añade dropdown-menu-end para alinear a la derecha** --}}
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>


       
                    <li class="nav-item dropdown">