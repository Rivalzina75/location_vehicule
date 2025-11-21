<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Importer le Modèle User
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Carbon\Carbon; // Outil de gestion des dates et heures

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard'; // Redirection après connexion

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Surcharge la vérification de l'utilisateur (appelé AVANT la tentative de connexion).
     */
    protected function validateLogin(Request $request)
    {
        // 1. Validation de base
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Récupérer l'utilisateur par e-mail
        $user = User::where($this->username(), $request->input($this->username()))->first();

        // -------------------------
        // LOGIQUE DE VÉRIFICATION DU BLOCAGE
        // -------------------------
        if ($user && $user->blocked_until && Carbon::now()->lessThan($user->blocked_until)) {
            $remainingSeconds = $user->blocked_until->diffInSeconds(Carbon::now());

            // AJOUT POUR LE COMPTEUR JS
            session()->flash('lockout_time', $remainingSeconds);

            // Point 4: Afficher le message de blocage (qui sera traduit par lang/fr/passwords.php)
            throw \Illuminate\Validation\ValidationException::withMessages([
                $this->username() => ["Votre compte est bloqué. Veuillez attendre {$remainingSeconds} secondes avant de réessayer."]
            ]);
        }
    }


    /**
     * Gère une tentative de connexion échouée.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->login_attempts++;

            if ($user->login_attempts >= 3) {
                $waitSeconds = 30 + (max(0, $user->login_attempts - 3) * 15);
                $user->blocked_until = Carbon::now()->addSeconds($waitSeconds);

                // AJOUT POUR LE COMPTEUR JS
                session()->flash('lockout_time', $waitSeconds);

                $user->save();

                // Le message d'erreur sera traduit par lang/fr/auth.php
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() => __('throttle_message', ['seconds' => $waitSeconds])
                    ]);
            }
            $user->save();
        }

        // ---------------------------------------------
        // CORRECTION : On remet la clé de traduction
        // ---------------------------------------------
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('auth.failed'),
            ]);
    }

    /**
     * Gère la réponse après un succès de connexion.
     */
    protected function authenticated(Request $request, $user)
    {
        $user->login_attempts = 0;
        $user->blocked_until = null;
        $user->save();

        return redirect()->intended($this->redirectPath());
    }
}
