<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequisicionController;

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

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren inicio de sesión)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // --- MÓDULO DE USUARIOS ---
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');

    // --- MÓDULO DE REQUISICIONES ---
    // ¡Esta única línea crea 7 rutas automáticamente! (index, create, store, show, edit, update, destroy)
    Route::resource('requisiciones', RequisicionController::class);
    
    // Ruta personalizada adicional para avanzar el estado rápidamente
    Route::patch('requisiciones/{requisicion}/avanzar-estado', [RequisicionController::class, 'avanzarStatus'])
         ->name('requisiciones.avanzar');

});