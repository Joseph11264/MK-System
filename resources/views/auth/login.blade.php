<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Acceso al Sistema</title>
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;"> 

<section class="login-container shadow-sm p-4 bg-white rounded border-top border-primary border-5" style="max-width: 400px; width: 100%;">
    <h2 class="text-center mb-3 text-primary">Acceso de Usuarios</h2>
    <hr>
    
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <p class="mb-0">{{ $errors->first('username') }}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf <div class="mb-3">
            <label for="username" class="form-label fw-bold">Usuario</label>
            <input type="text" class="form-control" placeholder="Ingrese Nombre de Usuario" name="username" id="username" value="{{ old('username') }}" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-bold">Contraseña</label>
            <input type="password" class="form-control" placeholder="Ingrese Contraseña" name="password" id="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 mb-3">
            Ingresar
        </button>

        <div class="text-center">
            <a href="#" class="text-decoration-none text-secondary">¿Olvidaste tu contraseña?</a>
        </div>
    </form>
</section>   

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>