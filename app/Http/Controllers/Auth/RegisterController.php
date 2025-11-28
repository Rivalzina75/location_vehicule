<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
     * Redirects to dashboard (protected by 'verified' middleware)
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
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
            'date_of_birth' => ['required', 'date_format:d/m/Y', 'before:today', 'after:1900-01-01'],
            'phone_number' => ['required', 'regex:/^0[1-9]([ .-]?[0-9]{2}){4}$/'],
            'address_line1' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'regex:/^[0-9]{5}$/'],
            'city' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', new PasswordRobustness, 'confirmed'],
        ], [
            // Custom error messages
            'date_of_birth.date_format' => __('Le format de date doit être JJ/MM/AAAA.'),
            'date_of_birth.before' => __('La date de naissance doit être dans le passé.'),
            'phone_number.regex' => __('Le numéro de téléphone doit être au format français (ex: 06 12 34 56 78).'),
            'postal_code.regex' => __('Le code postal doit contenir 5 chiffres.'),
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'email' => strtolower(trim($data['email'])),
            'date_of_birth' => $data['date_of_birth'],
            'phone_number' => $data['phone_number'],
            'address_line1' => trim($data['address_line1']),
            'postal_code' => $data['postal_code'],
            'city' => trim($data['city']),
            'password' => Hash::make($data['password']),
        ]);
    }
}
