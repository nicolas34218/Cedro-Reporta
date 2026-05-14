<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\Secretary;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Verifica se está autenticado como Admin ou Secretary
        if ((Auth::guard('admin')->check() && Auth::guard('admin')->user() instanceof Admin) ||
            (Auth::guard('secretary')->check() && Auth::guard('secretary')->user() instanceof Secretary)) {
            return $next($request);
        }

        abort(403, 'Acesso não autorizado ao painel administrativo.');
    }
}
