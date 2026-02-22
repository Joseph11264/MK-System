<?php

namespace App\Http\Controllers;

use App\Models\Requisicion;
use App\Models\RequisicionSt;
use Illuminate\Http\Request;

class DashboardController
{
    public function index()
    {
        // 1. Tarjetas de Resumen (KPIs)
        $kpis = [
            'req_pendientes' => Requisicion::where('status', 'Pendiente')->count(),
            'req_completadas' => Requisicion::where('status', 'Completado')->count(),
            'st_pendientes' => RequisicionSt::where('status', 'Pendiente')->count(),
            'st_completados' => RequisicionSt::where('status', 'Completado')->count(),
            // Calculamos el dinero recaudado este mes por reparaciones cobradas
            'ingresos_mes' => RequisicionSt::where('estado_pago', 'Pagado')
                                           ->whereMonth('created_at', date('m'))
                                           ->sum('precio_reparacion'),
        ];

        // 2. Datos para Gráficas (Agrupados por estado)
        $stStatus = RequisicionSt::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->toArray();
        $reqStatus = Requisicion::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->toArray();

        // Rellenamos con 0 si algún estado no existe aún en la BD para que la gráfica no falle
        $chartData = [
            'st' => [
                $stStatus['Pendiente'] ?? 0, 
                $stStatus['Completado'] ?? 0, 
                $stStatus['Cancelado'] ?? 0
            ],
            'req' => [
                $reqStatus['Pendiente'] ?? 0, 
                $reqStatus['En Curso'] ?? 0, 
                $reqStatus['Completado'] ?? 0, 
                $reqStatus['Cancelado'] ?? 0
            ]
        ];

        return view('dashboard', compact('kpis', 'chartData'));
    }
}