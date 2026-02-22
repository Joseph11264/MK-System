<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RecuperacionController
{
    public function create()
    {
        return view('auth.recuperacion');
    }

    public function store(Request $request)
    {
        // Añadimos la validación de la nueva contraseña
        $request->validate([
            'username' => 'required|string',
            'nombre' => 'required|string',
            'password' => 'required|string|min:6|confirmed' // Requiere un campo 'password_confirmation'
        ], [
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        // Buscamos que el usuario y el nombre exacto coincidan
        $user = User::where('username', $request->username)
                    ->where('nombre', $request->nombre)
                    ->first();

        if (!$user) {
            return back()->with('error', '⚠️ Los datos proporcionados no coinciden con ningún usuario registrado.');
        }

        // Actualizamos la clave con la que el usuario acaba de escribir
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Borramos los intentos fallidos del login
        $request->session()->forget('login_attempts');

        // Redirigimos al login con un mensaje de éxito normal
        return redirect()->route('login')->with('success', '✅ Contraseña actualizada correctamente. Ya puedes iniciar sesión.');
    }
}