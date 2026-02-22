<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Servicio #{{ $ticket->nro_orden_st }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        
        /* Encabezado estructurado con tabla */
        .header-table { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #0056b3; padding-bottom: 10px; }
        .header-left { width: 70%; vertical-align: middle; }
        .header-right { width: 30%; text-align: right; vertical-align: middle; }
        
        /* Ajuste del logo igual que en requisiciones */
        .logo { max-height: 90px; max-width: 140px; object-fit: contain; } 
        
        .title { color: #0056b3; font-size: 22px; font-weight: bold; margin: 0; }
        .subtitle { color: #555; font-size: 14px; margin: 5px 0 0 0; font-weight: bold; }
        
        /* Cuadrícula de Información */
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px 0; }
        .label { font-weight: bold; color: #000; }
        
        /* Etiquetas */
        .badge-cancelado { color: #dc3545; font-weight: bold; font-size: 13px; border: 1px solid #dc3545; padding: 3px 6px; border-radius: 4px; }
        .badge-normal { font-weight: bold; font-size: 13px; }
        
        /* Tabla de Productos/Repuestos */
        .products-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .products-table th, .products-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .products-table th { background-color: #f4f4f4; color: #333; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-danger { color: #dc3545; font-weight: bold; font-size: 11px; font-style: italic; }
        .text-success { color: #198754; font-weight: bold; }
        
        /* Marca de Agua para Cancelados */
        .watermark { text-align: center; margin-top: 40px; color: #ffdddd; font-size: 45px; font-weight: bold; border: 5px solid #ffdddd; padding: 15px; transform: rotate(-10deg); }
        
        /* Pie de página */
        .footer { width: 100%; text-align: center; font-size: 10px; color: #888; position: fixed; bottom: 0; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-left">
                <div class="title">SISTEMA MK</div>
                <div class="subtitle">
                    ORDEN DE SERVICIO TÉCNICO #{{ $ticket->nro_orden_st }}
                </div>
            </td>
            <td class="header-right">
                <img src="{{ public_path('img/logo.jpg') }}" alt="Logo MK" class="logo">
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <span class="label">Cliente:</span> {{ $ticket->cliente }}
            </td>
            <td style="width: 50%;">
                <span class="label">Fecha de Recepción:</span> {{ $ticket->created_at->format('d/m/Y H:i') }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Contacto:</span> 
                {{ $ticket->telefono_cliente ?: 'Sin teléfono' }} 
                @if($ticket->correo_cliente) | {{ $ticket->correo_cliente }} @endif
            </td>
            <td>
                <span class="label">Equipo / Serial:</span> {{ $ticket->codigo_equipo }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Técnico Asignado:</span> 
                {{ $ticket->tecnico->nombre ?? 'Pendiente por asignar' }}
            </td>
            <td>
                <span class="label">Tipo de Servicio:</span> 
                <strong>{{ $ticket->tipo_st === 'Garantia' ? 'GARANTÍA' : 'REPARACIÓN' }}</strong>
            </td>
        </tr>
        <tr>
            <td style="background-color: #e9ecef; padding: 8px; border-radius: 4px;">
                <span class="label" style="font-size: 13px;">Precio de Reparación:</span> 
                <span style="font-size: 14px; font-weight: bold; color: #198754;">
                    ${{ number_format($ticket->precio_reparacion ?? 0, 2) }}
                </span>
            </td>
        </tr>
    </table>

    <h3 style="color: #0056b3; margin-bottom: 10px; font-size: 14px;">Repuestos / Componentes Asignados</h3>
    
    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 15%;">Código</th>
                <th style="width: 40%;">Componente</th>
                <th class="text-center" style="width: 10%;">Cant.</th>
                <th style="width: 35%;">Observación / Falla</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ticket->detalles as $detalle)
            <tr>
                <td><strong>{{ $detalle->codigo_producto }}</strong></td>
                <td>
                    @if($detalle->productoCatalogo)
                        {{ $detalle->productoCatalogo->descripcion }}
                    @else
                        <span class="text-danger"> Producto no añadido al sistema</span>
                    @endif
                </td>
                <td class="text-center"><strong>{{ $detalle->cantidad }}</strong></td>
                <td>{{ $detalle->observacion ?: 'Ver revisión técnica' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No se han registrado repuestos para esta orden.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($ticket->status === 'Cancelado')
        <div class="watermark">
            SERVICIO ANULADO
        </div>
    @endif

    <div class="footer">
        Orden de Servicio generada por el Sistema MK<br>
        Registrado por: {{ $ticket->creador->nombre ?? 'Sistema' }}
    </div>

</body>
</html>