<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Notificación de Servicio Técnico</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #0056b3; color: white; padding: 20px; text-align: center;">
            <h2 style="margin: 0;">SISTEMA MK</h2>
            <p style="margin: 5px 0 0 0;">Notificación de Servicio Técnico</p>
        </div>
        
        <div style="padding: 20px;">
            <p>Hola, <strong>{{ $ticket->cliente }}</strong>.</p>
            <p>Te informamos con gusto que el servicio técnico para tu equipo (Serial/Código: <strong>{{ $ticket->codigo_equipo }}</strong>) ha sido <strong>COMPLETADO</strong>.</p>

            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 0 0 10px 0;"><strong>Resumen de la Orden #{{ $ticket->nro_orden_st }}:</strong></p>
                <ul style="margin: 0; padding-left: 20px;">
                    <li><strong>Tipo de Servicio:</strong> {{ $ticket->tipo_st }}</li>
                    @if($ticket->tipo_st === 'Reparacion')
                        <li><strong>Monto Total:</strong> ${{ number_format($ticket->precio_reparacion, 2) }}</li>
                    @else
                        <li><strong>Monto Total:</strong> Cubierto por Garantía</li>
                    @endif
                </ul>
            </div>

            <p>Adjunto a este correo electrónico encontrarás el reporte detallado en formato PDF con las piezas reemplazadas o las acciones tomadas.</p>
            
            <p style="margin-top: 30px;">Gracias por confiar en nosotros.</p>
            <p><strong>Atentamente,<br>El equipo de MKJOULES</strong></p>
        </div>
    </div>

</body>
</html>