<?php

namespace App\Models;

// IMPORTATIONS
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// --- AJOUT 1 : Importer nos 2 e-mails personnalisés ---
use App\Notifications\VerifyEmail; // <-- MODIFIÉ (C'était VerifyEmailFrench)
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Les attributs qui peuvent être assignés en masse.
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
        'login_attempts',
        'blocked_until',
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtenir les attributs qui doivent être castés.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'blocked_until' => 'datetime',
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

    /**
     * --- UTILISATION 1 : Force Laravel à utiliser notre e-mail de VÉRIFICATION ---
     */
    public function sendEmailVerificationNotification()
    {
        // Utilise la nouvelle classe standardisée
        $this->notify(new VerifyEmail); // <-- MODIFIÉ
    }

    /**
     * --- UTILISATION 2 : Force Laravel à utiliser notre e-mail de RÉINITIALISATION ---
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}
