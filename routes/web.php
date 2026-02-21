<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Rutas Públicas
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'authenticate']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Rutas Protegidas (Solo usuarios que hayan iniciado sesión)
Route::middleware('auth')->group(function () {
    
    // Gestión de Usuarios
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');

    // Aquí irán las rutas de Requisiciones y Productos más adelante...
});