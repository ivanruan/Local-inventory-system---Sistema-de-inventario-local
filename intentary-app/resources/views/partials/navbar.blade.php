<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Inventario</a>
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
                        <i class="fas fa-boxes me-1"></i>Productos
                    </a>
                </li>
                
                {{-- Dropdown para más opciones --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" 
                       role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog me-1"></i>Gestión
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('productos.index') }}">
                            <i class="fas fa-trademark me-2"></i>productos
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('proveedores.index') }}">
                            <i class="fas fa-truck me-2"></i>Proveedores
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('ubicaciones.index') }}">
                            <i class="fas fa-map-marker-alt me-2"></i>Ubicaciones
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('proyectos.index') }}">
                            <i class="fas fa-project-diagram me-2"></i>Proyectos
                        </a></li>
                    </ul>
                </li>

                {{-- Cerrar sesion --}}
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" 
                        role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ auth()->user()->nombre }}
                            <span class="badge bg-secondary ms-1">{{ ucfirst(auth()->user()->rol) }}</span>
                        </a>
                        <ul class="dropdown-menu">
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
