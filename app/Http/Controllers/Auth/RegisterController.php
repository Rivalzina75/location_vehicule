<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// N'oubliez pas d'importer votre règle de mot de passe !
use App\Rules\PasswordRobustness; 

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | Ce contrôleur gère l'inscription des nouveaux utilisateurs ainsi que
    | leur validation et création.
    |
    */

    use RegistersUsers;

    /**
     * Où rediriger les utilisateurs après l'inscription.
     *
     * @var string
     */
    // Corrigé pour pointer vers notre nouveau tableau de bord
    protected $redirectTo = '/dashboard'; 

    /**
     * Créer une nouvelle instance de contrôleur.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Obtenir un validateur pour une demande d'inscription entrante.
     * (MIS À JOUR AVEC LES NOUVEAUX CHAMPS)
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            // Validation pour les nouveaux champs
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            
            // Validation pour le format de date français JJ/MM/AAAA
            'date_of_birth' => ['required', 'date_format:d/m/Y'], 
            
            'phone_number' => ['required', 'string', 'max:20'], // Simple validation
            'address_line1' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:255'],

            // Validation du mot de passe (utilise votre règle)
            'password' => ['required', 'string', 'confirmed', new PasswordRobustness],
        ]);
    }

    /**
     * Créer une nouvelle instance d'utilisateur après une inscription valide.
     * (MIS À JOUR AVEC LES NOUVEAUX CHAMPS)
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            
            // Le Mutator dans User.php s'occupe de la conversion JJ/MM/AAAA
            'date_of_birth' => $data['date_of_birth'], 
            
            'phone_number' => $data['phone_number'],
            'address_line1' => $data['address_line1'],
            'postal_code' => $data['postal_code'],
            'city' => $data['city'],
            
            // Laravel s'occupe du hachage
            'password' => Hash::make($data['password']), 
        ]);
    }
}