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
        // Vérifie si l'utilisateur est bloqué et si le temps de blocage n'est pas écoulé
        if ($user && $user->blocked_until && Carbon::now()->lessThan($user->blocked_until)) {
            $remainingSeconds = $user->blocked_until->diffInSeconds(Carbon::now());
            
            // Point 4: Afficher le message de blocage
            throw \Illuminate\Validation\ValidationException::withMessages([
                $this->username() => ["Votre compte est bloqué. Veuillez attendre {$remainingSeconds} secondes avant de réessayer."]
            ]);
        }
    }


    /**
     * Gère une tentative de connexion échouée.
     * (Appelé si le mot de passe est incorrect)
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // Si l'utilisateur est trouvé et non bloqué par la validation initiale
        if ($user) {
            $user->login_attempts++; // Incrémenter le compteur
            
            // Si le nombre d'erreurs atteint ou dépasse 3
            if ($user->login_attempts >= 3) {
                // Point 4: Calcul du temps d'attente croissant
                // 3e tentative échouée => 30s
                // 4e tentative échouée => 45s (30 + 1 * 15)
                // 5e tentative échouée => 60s (30 + 2 * 15)
                $waitSeconds = 30 + (max(0, $user->login_attempts - 3) * 15);
                
                $user->blocked_until = Carbon::now()->addSeconds($waitSeconds);
                
                // Message d'erreur spécifique pour le blocage
                $user->save();
                
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() => "Compte bloqué après tentatives erronées. Veuillez attendre {$waitSeconds} secondes avant de réessayer."
                    ]);
            }
            // Sauvegarder l'incrémentation des tentatives
            $user->save();
        }

        // Retourne la réponse d'échec de connexion par défaut de Laravel
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
        // Si la connexion réussit, réinitialiser le compteur de tentatives (Point 4)
        $user->login_attempts = 0;
        $user->blocked_until = null;
        $user->save();

        return redirect()->intended($this->redirectPath());
    }
}