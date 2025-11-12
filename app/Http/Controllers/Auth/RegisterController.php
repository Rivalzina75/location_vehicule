<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\PasswordRobustness; // Importation de la règle de sécurité

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     * Redirection vers la racine (page d'accueil)
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     * MODIFIÉ POUR VOS CHAMPS (ET CORRIGÉ)
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            // Champs personnalisés
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            // -----------------------------------------------------------------
            // CORRECTION DE LA SYNTAXE (cause de l'erreur 500)
            // -----------------------------------------------------------------
            'date_of_birth' => ['nullable', 'date_format:d/m/Y'],
            'phone_number' => ['nullable', 'regex:/^0[1-9]([ .-]?[0-9]{2}){4}$/'],
            // -----------------------------------------------------------------

            'address_line1' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:255'],

            // Règle de sécurité Machina (Point 1, 2, 3)
            'password' => [
                'required',
                'string',
                new PasswordRobustness,
                'confirmed' // Vérifie password_confirmation
            ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * MODIFIÉ POUR VOS CHAMPS
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Enregistrement des champs personnalisés
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'address_line1' => $data['address_line1'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'city' => $data['city'] ?? null,

            // Hachage du MDP (Point 2)
            'password' => Hash::make($data['password']),
        ]);
    }
}
