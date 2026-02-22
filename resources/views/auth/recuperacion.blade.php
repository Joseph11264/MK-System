<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Restablecer Contraseña - Sistema MK</title>
</head>
<body class="bg-body-tertiary d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow-lg border-0 border-top border-danger border-5" style="width: 100%; max-width: 450px;">
        <div class="card-body p-4 p-md-5">
            <h3 class="text-center text-danger fw-bold mb-4">🔐 Restablecer Contraseña</h3>
            
            @if(session('error'))
                <div class="alert alert-danger shadow-sm fw-bold">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-muted text-center mb-4">Confirma tu identidad y elige tu nueva contraseña de acceso.</p>

            <form action="{{ route('recuperacion.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Usuario</label>
                        <input type="text" name="username" class="form-control" placeholder="Ej: jperez" required autofocus>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Perez" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nueva Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repite la contraseña" required minlength="6">
                </div>
                
                <button type="submit" class="btn btn-danger w-100 fw-bold mb-3 fs-5 shadow-sm">Guardar Nueva Contraseña</button>
                
                <div class="text-center mt-3 border-top pt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none text-muted fw-bold">Volver al inicio de sesión</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>