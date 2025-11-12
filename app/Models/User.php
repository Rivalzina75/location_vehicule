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
        'first_name',
        'last_name',
        'email',
        'password',
        'date_of_birth',
        'phone_number',
        'address_line1',
        'postal_code',
        'city',

        // -----------------------------------------------------------------
        // AJOUTS POUR LA SÉCURITÉ (Machina Point 4)
        // -----------------------------------------------------------------
        'login_attempts',
        'blocked_until',
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
            'date_of_birth' => 'date',

            // -----------------------------------------------------------------
            // AJOUT POUR LA SÉCURITÉ (Machina Point 4)
            // -----------------------------------------------------------------
            'blocked_until' => 'datetime', // Indique à Laravel que c'est une date
        ];
    }

    /**
     * Mutator pour la date de naissance (Format Français).
     */
    protected function setDateOfBirthAttribute($value)
    {
        if ($value) {
            $this->attributes['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }
    }

    /**
     * Accessor pour la date de naissance (Format Français).
     */
    protected function getDateOfBirthAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('d/m/Y');
        }
    }
}
