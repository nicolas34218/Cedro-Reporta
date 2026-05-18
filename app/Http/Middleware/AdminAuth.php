<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para autenticação em painel admin.
 * Aceita tanto Admin quanto Secretary.
 * Se não estiver autenticado, redireciona para login.
 */
class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se está autenticado em algum dos guards (admin ou secretary)
        if (Auth::guard('admin')->check() || Auth::guard('secretary')->check()) {
            return $next($request);
        }

        // Se não está autenticado, redireciona para login
        return redirect()->route('login')
            ->with('error', 'Você precisa estar autenticado para acessar o painel administrativo.');
    }
}
