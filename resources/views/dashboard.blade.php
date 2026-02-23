@extends('layouts.app')

@section('titulo', 'Panel de Control')

@section('content')
<section class="main-content-wrapper">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <h2 class="text-primary m-0 mb-3 mb-md-0">📊 Panel de Rendimiento</h2>
        
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 shadow-sm p-2 bg-body rounded border border-secondary border-opacity-25">
            <select name="mes" class="form-select fw-bold border-0 bg-transparent">
                @foreach($nombresMeses as $num => $nombre)
                    <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                @endforeach
            </select>
            
            <select name="anio" class="form-select fw-bold border-0 bg-transparent">
                @for($i = date('Y'); $i >= 2024; $i--)
                    <option value="{{ $i }}" {{ $anio == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            
            <button type="submit" class="btn btn-primary fw-bold px-3">Consultar</button>
        </form>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    🔧 Servicio Técnico ({{ $nombresMeses[$mes] }} {{ $anio }})
                </div>
                <div class="card-body bg-body d-flex flex-column align-items-center justify-content-center">
                    @if($stPendientes == 0 && $stCompletados == 0 && $stCancelados == 0)
                        <p class="text-muted fs-5 my-5">No hay registros de ST en este mes.</p>
                    @else
                        <div style="position: relative; height: 300px; width: 100%;">
                            <canvas id="graficaST"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header bg-success text-white fw-bold">
                    📦 Requisiciones de Producción ({{ $nombresMeses[$mes] }} {{ $anio }})
                </div>
                <div class="card-body bg-body d-flex flex-column align-items-center justify-content-center">
                    @if($reqPendientes == 0 && $reqEnCurso == 0 && $reqCompletados == 0 && $reqCancelados == 0)
                        <p class="text-muted fs-5 my-5">No hay requisiciones en este mes.</p>
                    @else
                        <div style="position: relative; height: 300px; width: 100%;">
                            <canvas id="graficaReq"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Colores consistentes con tus estados
        const colorPendiente = '#dc3545'; // Rojo
        const colorEnCurso = '#fd7e14';   // Naranja
        const colorCompletado = '#198754'; // Verde (Corregido el nombre de variable)
        const colorCancelado = '#6c757d'; // Gris

        // ==========================================
        // 1. INICIALIZAR GRÁFICA SERVICIO TÉCNICO
        // ==========================================
        const ctxST = document.getElementById('graficaST');
        if (ctxST) {
            new Chart(ctxST, {
                type: 'doughnut', 
                data: {
                    labels: ['Pendientes', 'Completados', 'Cancelados'],
                    datasets: [{
                        data: [{{ $stPendientes }}, {{ $stCompletados }}, {{ $stCancelados }}],
                        backgroundColor: [colorPendiente, colorCompletado, colorCancelado],
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // ==========================================
        // 2. INICIALIZAR GRÁFICA REQUISICIONES
        // ==========================================
        const ctxReq = document.getElementById('graficaReq');
        if (ctxReq) {
            new Chart(ctxReq, {
                type: 'bar', 
                data: {
                    labels: ['Pendientes', 'En Curso', 'Completadas', 'Canceladas'], // Corregido el Label
                    datasets: [{
                        label: 'Cantidad de Requisiciones',
                        data: [{{ $reqPendientes }}, {{ $reqEnCurso }}, {{ $reqCompletados }}, {{ $reqCancelados }}], // Corregida la variable
                        backgroundColor: [colorPendiente, colorEnCurso, colorCompletado, colorCancelado], // Corregido el color
                        borderRadius: 5, 
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false } 
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 } 
                        }
                    }
                }
            });
        }
    });
</script>
@endsection