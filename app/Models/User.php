<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Le decimos a Laravel en qué tabla buscar (opcional si la tabla se llama 'users', 
    // pero como la tuya se llamará 'usuarios', lo especificamos).
    protected $table = 'usuarios';

    // Los campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre',
        'username',
        'password',
        'rol',
    ];

    // Ocultamos la contraseña para que nunca viaje en las respuestas de la API o arrays
    protected $hidden = [
        'password',
    ];

    // Laravel 11 encriptará la contraseña automáticamente al guardarla
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
