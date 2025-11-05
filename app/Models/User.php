<?php

namespace App\Models;

// IMPORTANT: Importer Carbon pour la gestion des dates
use Carbon\Carbon;
// IMPORTANT: Importer MustVerifyEmail pour l'étape C (vérification d'email)
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Nous ajoutons "implements MustVerifyEmail" (pour l'étape C)
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',      // Nouveau
        'last_name',       // Nouveau
        'email',
        'password',
        'date_of_birth',   // Nouveau
        'phone_number',    // Nouveau
        'address_line1',   // Nouveau
        'postal_code',     // Nouveau
        'city',            // Nouveau
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtenir les attributs qui doivent être castés.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // Indique à Laravel que c'est une date, pour qu'il la gère correctement
            'date_of_birth' => 'date', 
        ];
    }

    /**
     * NOUVEAU: Mutator pour la date de naissance (Format Français).
     * Cette fonction est appelée AUTOMATIQUEMENT avant de sauvegarder en BDD.
     * Elle convertit le format 'JJ/MM/AAAA' (du formulaire) 
     * en 'YYYY-MM-DD' (pour la BDD).
     */
    protected function setDateOfBirthAttribute($value)
    {
        if ($value) {
            $this->attributes['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }
    }

    /**
     * NOUVEAU: Accessor pour la date de naissance (Format Français).
     * Appelée AUTOMATIQUEMENT quand on lit la date depuis la BDD.
     * Elle convertit 'YYYY-MM-DD' (de la BDD) 
     * en 'JJ/MM/AAAA' (pour l'affichage).
     */
    protected function getDateOfBirthAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('d/m/Y');
        }
    }
}