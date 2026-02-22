<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Acceso al Sistema</title>
    
    <script>
        const temaGuardado = localStorage.getItem('tema-mk') || 'light';
        document.documentElement.setAttribute('data-bs-theme', temaGuardado);
    </script>
</head>
<body class="bg-body-tertiary d-flex align-items-center justify-content-center position-relative" style="height: 100vh;"> 

<button class="btn btn-outline-secondary position-absolute top-0 end-0 m-3 rounded-circle border-0 fs-4" id="btnModoOscuro" onclick="cambiarTema()" title="Modo Oscuro">
    <span id="iconoTema">🌙</span>
</button>

<section class="login-container shadow p-4 bg-body rounded border-top border-primary border-5" style="max-width: 400px; width: 100%;">
    
    <div class="text-center mb-3">
        <img src="{{ asset('img/logo.jpg') }}" alt="Logo MK" style="max-height: 80px; object-fit: contain;">
    </div>

    <h2 class="text-center mb-3 text-primary fs-4 fw-bold">Acceso de Usuarios</h2>
    <hr>
    
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <p class="mb-0">{{ $errors->first('username') }}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf 
        <div class="mb-3">
            <label for="username" class="form-label fw-bold">Usuario</label>
            <input type="text" class="form-control" placeholder="Ingrese Nombre de Usuario" name="username" id="username" value="{{ old('username') }}" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-bold">Contraseña</label>
            <input type="password" class="form-control" placeholder="Ingrese Contraseña" name="password" id="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 mb-3 fw-bold fs-5 shadow-sm">
            Ingresar
        </button>

        @if(session('login_attempts', 0) >= 3)
            <div class="mt-4 text-center border-top pt-3">
                <p class="text-muted mb-1 small">¿Tienes problemas para ingresar?</p>
                <a href="{{ route('recuperacion.create') }}" class="text-danger fw-bold text-decoration-none d-block p-2 bg-danger bg-opacity-10 rounded">
                    🔐 Recuperar Usuario / Contraseña
                </a>
            </div>
        @endif
    </form>
</section>   

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
</body>
</html>