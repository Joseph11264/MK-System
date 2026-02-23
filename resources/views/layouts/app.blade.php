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
        
        <button class="btn btn-outline-light d-md-none me-3 border-0 fs-3 p-0" id="btnToggleSidebar">
                ☰
        </button>

        <a href="{{ route('dashboard') }}" class="text-decoration-none d-flex align-items-center ms-3">
            <div class="bg-body rounded-circle d-flex justify-content-center align-items-center shadow-sm" style="width: 48px; height: 48px;">
                <img src="{{ asset('img/logo.jpg') }}" alt="Logo MK" style="max-height: 35px; object-fit: contain;">
            </div>
            <span class="ms-3 fw-bold text-white fs-4 tracking-tight d-none d-sm-block">SISTEMA MK</span>
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

                 @if(in_array(auth()->user()->rol, ['SuperAdmin', 'Administracion', 'Produccion', 'Almacen']))
                <li class="nav-item mb-2 mt-3">
                    <small class="text-muted text-uppercase fw-bold ms-2">Fábrica</small>
                    <a class="nav-link text-body rounded border border-light custom-hover mt-1" href="{{ route('ordenes.index') }}">🏭 Órdenes de Producción</a>
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

    document.getElementById('btnToggleSidebar')?.addEventListener('click', function(e) {
            e.preventDefault(); // Evita que la página salte
            document.querySelector('.main-nav-sidebar').classList.toggle('mobile-show');
        });

        // Opcional: Cerrar el menú si tocas cualquier parte del fondo (en móviles)
        document.querySelector('.main-content-wrapper').addEventListener('click', function() {
            const sidebar = document.querySelector('.main-nav-sidebar');
            if(window.innerWidth <= 768 && sidebar.classList.contains('mobile-show')) {
                sidebar.classList.remove('mobile-show');
            }
        });

        

    </script>   
    
    <style>
        .custom-hover:hover {
            background-color: var(--bs-tertiary-bg);
            color: #0056b3 !important;
            font-weight: 500;
        }

        /* ==========================================================
           MEJORAS PARA MODO OSCURO (SIN PROBLEMAS DE CACHÉ)
           ========================================================== */
        [data-bs-theme="dark"] {
            /* 1. Fondo principal de la pantalla (casi negro mate) */
            --bs-body-bg: #101010 !important;
            --bs-body-tertiary-bg: #101010 !important;
            
            /* 2. Superficies y Tarjetas (gris oscuro para elevar) */
            --bs-secondary-bg: #1e1e1e !important;
            --bs-card-bg: #1e1e1e !important;
            
            /* 3. Textos y bordes */
            --bs-body-color: #e0e0e0 !important;
            --bs-border-color: #333333 !important;
        }

        /* 4. Forzar fondos que usan clases blancas por defecto */
        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .bg-light {
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
        }

        /* 5. ARREGLO DE LA CABECERA AZUL, BLANCA Y SECUNDARIA */
        [data-bs-theme="dark"] .table-primary th {
            background-color: #1a222b !important; /* Tono gris-azulado */
            color: #e0e0e0 !important;
            border-bottom: 2px solid #2c3e50 !important;
        }
        
        [data-bs-theme="dark"] .table-light th,
        [data-bs-theme="dark"] thead.table-light th,
        [data-bs-theme="dark"] .table-secondary th,
        [data-bs-theme="dark"] thead.table-secondary th {
            background-color: #24282c !important; /* Gris oscuro elegante, adiós al blanco */
            color: #e0e0e0 !important;
            border-bottom: 2px solid #373b3e !important;
        }

        /* 6. Fondo general de las filas de la tabla */
        [data-bs-theme="dark"] .table,
        [data-bs-theme="dark"] table tbody tr td {
            background-color: #1e1e1e !important; /* Mismo color de las tarjetas */
            color: #d1d1d1 !important;
            border-color: #333 !important;
        }

        /* 6. Fondo general de las filas de la tabla */
        [data-bs-theme="dark"] .table {
            --bs-table-bg: #1e1e1e; /* Mismo color de las tarjetas (gris claro) */
            --bs-table-color: #d1d1d1;
            border-color: #333 !important;
        }

        /* 7. Arreglo para los Inputs y Selects */
        [data-bs-theme="dark"] .form-control, 
        [data-bs-theme="dark"] .form-select {
            background-color: #24282c !important; 
            border-color: #373b3e !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .form-control:focus, 
        [data-bs-theme="dark"] .form-select:focus {
            background-color: #2b3035 !important;
            border-color: #0d6efd !important;
        }

        /* 8. ESTADOS TRANSLÚCIDOS DE LA TABLA (MODO NOCHE) */
        [data-bs-theme="dark"] tr.status-pendiente td,
        [data-bs-theme="dark"] .status-pendiente td {
            background-color: rgba(220, 53, 69, 0.15) !important; 
            color: #ffb3b8 !important; 
        }

        [data-bs-theme="dark"] tr.status-en-curso td,
        [data-bs-theme="dark"] .status-en-curso td {
            background-color: rgba(253, 126, 20, 0.15) !important; 
            color: #ffdf80 !important; 
        }

        [data-bs-theme="dark"] tr.status-completado td,
        [data-bs-theme="dark"] .status-completado td {
            background-color: rgba(40, 167, 69, 0.15) !important; 
            color: #a3e4b7 !important; 
        }

        /* ==========================================================
           RESPONSIVIDAD PARA MÓVILES (SMARTPHONES Y TABLETS)
           ========================================================== */
        @media (max-width: 768px) {
            /* 1. ARREGLAR LA CABECERA (Evitar que el texto empuje la pantalla) */
            .main-header {
                flex-wrap: nowrap;
            }
            .main-header .tracking-tight {
                display: none !important; /* Oculta "SISTEMA MK" para que quepan los botones */
            }
            .main-header .text-end.lh-sm {
                display: none !important; /* Oculta tu nombre de usuario */
            }
            .main-header .badge {
                display: none !important; /* Oculta el rol (SuperAdmin) */
            }

            /* 2. EL MENÚ LATERAL (Hacerlo flotante para que no ocupe espacio físico) */
            .main-nav-sidebar {
                position: fixed !important; /* Mágico: lo saca de la cuadrícula */
                top: 64px !important; /* Lo pone justo debajo de la cabecera */
                left: 0 !important;
                height: calc(100vh - 64px) !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 1050 !important;
                background-color: var(--bs-body-bg) !important; 
            }
            
            .main-nav-sidebar.mobile-show {
                transform: translateX(0);
                box-shadow: 10px 0 20px rgba(0,0,0,0.8) !important; 
            }

            /* 3. EL CONTENIDO PRINCIPAL (Quitar el hueco negro a la izquierda) */
            .main-content-wrapper {
                margin-left: 0 !important;
                width: 100vw !important; /* Toma exactamente el 100% de la pantalla */
                padding: 15px !important; 
            }
        }
    </style>
</body>
</html>