<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\PasswordRobustness;
use Illuminate\Foundation\Auth\ResetsPasswords; // On garde cet import

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/dashboard';

    // On garde cette méthode pour la sécurité
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                new PasswordRobustness,
            ],
        ];
    }

    // (Il ne doit plus y avoir la méthode validationErrorMessages() ici)
}
