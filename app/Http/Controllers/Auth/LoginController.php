<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where($this->username(), $request->input($this->username()))->first();

        // Blocage strict si la date de fin est dans le futur
        if ($user && $user->blocked_until && Carbon::now()->lessThan($user->blocked_until)) {
            $remainingSeconds = $user->blocked_until->diffInSeconds(Carbon::now());
            session()->flash('lockout_time', $remainingSeconds);

            throw \Illuminate\Validation\ValidationException::withMessages([
                $this->username() => ["Compte bloqué. Attendez {$remainingSeconds} secondes."]
            ]);
        }
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // === LOGIQUE DE MÉMOIRE (5 MINUTES) ===
            // Si on ne fait pas d'erreur pendant 5 minutes, on efface l'ardoise.
            $bufferTime = 5;

            if ($user->blocked_until) {
                // CAS 1 : Il sort d'un blocage.
                // Si la fin du blocage date de plus de 5 min -> Reset.
                if ($user->blocked_until->copy()->addMinutes($bufferTime)->isPast()) {
                    $user->login_attempts = 0;
                    $user->blocked_until = null;
                }
            } elseif ($user->login_attempts > 0) {
                // CAS 2 : Il n'était pas bloqué (juste 1 ou 2 erreurs).
                // Si la dernière tentative date de plus de 5 min -> Reset.
                if ($user->updated_at->copy()->addMinutes($bufferTime)->isPast()) {
                    $user->login_attempts = 0;
                }
            }
            // ======================================

            $user->login_attempts++;

            if ($user->login_attempts >= 3) {
                // Calcul progressif : 30s, 45s, 60s...
                $waitSeconds = 30 + (max(0, $user->login_attempts - 3) * 15);

                $user->blocked_until = Carbon::now()->addSeconds($waitSeconds);
                session()->flash('lockout_time', $waitSeconds);
                $user->save();

                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() => __('Trop de tentatives. Réessayez dans :seconds secondes.', ['seconds' => $waitSeconds])
                    ]);
            }

            $user->save();
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('auth.failed'),
            ]);
    }

    protected function authenticated(Request $request, $user)
    {
        $user->login_attempts = 0;
        $user->blocked_until = null;
        $user->save();
        return redirect()->intended($this->redirectPath());
    }
}
