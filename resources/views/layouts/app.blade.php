<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA MK - @yield('titulo', 'Inicio')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        const temaGuardado = localStorage.getItem('tema-mk') || 'light';
        document.documentElement.setAttribute('data-bs-theme', temaGuardado);
    </script>
</head>
<body class="bg-body-tertiary">

    <header class="main-header d-flex justify-content-between align-items-center p-2 text-white shadow" style="background: linear-gradient(90deg, #153b75 0%, #0056b3 100%);">
        
        <a href="{{ route('dashboard') }}" class="text-decoration-none d-flex align-items-center ms-3">
            <div class="bg-body rounded-circle d-flex justify-content-center align-items-center shadow-sm" style="width: 48px; height: 48px;">
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
            
                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Almacen']))
                    @php
                        // Consultamos cuántas y cuáles son nuevas (Pendientes)
                        $countPendientes = \App\Models\Requisicion::where('status', 'Pendiente')->count();
                        $reqsPendientes = \App\Models\Requisicion::where('status', 'Pendiente')
                                            ->orderBy('created_at', 'desc')
                                            ->take(5)
                                            ->get();
                    @endphp
                    
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link dropdown-toggle position-relative" href="#" id="notifPendientes" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Nuevas Solicitudes">
                            <span class="fs-5">🔔</span>
                            @if($countPendientes > 0)
                                <span class="position-absolute top-25 start-75 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65em;">
                                    {{ $countPendientes }}
                                </span>
                            @endif
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-danger border-top border-4" aria-labelledby="notifPendientes" style="width: 320px;">
                            <li><h6 class="dropdown-header text-dark fw-bold bg-light py-2">🔴 Nuevas Solicitudes ({{ $countPendientes }})</h6></li>
                            
                            @forelse($reqsPendientes as $req)
                                <li>
                                    <a class="dropdown-item border-bottom py-2 custom-hover" href="{{ route('requisiciones.show', $req->id) }}">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-primary">Req #{{ $req->id }}</span>
                                            <span class="badge bg-danger" style="font-size: 0.7em;">Pendiente</span>
                                        </div>
                                        <div class="text-truncate small text-muted">
                                            Técnico: <span class="fw-bold text-dark">{{ $req->nombre_tecnico }}</span>
                                        </div>
                                        <div class="small text-muted text-end mt-1" style="font-size: 0.75em;">
                                            Creado {{ $req->created_at->diffForHumans() }}
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item text-muted text-center py-4">No hay solicitudes nuevas en este momento.</span></li>
                            @endforelse
                            
                            @if($countPendientes > 0)
                                <li><hr class="dropdown-divider m-0"></li>
                                <li>
                                    <a class="dropdown-item text-center text-danger fw-bold py-2 bg-light text-dark" href="{{ route('requisiciones.index', ['status' => 'Pendiente']) }}">
                                        Ver todas las Pendientes ➔
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

            @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion']))
                    @php
                        // Consultamos cuántas y cuáles están en curso directamente
                        $countEnCurso = \App\Models\Requisicion::where('status', 'En Curso')->count();
                        $reqsEnCurso = \App\Models\Requisicion::where('status', 'En Curso')
                                            ->orderBy('updated_at', 'desc')
                                            ->take(5)
                                            ->get();
                    @endphp
                    
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle position-relative" href="#" id="notifEnCurso" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Trabajos en Curso">
                            <span class="fs-5">⏳</span>
                            @if($countEnCurso > 0)
                                <span class="position-absolute top-25 start-75 translate-middle badge rounded-pill bg-warning text-dark border border-light" style="font-size: 0.65em;">
                                    {{ $countEnCurso }}
                                </span>
                            @endif
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-warning border-top border-4" aria-labelledby="notifEnCurso" style="width: 320px;">
                            <li><h6 class="dropdown-header text-dark fw-bold bg-light py-2">🟠 Preparando en Almacén ({{ $countEnCurso }})</h6></li>
                            
                            @forelse($reqsEnCurso as $req)
                                <li>
                                    <a class="dropdown-item border-bottom py-2 custom-hover" href="{{ route('requisiciones.show', $req->id) }}">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-primary">Req #{{ $req->id }}</span>
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7em;">En Curso</span>
                                        </div>
                                        <div class="text-truncate small text-muted">
                                            Técnico: <span class="fw-bold text-dark">{{ $req->nombre_tecnico }}</span>
                                        </div>
                                        <div class="small text-muted text-end mt-1" style="font-size: 0.75em;">
                                            Modificado {{ $req->updated_at->diffForHumans() }}
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item text-muted text-center py-4">No hay requisiciones en preparación en este momento.</span></li>
                            @endforelse
                            
                            @if($countEnCurso > 0)
                                <li><hr class="dropdown-divider m-0"></li>
                                <li>
                                    <a class="dropdown-item text-center text-warning fw-bold py-2 bg-light text-dark" href="{{ route('requisiciones.index', ['status' => 'En Curso']) }}">
                                        Ver todas las En Curso ➔
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

            <button class="btn btn-sm btn-outline-secondary rounded-circle border-0 fs-5" id="btnModoOscuro" onclick="cambiarTema()" title="Alternar Modo Oscuro">
                <span id="iconoTema">🌙</span>
            </button>
            
            <section class="d-flex align-items-center gap-3 border-start border-light border-opacity-50 ps-3">
                <div class="text-light text-end lh-sm">
                    <span class="fw-bold">{{ auth()->user()->nombre ?? 'Invitado' }}</span>
                    <span class="badge bg-body text-primary ms-1" style="font-size: 0.7em;">{{ auth()->user()->rol ?? '' }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm fw-bold shadow-sm">🚪 Salir</button>
                </form>
            </section>
        </div>
    </header>

    <div class="d-flex">
        <nav class="main-nav-sidebar bg-body shadow-sm" style="min-width: 250px; min-height: calc(100vh - 64px); padding-top: 20px;">
            <ul class="nav flex-column px-3">
                
                <li class="nav-item mb-3">
                    <a class="nav-link text-white bg-primary rounded fw-bold shadow-sm" href="{{ route('dashboard') }}">📊 Panel Principal</a>
                </li>
                
                <li class="nav-item mb-2">
                    <a class="nav-link text-body rounded border border-light custom-hover" href="{{ route('requisiciones.create') }}">➕ Crear Registro</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-body rounded border border-light custom-hover" href="{{ route('requisiciones.index') }}">📄 Consultar</a>
                </li>

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'ServicioTecnico', 'Almacen']))
                <li class="nav-item mb-2 mt-3">
                    <small class="text-muted text-uppercase fw-bold ms-2">Operaciones</small>
                    <a class="nav-link text-body rounded border border-light custom-hover mt-1" href="{{ route('st.index') }}">🔧 Servicio Técnico</a>
                </li>
                @endif

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'ServicioTecnico']))
                <li class="nav-item mb-2">
                    <a class="nav-link text-body rounded border border-light custom-hover" href="{{ route('clientes.index') }}">👥 Directorio de Clientes</a>
                </li>
                 @endif

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Produccion', 'Almacen', 'ServicioTecnico']))
                <li class="nav-item mb-2">
                    <a class="nav-link text-body rounded border border-light custom-hover" href="{{ route('productos.index') }}">📦 Catálogo de Productos</a>
                </li>
                @endif

                @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion']))
                <li class="nav-item mb-2">
                    <a class="nav-link text-body rounded border border-light custom-hover" href="{{ route('familias.index') }}">🏷️ Gestionar Familias</a>
                </li>
                @endif
                
                @if(auth()->user()->rol === 'SuperAdmin')
                    <hr class="my-3 opacity-25">
                    <li class="nav-item mb-2">
                        <small class="text-muted text-uppercase fw-bold ms-2">Administración</small>
                        <a class="nav-link text-body rounded border border-light custom-hover mt-1" href="{{ route('usuarios.index') }}">👤 Gestionar Usuarios</a>
                    </li>
                @endif
            </ul>
        </nav>

        <main class="main-content-wrapper flex-grow-1 p-4 w-100 bg-body">
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

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const temaActual = document.documentElement.getAttribute('data-bs-theme');
        document.getElementById('iconoTema').innerText = temaActual === 'dark' ? '☀️' : '🌙';
    });

    function cambiarTema() {
        const html = document.documentElement;
        const temaActual = html.getAttribute('data-bs-theme');
        const nuevoTema = temaActual === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-bs-theme', nuevoTema);
        document.getElementById('iconoTema').innerText = nuevoTema === 'dark' ? '☀️' : '🌙';
        localStorage.setItem('tema-mk', nuevoTema);
    }
    </script>   
    
    <style>
        .custom-hover:hover {
            background-color: var(--bs-tertiary-bg);
            color: #0056b3 !important;
            font-weight: 500;
        }
    </style>
</body>
</html>