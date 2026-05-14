<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\Citizen;
use App\Models\Secretary;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para resolver o usuário correto a partir da sessão.
 * Tenta localizar o usuário em todos os 3 modelos possíveis.
 */
class ResolveMultiAuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se o usuário já está autenticado, apenas continua
        if (Auth::check()) {
            return $next($request);
        }

        // Se há um user_id na sessão mas nenhum usuário está autenticado,
        // tenta localizar o usuário nos 3 modelos possíveis
        if ($request->session()->has('auth.id')) {
            $userId = $request->session()->get('auth.id');

            // Tenta localizar como Admin
            $user = Admin::find($userId);
            if ($user) {
                Auth::login($user, $request->boolean('remember'));
                return $next($request);
            }

            // Tenta localizar como Secretary
            $user = Secretary::find($userId);
            if ($user) {
                Auth::login($user, $request->boolean('remember'));
                return $next($request);
            }

            // Tenta localizar como Citizen
            $user = Citizen::find($userId);
            if ($user) {
                Auth::login($user, $request->boolean('remember'));
                return $next($request);
            }
        }

        return $next($request);
    }
}
