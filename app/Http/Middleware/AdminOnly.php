<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar acesso ao painel administrativo.
 *
 * Valida se o usuário é Admin ou Secretário.
 */
class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->canAccessAdminPanel()) {
            abort(403, 'Acesso não autorizado ao painel administrativo.');
        }

        return $next($request);
    }
}
