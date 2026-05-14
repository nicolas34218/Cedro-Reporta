<?php

namespace App\Http\Middleware;

use App\Models\Citizen;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CitizenOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se está autenticado como Citizen
        if (Auth::guard('citizen')->check() && Auth::guard('citizen')->user() instanceof Citizen) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
