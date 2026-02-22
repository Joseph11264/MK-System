@extends('layouts.app')

@section('titulo', 'Panel Principal')

@section('content')
<section class="main-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary m-0">📊 Panel de Control General</h2>
        <span class="text-muted fw-bold">{{ date('d M Y, h:i A') }}</span>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow-sm h-100 border-0">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-light opacity-75">Req. Pendientes</h6>
                    <h2 class="display-5 fw-bold mb-0">{{ $kpis['req_pendientes'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark shadow-sm h-100 border-0">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold opacity-75">Tickets ST Pendientes</h6>
                    <h2 class="display-5 fw-bold mb-0">{{ $kpis['st_pendientes'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow-sm h-100 border-0">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-light opacity-75">Trabajos Completados</h6>
                    <h2 class="display-5 fw-bold mb-0">{{ $kpis['req_completadas'] + $kpis['st_completados'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold text-secondary border-bottom-0 pt-3">
                    🔧 Resumen de Servicio Técnico (ST)
                </div>
                <div class="card-body">
                    <canvas id="stChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold text-secondary border-bottom-0 pt-3">
                    📦 Resumen de Requisiciones / Almacén
                </div>
                <div class="card-body">
                    <canvas id="reqChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Datos inyectados desde Laravel
        const dataST = @json($chartData['st']);
        const dataReq = @json($chartData['req']);

        // 1. Gráfica de ST (Torta / Pie)
        new Chart(document.getElementById('stChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'Completados', 'Cancelados'],
                datasets: [{
                    data: dataST,
                    backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // 2. Gráfica de Requisiciones (Barras)
        new Chart(document.getElementById('reqChart'), {
            type: 'bar',
            data: {
                labels: ['Pendientes', 'En Curso', 'Completadas', 'Canceladas'],
                datasets: [{
                    label: 'Cantidad de Órdenes',
                    data: dataReq,
                    backgroundColor: ['#0d6efd', '#0dcaf0', '#198754', '#dc3545'],
                    borderRadius: 5
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    });
</script>
@endsection