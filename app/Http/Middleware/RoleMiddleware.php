<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
{
    // Si el usuario no está logueado o su rol no está en la lista permitida
    if (!auth()->check() || !in_array(auth()->user()->rol, $roles)) {
        abort(403, 'No tienes permisos para acceder a esta sección.');
    }

    return $next($request);
}
}
