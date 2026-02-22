<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController
{
    // --- AUTENTICACIÓN ---

    public function showLoginForm()
    {
        return view('auth.login'); // Retornará tu vista de login
    }

    public function authenticate(Request $request)
    {
        // Validación rápida
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Auth::attempt verifica si el usuario existe y si el hash de la contraseña coincide
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Evita ataques de fijación de sesión
            return redirect()->intended('/requisiciones'); 
        }

        // Si falla, lo devuelve al login con un mensaje de error que Bootstrap mostrará
        return back()->withErrors([
            'username' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Regenera el token CSRF por seguridad
        return redirect('/login');
    }

    // --- GESTIÓN DE USUARIOS (CRUD) ---

    public function index()
    {
        $this->authorizeSuperAdmin(); // Tu antiguo checkRoleAccess

        // Obtiene todos los usuarios ordenados por ID
        $usuarios = User::orderBy('id', 'asc')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        // Si el 'username' ya existe, Laravel redirige automáticamente con un error
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'username' => 'required|string|max:50|unique:usuarios,username',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:SuperAdmin,Administracion,ServicioTecnico,Almacen,Produccion',
        ]);

        // Se crea el usuario (la contraseña se hashea automáticamente gracias al Modelo)
        User::create($validated);

        // Flash message para tu alerta de Bootstrap
        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario registrado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'username' => 'required|string|max:50|unique:usuarios,username,' . $usuario->id, // Ignora el ID actual
            'rol' => 'required|in:SuperAdmin,Administracion,ServicioTecnico,Almacen,Produccion',
            'password' => 'nullable|string|min:6', // Nullable: solo valida si se envía
        ]);

        // Si no se envió contraseña, la quitamos del arreglo para no sobreescribirla vacía
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $usuario->update($validated);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    // Método privado temporal para emular tu checkRoleAccess
    private function authorizeSuperAdmin()
    {
        abort_unless(Auth::check() && Auth::user()->rol === 'SuperAdmin', 403, 'Acceso denegado.');
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }
}