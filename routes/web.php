<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequisicionController;
use App\Http\Controllers\ServicioTecnicoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Models\Cliente;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Redirigir la raíz al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Autenticación
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'authenticate']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/recuperar-acceso', [App\Http\Controllers\RecuperacionController::class, 'create'])->name('recuperacion.create');
Route::post('/recuperar-acceso', [App\Http\Controllers\RecuperacionController::class, 'store'])->name('recuperacion.store');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren inicio de sesión)
|--------------------------------------------------------------------------
*/

Route::get('/api/clientes/buscar', function (Request $request) {
    $termino = $request->query('q');
    $clientes = Cliente::where('nombre', 'like', "%{$termino}%")->take(5)->get();
    return response()->json($clientes);
})->middleware('auth');

Route::middleware('auth')->group(function () {

// Solo el SuperAdmin gestiona usuarios
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::resource('usuarios', UserController::class);
    });

    // Solo SuperAdmin y Administracion gestionan el catálogo de productos
    Route::middleware('role:SuperAdmin,Administracion')->group(function () {
        Route::resource('productos', ProductoController::class);
    });

    // Servicio Técnico: Todos menos Producción (según tu lógica original)
    Route::middleware('role:SuperAdmin,Administracion,ServicioTecnico,Almacen')->group(function () {
        Route::patch('st/{id}/avanzar', [ServicioTecnicoController::class, 'avanzarStatus'])->name('st.avanzar');
        Route::resource('st', ServicioTecnicoController::class);
    });

    // Requisiciones Normales: Todos pueden verlas
    Route::resource('requisiciones', RequisicionController::class);
    
    // --- MÓDULO DE USUARIOS ---
    Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::get('requisiciones/{id}/reporte', [RequisicionController::class, 'generarReporte'])->name('requisiciones.reporte');
    Route::get('st/{id}/reporte', [ServicioTecnicoController::class, 'generarReporte'])->name('st.reporte');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
    


    // --- MÓDULO DE REQUISICIONES ---
    // ¡Esta única línea crea 7 rutas automáticamente! (index, create, store, show, edit, update, destroy)
    Route::resource('requisiciones', RequisicionController::class);
    Route::resource('st', ServicioTecnicoController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('familias', App\Http\Controllers\FamiliaController::class)->only(['index', 'store', 'destroy']);
    Route::resource('clientes', App\Http\Controllers\ClienteController::class);
    
    // Ruta personalizada adicional para avanzar el estado rápidamente
    Route::patch('requisiciones/{requisicion}/avanzar-estado', [RequisicionController::class, 'avanzarStatus'])
         ->name('requisiciones.avanzar');

});