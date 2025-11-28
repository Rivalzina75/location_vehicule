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

    /**
     * Show the login form - check and clear expired lockout
     */
    public function showLoginForm()
    {
        // Check if lockout has expired and clear it
        if (session()->has('lockout_until')) {
            $lockoutUntil = session('lockout_until');
            if (Carbon::now()->timestamp >= $lockoutUntil) {
                session()->forget(['lockout_until', 'lockout_email']);
            }
        }

        return view('auth.login');
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where($this->username(), $request->input($this->username()))->first();

        // Check if user is currently blocked
        if ($user && $user->blocked_until && Carbon::now()->lessThan($user->blocked_until)) {
            // Store timestamp in PERSISTENT session (survives language changes)
            $lockoutTimestamp = $user->blocked_until->timestamp;
            session(['lockout_until' => $lockoutTimestamp]);
            session(['lockout_email' => $request->input($this->username())]);

            // Use translation key - countdown handled by JavaScript
            throw \Illuminate\Validation\ValidationException::withMessages([
                $this->username() => [__('lockout_message')]
            ]);
        }
    }

    /**
     * Handle a failed login attempt.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // === MEMORY LOGIC (5 MINUTES) ===
            // Reset attempts after 5 minutes of no errors
            $bufferTime = 5;

            if ($user->blocked_until) {
                // Case 1: Coming out of a block
                if ($user->blocked_until->copy()->addMinutes($bufferTime)->isPast()) {
                    $user->login_attempts = 0;
                    $user->blocked_until = null;
                }
            } elseif ($user->login_attempts > 0) {
                // Case 2: Not blocked but has previous attempts
                if ($user->updated_at->copy()->addMinutes($bufferTime)->isPast()) {
                    $user->login_attempts = 0;
                }
            }

            $user->login_attempts++;

            // Block after 3 failed attempts
            if ($user->login_attempts >= 3) {
                // Progressive wait: 30s, 45s, 60s, 75s...
                $waitSeconds = 30 + (max(0, $user->login_attempts - 3) * 15);

                $user->blocked_until = Carbon::now()->addSeconds($waitSeconds);

                // Store in PERSISTENT session
                $lockoutTimestamp = $user->blocked_until->timestamp;
                session(['lockout_until' => $lockoutTimestamp]);
                session(['lockout_email' => $request->email]);

                $user->save();

                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() => __('lockout_message')
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

    /**
     * The user has been authenticated - reset lockout.
     */
    protected function authenticated(Request $request, $user)
    {
        // Reset attempts on successful login
        $user->login_attempts = 0;
        $user->blocked_until = null;
        $user->save();

        // Clear lockout session
        session()->forget(['lockout_until', 'lockout_email']);

        return redirect()->intended($this->redirectPath());
    }
}
