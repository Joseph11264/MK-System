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

    <header class="main-header d-flex justify-content-between align-items-center p-2 text-white shadow" style="background: linear-gradient(90deg, #153b75 0%, #0056b3 100%);">
        
        <a href="{{ route('dashboard') }}" class="text-decoration-none d-flex align-items-center ms-3">
            <div class="bg-white rounded-circle d-flex justify-content-center align-items-center shadow-sm" style="width: 48px; height: 48px;">
                <img src="{{ asset('img/logo.jpg') }}" alt="Logo MK" style="max-height: 35px; object-fit: contain;">
            </div>
            <span class="ms-3 fw-bold text-white fs-4 tracking-tight">SISTEMA MK</span>
        </a>

        @php
            $reqPendientes = \App\Models\Requisicion::where('status', 'Pendiente')->count();
            $stPendientes = \App\Models\RequisicionSt::where('status', 'Pendiente')->count();
            $totalNotif = $reqPendientes + $stPendientes;
        @endphp

        <div class="d-flex align-items-center gap-4 me-3">
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle position-relative text-white" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.3rem;">
                        🔔
                        @if($totalNotif > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.55em;">
                                {{ $totalNotif }}
                            </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="notifDropdown" style="min-width: 260px;">
                        <li><h6 class="dropdown-header text-primary fw-bold bg-light py-2">Notificaciones Pendientes</h6></li>
                        
                        @if($reqPendientes > 0)
                            <li>
                                <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('requisiciones.index', ['status' => 'Pendiente']) }}">
                                    <span>📄 Requisiciones</span>
                                    <span class="badge bg-primary rounded-pill">{{ $reqPendientes }}</span>
                                </a>
                            </li>
                        @endif
                        
                        @if($stPendientes > 0)
                            <li>
                                <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('st.index', ['status' => 'Pendiente']) }}">
                                    <span>🔧 Tickets ST</span>
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $stPendientes }}</span>
                                </a>
                            </li>
                        @endif

                        @if($totalNotif == 0)
                            <li><span class="dropdown-item text-muted py-3 text-center">¡Todo al día! No hay tareas pendientes </span></li>
                        @endif
                    </ul>
                </li>
            </ul>
            
            <section class="d-flex align-items-center gap-3 border-start border-light border-opacity-50 ps-3">
                <div class="text-light text-end lh-sm">
                    <span class="fw-bold">{{ auth()->user()->nombre ?? 'Invitado' }}</span>
                    <span class="badge bg-light text-primary ms-1" style="font-size: 0.7em;">{{ auth()->user()->rol ?? '' }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm fw-bold shadow-sm">🚪 Salir</button>
                </form>
            </section>
        </div>
    </header>

    <div class="d-flex">
        <nav class="main-nav-sidebar bg-white shadow-sm" style="min-width: 250px; min-height: calc(100vh - 64px); padding-top: 20px;">
            <ul class="nav flex-column px-3">
                
                <li class="nav-item mb-3">
                    <a class="nav-link text-white bg-primary rounded fw-bold shadow-sm" href="{{ route('dashboard') }}">📊 Panel Principal</a>
                </li>
                
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark rounded border border-light custom-hover" href="{{ route('requisiciones.create') }}">➕ Crear Registro</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark rounded border border-light custom-hover" href="{{ route('requisiciones.index') }}">📄 Consultar</a>
                </li>

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'ServicioTecnico', 'Almacen']))
                <li class="nav-item mb-2 mt-3">
                    <small class="text-muted text-uppercase fw-bold ms-2">Operaciones</small>
                    <a class="nav-link text-dark rounded border border-light custom-hover mt-1" href="{{ route('st.index') }}">🔧 Servicio Técnico</a>
                </li>
                @endif

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion']))
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark rounded border border-light custom-hover" href="{{ route('productos.index') }}">📦 Catálogo de Productos</a>
                </li>
                @endif

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion']))
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark rounded border border-light custom-hover" href="{{ route('familias.index') }}">🏷️ Gestionar Familias</a>
                </li>
                @endif
                
                @if(auth()->user()->rol === 'SuperAdmin')
                    <hr class="my-3 opacity-25">
                    <li class="nav-item mb-2">
                        <small class="text-muted text-uppercase fw-bold ms-2">Administración</small>
                        <a class="nav-link text-dark rounded border border-light custom-hover mt-1" href="{{ route('usuarios.index') }}">👤 Gestionar Usuarios</a>
                    </li>
                @endif
            </ul>
        </nav>

        <main class="main-content-wrapper flex-grow-1 p-4 w-100 bg-light">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-5 border-success" role="alert">
                    <strong>¡Éxito!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-5 border-danger" role="alert">
                    <strong>¡Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .custom-hover:hover {
            background-color: #f8f9fa;
            color: #0056b3 !important;
            font-weight: 500;
        }
    </style>
</body>
</html>