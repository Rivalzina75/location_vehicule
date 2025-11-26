<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\PasswordRobustness;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     * CHANGEMENT : On redirige vers le dashboard protégé.
     * Comme il est protégé par 'verified', l'utilisateur sera
     * intercepté et envoyé vers la page de vérification d'email.
     */
    protected $redirectTo = '/dashboard';

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
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'date_of_birth' => ['nullable', 'date_format:d/m/Y'],
            'phone_number' => ['nullable', 'regex:/^0[1-9]([ .-]?[0-9]{2}){4}$/'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:255'],
            'password' => [
                'required',
                'string',
                new PasswordRobustness,
                'confirmed'
            ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'address_line1' => $data['address_line1'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'city' => $data['city'] ?? null,
            'password' => Hash::make($data['password']),
        ]);
    }
}
