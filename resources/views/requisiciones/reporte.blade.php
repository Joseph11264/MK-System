<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte #{{ $requisicion->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        
        /* Estilos del Encabezado con tabla para alinear el logo a la derecha */
        .header-table { width: 100%; border-bottom: 2px solid #007bff; margin-bottom: 20px; padding-bottom: 10px; }
        .header-table td { vertical-align: middle; }
        .logo { max-height: 80px; max-width: 120px; }
        
        /* Estilos de los datos generales */
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px 0; }
        .badge-cancelado { color: #dc3545; font-weight: bold; border: 1px solid #dc3545; padding: 2px 5px; border-radius: 3px; }

        /* Estilos de la tabla de productos */
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-danger { color: #dc3545; font-size: 11px; font-weight: bold; font-style: italic; }
        
        /* Sello de agua para Cancelados */
        .watermark { text-align: center; margin-top: 50px; color: #ffdddd; font-size: 45px; font-weight: bold; border: 5px solid #ffdddd; padding: 15px; transform: rotate(-10deg); }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <h1 style="margin: 0;">SISTEMA MK - {{ strtoupper($requisicion->tipo) }} #{{ $requisicion->id }}</h1>
            </td>
            <td style="width: 30%; text-align: right;">
                <img src="{{ public_path('img/logo.jpg') }}" class="logo" alt="Logo MK">
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="width: 50%;"><strong>Técnico:</strong> {{ $requisicion->nombre_tecnico }} ({{ $requisicion->nro_tecnico }})</td>
            <td style="width: 50%;"><strong>Fecha:</strong> {{ $requisicion->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Tipo:</strong> {{ $requisicion->tipo === 'Devolucion' ? 'Devolución' : 'Requisición' }}</td>
            <td>
                <strong>Estado:</strong> 
                @if($requisicion->status === 'Cancelado')
                    <span class="badge-cancelado">CANCELADO</span>
                @else
                    {{ $requisicion->status }}
                @endif
            </td>
        </tr>
    </table>

    <h3>Productos Solicitados</h3>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 15%;">Código</th>
                <th style="width: 40%;">Descripción en Catálogo</th>
                <th class="text-center" style="width: 10%;">Cantidad</th>
                <th style="width: 35%;">Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisicion->detalles as $detalle)
            <tr>
                <td>{{ $detalle->codigo_producto }}</td>
                <td>
                    @if($detalle->productoCatalogo)
                        {{ $detalle->productoCatalogo->descripcion }}
                    @else
                        <span class="text-danger"> Producto no añadido al sistema</span>
                    @endif
                </td>
                <td class="text-center">{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->observacion ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($requisicion->status === 'Cancelado')
        <div class="watermark">
            DOCUMENTO ANULADO
        </div>
    @endif

</body>
</html>