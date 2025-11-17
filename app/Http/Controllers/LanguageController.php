<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Change la langue de l'application et la stocke en session.
     */
    public function switchLang(Request $request, $locale)
    {
        // 1. Vérifier si la langue demandée est supportée (fr ou en)
        if (in_array($locale, ['en', 'fr'])) {

            // 2. Mettre la langue dans la session
            Session::put('locale', $locale);
        }

        // 3. Rediriger l'utilisateur vers la page précédente
        return redirect()->back();
    }
}
