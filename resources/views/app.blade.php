<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA MK - @yield('titulo', 'Inicio')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-light">

    <header class="main-header d-flex justify-content-between align-items-center p-3 bg-dark text-white">
        <section class="logo-area">
            SISTEMA ADMINISTRATIVO <span class="text-primary">DE ALMACÉN</span>
        </section>
        
        <section class="header-right-group d-flex align-items-center gap-3">
            <section class="welcome-bar">
                Bienvenido, <span class="fw-bold">{{ auth()->user()->nombre ?? 'Invitado' }}</span>
                <span class="badge bg-secondary ms-1">{{ auth()->user()->rol ?? '' }}</span>
            </section>

            <section class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle position-relative" type="button" data-bs-toggle="dropdown">
                    🔔
                    @if(isset($notificaciones['pendientes']) && $notificaciones['pendientes'] > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $notificaciones['pendientes'] }}
                        </span>
                    @endif
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    @if(isset($notificaciones['pendientes']) && $notificaciones['pendientes'] > 0)
                        <li><a class="dropdown-item text-danger" href="{{ route('requisiciones.index', ['status' => 'Pendiente']) }}">
                            Tienes {{ $notificaciones['pendientes'] }} pendientes
                        </a></li>
                    @endif
                    @if(isset($notificaciones['curso']) && $notificaciones['curso'] > 0)
                        <li><a class="dropdown-item text-warning" href="{{ route('requisiciones.index', ['status' => 'En Curso']) }}">
                            Hay {{ $notificaciones['curso'] }} en curso
                        </a></li>
                    @endif
                    @if(empty($notificaciones['pendientes']) && empty($notificaciones['curso']))
                        <li><span class="dropdown-item text-muted">No tienes notificaciones.</span></li>
                    @endif
                </ul>
            </section>
        </section>
    </header>

    <div class="d-flex">
        <nav class="main-nav-sidebar bg-white shadow-sm" style="min-width: 250px; min-height: 100vh;">
            <ul class="nav flex-column p-3">
                
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="{{ route('requisiciones.create') }}">➕ Crear Registro</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="{{ route('requisiciones.index') }}">📄 Consultar</a>
                </li>

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'ServicioTecnico', 'Almacen']))
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="{{ route('st.index') }}">🔧 Servicio Técnico</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="{{ route('st.create') }}">🔧 Crear Registro ST</a>
                    </li>
                @endif

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion']))
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="{{ route('productos.create') }}">📦 Agregar Productos</a>
                    </li>
                @endif

                @if(auth()->user()->rol === 'SuperAdmin')
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="{{ route('usuarios.index') }}">👤 Gestionar Usuarios</a>
                    </li>
                @endif

                <hr>
                
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 text-start">🚪 Cerrar Sesión</button>
                    </form>
                </li>
            </ul>
        </nav>

        <main class="flex-grow-1 p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <strong>¡Éxito!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <strong>¡Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/alertas.js') }}"></script>
</body>
</html>