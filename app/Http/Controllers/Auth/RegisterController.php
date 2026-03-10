<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\PasswordRobustness;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'date_of_birth' => ['required', 'date_format:d/m/Y', 'before:today', 'after:1900-01-01'],
            'phone_number' => ['required', 'string', 'regex:/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/'],
            'address_line1' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'regex:/^[0-9]{5}$/'],
            'city' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'password' => ['required', 'string', new PasswordRobustness, 'confirmed'],
        ], [
            // Custom error messages
            'date_of_birth.date_format' => __('Le format de date doit être JJ/MM/AAAA.'),
            'date_of_birth.before' => __('La date de naissance doit être dans le passé.'),
            'phone_number.regex' => __('Le numéro de téléphone n\'est pas au bon format.'),
            'postal_code.regex' => __('Le code postal doit contenir 5 chiffres.'),
            'city.regex' => __('Le nom de la ville ne doit contenir que des lettres, des espaces ou des tirets.'),
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
            'password' => Hash::make($data['password']),
            'date_of_birth' => $data['date_of_birth'],
            'phone_number' => $data['phone_number'],
            'address_line1' => trim($data['address_line1']),
            'postal_code' => $data['postal_code'],
            'city' => trim($data['city']),
            'role' => 'client', // Par défaut, tous les nouveaux utilisateurs sont des clients
        ]);
    }
}
