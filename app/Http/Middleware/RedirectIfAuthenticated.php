<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que redireciona usuários autenticados para suas respectivas áreas.
 * Usado em rotas 'guest' (login, register) para evitar que usuários logados acessem essas páginas.
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se está autenticado como admin
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        // Se está autenticado como secretary
        if (auth('secretary')->check()) {
            return redirect()->route('secretary.dashboard');
        }
        
        // Se está autenticado como citizen
        if (auth('citizen')->check()) {
            return redirect()->route('citizen.home');
        }

        return $next($request);
    }
}
