<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class PasswordRobustness implements ValidationRule
{
    /**
     * Exécute la règle de validation.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $messages = []; // On prépare un tableau pour les erreurs

        // 1. Longueur 14
        if (strlen($value) < 14) {
            $messages[] = '- au moins 14 caractères';
        }
        // 2. Minuscule
        if (!preg_match('/[a-z]/', $value)) {
            $messages[] = '- au moins une minuscule';
        }
        // 3. Majuscule
        if (!preg_match('/[A-Z]/', $value)) {
            $messages[] = '- au moins une majuscule';
        }
        // 4. Chiffre
        if (!preg_match('/[0-9]/', $value)) {
            $messages[] = '- au moins un chiffre';
        }
        // 5. Caractère spécial
        if (!preg_match('/[\W_]/', $value)) {
            $messages[] = '- au moins un caractère spécial';
        }

        // Si on a trouvé des erreurs...
        if (!empty($messages)) {

            // -----------------------------------------------------------------
            // CORRECTION : On construit un message HTML avec des sauts de ligne
            // -----------------------------------------------------------------
            $htmlMessage = 'Il vous manque :<br>' . implode('<br>', $messages);

            // On envoie le message HTML
            $fail($htmlMessage);
        }
    }
}
