<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Vérifier si une langue est stockée dans la session
        if (Session::has('locale')) {
            // 2. Si oui, forcer Laravel à utiliser cette langue
            App::setLocale(Session::get('locale'));
        }

        // 3. Continuer le chargement de la page
        return $next($request);
    }
}
