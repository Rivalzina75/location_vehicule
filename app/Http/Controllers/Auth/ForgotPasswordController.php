<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any functions that require customization.
    |
    */

    use SendsPasswordResetEmails;

    // Les méthodes 'sendResetLinkFailedResponse' et 'sendResetLinkResponse'
    // que nous avions ajoutées ont été supprimées.
    //
    // Laravel va maintenant utiliser automatiquement les traductions
    // de votre fichier 'lang/fr/passwords.php'.
}
