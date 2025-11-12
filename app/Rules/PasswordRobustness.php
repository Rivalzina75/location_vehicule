<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordRobustness implements Rule
{
    protected $messages = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Réinitialiser les messages à chaque validation
        $this->messages = [];

        // 1. Longueur minimum 14 caractères
        if (strlen($value) < 14) {
            $this->messages[] = 'doit contenir au moins 14 caractères';
        }
        // 2. Une minuscule
        if (!preg_match('/[a-z]/', $value)) {
            $this->messages[] = 'doit contenir au moins une minuscule';
        }
        // 3. Une majuscule
        if (!preg_match('/[A-Z]/', $value)) {
            $this->messages[] = 'doit contenir au moins une majuscule';
        }
        // 4. Un chiffre
        if (!preg_match('/[0-9]/', $value)) {
            $this->messages[] = 'doit contenir au moins un chiffre';
        }
        // 5. Un caractère spécial
        if (!preg_match('/[\W_]/', $value)) { // \W (non-mot) ou _ (underscore)
            $this->messages[] = 'doit contenir au moins un caractère spécial';
        }

        return empty($this->messages);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // Retourne la liste détaillée des problèmes (Machina Point 3)
        return 'Le mot de passe ' . implode(', et ', $this->messages) . '.';
    }
}
