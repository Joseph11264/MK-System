<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { border-bottom: 3px solid #007bff; margin-bottom: 20px; padding-bottom: 10px; }
        .section-title { background: #f4f4f4; padding: 5px; font-weight: bold; border-left: 4px solid #007bff; margin: 15px 0 10px 0; }
        .info-grid { width: 100%; margin-bottom: 20px; }
        .info-grid td { padding: 4px 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .table th { background-color: #eee; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; color: #007bff;">ORDEN DE SERVICIO TÉCNICO #{{ $ticket->nro_orden_st }}</h1>
        <p style="margin: 5px 0;">SISTEMA MK - Reporte Oficial de Recepción</p>
    </div>

    <table class="info-grid">
        <tr>
            <td style="width: 50%;"><strong>Cliente:</strong> {{ $ticket->cliente }}</td>
            <td><strong>Fecha de Ingreso:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Teléfono:</strong> {{ $ticket->telefono_cliente ?? 'N/A' }}</td>
            <td><strong>Correo:</strong> {{ $ticket->correo_cliente ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Equipo / Serial:</strong> {{ $ticket->codigo_equipo }}</td>
            <td><strong>Técnico Asignado:</strong> {{ $ticket->tecnico->nombre ?? 'Sin asignar' }}</td>
        </tr>
    </table>

    <div class="section-title">DETALLES TÉCNICOS / REPUESTOS SOLICITADOS</div>
    <table class="table">
        <thead>
            <tr>
                <th>Cód. Producto</th>
                <th style="text-align: center;">Cant.</th>
                <th>Descripción de Falla u Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticket->detalles as $detalle)
            <tr>
                <td>{{ $detalle->codigo_producto }}</td>
                <td style="text-align: center;">{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->observacion ?: 'Ver revisión técnica' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Este documento es un comprobante de recepción de equipo. Sujeto a revisión técnica posterior.</p>
        <p>Generado por: {{ auth()->user()->nombre }} el {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>