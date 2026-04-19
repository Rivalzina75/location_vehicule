<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  Les rôles autorisés (ex: 'admin', 'client')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Vérifier que l'utilisateur est connecté
        if (! $request->user()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier que l'utilisateur a un des rôles autorisés
        if (! in_array($request->user()->role, $roles)) {
            abort(403, 'Accès non autorisé. Cette page est réservée aux '.implode(' ou ', $roles).'.');
        }

        return $next($request);
    }
}
