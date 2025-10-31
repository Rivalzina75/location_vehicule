<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// Ancienne référence RouteServiceProvider supprimée
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\PasswordRobustness; // Votre règle de robustesse

class RegisterController extends Controller
{
    use RegistersUsers;

    // Redirection après inscription (corrigé pour ne pas utiliser RouteServiceProvider)
    protected $redirectTo = '/home'; 

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Obtient un validateur approprié pour les requêtes d'enregistrement entrantes.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // --- Notre règle de robustesse ---
            'password' => ['required', 'string', 'confirmed', new PasswordRobustness], 
            // ---------------------------------
        ]);
    }

    /**
     * Crée une nouvelle instance d'utilisateur après une validation réussie.
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
