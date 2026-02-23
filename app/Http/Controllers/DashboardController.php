<?php

namespace App\Http\Controllers;

use App\Models\Requisicion;
use App\Models\RequisicionSt;
use Illuminate\Http\Request;

class DashboardController
{
    public function index(Request $request)
    {
        // 1. Recibir los parámetros de mes y año (por defecto el actual)
        $mes = $request->input('mes', date('m'));
        $anio = $request->input('anio', date('Y'));

        // =========================================================
        // 2. DATOS DE SERVICIO TÉCNICO (ST) PARA EL MES SELECCIONADO
        // =========================================================
        $stQuery = RequisicionSt::whereYear('created_at', $anio)->whereMonth('created_at', $mes);
        
        $stPendientes = (clone $stQuery)->where('status', 'Pendiente')->count();
        $stCompletados = (clone $stQuery)->where('status', 'Completado')->count();
        $stCancelados = (clone $stQuery)->where('status', 'Cancelado')->count();

        // =========================================================
        // 3. DATOS DE REQUISICIONES NORMALES PARA EL MES SELECCIONADO
        // =========================================================
        $reqQuery = Requisicion::whereYear('created_at', $anio)->whereMonth('created_at', $mes);
        
        $reqPendientes = (clone $reqQuery)->where('status', 'Pendiente')->count();
        $reqEnCurso = (clone $reqQuery)->where('status', 'En Curso')->count();
        $reqCompletados = (clone $reqQuery)->where('status', 'Completado')->count(); 
        $reqCancelados = (clone $reqQuery)->where('status', 'Cancelado')->count();

        // Nombres de los meses para la vista
        $nombresMeses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        return view('dashboard', compact(
            'mes', 'anio', 'nombresMeses',
            'stPendientes', 'stCompletados', 'stCancelados',
            'reqPendientes', 'reqEnCurso', 'reqCompletados', 'reqCancelados'
        ));
    }
}