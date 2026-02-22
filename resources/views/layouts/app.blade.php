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
            SISTEMA ADMINISTRATIVO <span class="text-primary">DE REQUISICIONES</span>
        </section>
        
        <section class="header-right-group d-flex align-items-center gap-3">
            <section class="welcome-bar text-light">
                Bienvenido, <span class="fw-bold text-primary">{{ auth()->user()->nombre ?? 'Invitado' }}</span>
                <span class="badge bg-secondary ms-1">{{ auth()->user()->rol ?? '' }}</span>
            </section>

            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">🚪 Cerrar Sesión</button>
            </form>
        </section>
    </header>

    <div class="d-flex">
        <nav class="main-nav-sidebar bg-white shadow-sm" style="min-width: 250px; min-height: 100vh; padding-top: 20px;">
            <ul class="nav flex-column px-3">
                
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark rounded" href="{{ route('requisiciones.create') }}">➕ Crear Registro</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark rounded" href="{{ route('requisiciones.index') }}">📄 Consultar</a>
                </li>

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'ServicioTecnico', 'Almacen']))
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark rounded" href="{{ route('st.index') }}">🔧 Servicio Técnico</a>
                </li>
                @endif

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion']))
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark rounded" href="{{ route('productos.index') }}">📦 Agregar Productos</a>
                </li>
                @endif
                
                @if(auth()->user()->rol === 'SuperAdmin')
                    <hr>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark rounded" href="{{ route('usuarios.index') }}">👤 Gestionar Usuarios</a>
                    </li>
                @endif
            </ul>
        </nav>

        <main class="main-content-wrapper flex-grow-1 p-4 w-100">
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
</body>
</html>