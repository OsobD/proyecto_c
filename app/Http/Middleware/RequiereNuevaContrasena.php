<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiereNuevaContrasena
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado y debe cambiar su contraseña
        if (auth()->check() && auth()->user()->debe_cambiar_contrasena) {
            // Permitir acceso solo a la ruta de cambiar contraseña y logout
            if (!$request->is('cambiar-contrasena') && !$request->is('logout')) {
                return redirect()->route('cambiar-contrasena')
                    ->with('warning', 'Debes cambiar tu contraseña antes de continuar.');
            }
        }

        return $next($request);
    }
}
