@props(['status'])

@php
    // Mapa de colores según el texto del estado
    $styles = [
        // Estados de Flujo (Requisiciones / Tickets)
        'Pendiente'  => 'bg-warning text-dark',      // Amarillo: Atención
        'En Curso'   => 'bg-info text-dark',         // Cian: Trabajando
        'Completado' => 'bg-success text-white',     // Verde: Finalizado
        'Cancelado'  => 'bg-danger text-white',      // Rojo: Error/Cancelado
        
        // Estados Financieros
        'Pagado'     => 'bg-success text-white',
        'No Pagado'  => 'bg-danger text-white',
        
        // Tipos de Servicio
        'Reparacion' => 'bg-primary text-white',     // Azul
        'Garantia'   => 'bg-warning text-dark',      // Amarillo
    ];

    // Si el estado no existe en la lista, usamos gris (secondary) por defecto
    $class = $styles[$status] ?? 'bg-secondary text-white';
@endphp

{{-- Renderizado del Badge --}}
<span {{ $attributes->merge(['class' => "badge $class shadow-sm border border-light", 'style' => 'font-size: 0.85rem; letter-spacing: 0.5px;']) }}>
    {{ $status }}
</span>