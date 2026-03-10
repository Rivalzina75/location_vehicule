<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordRobustness implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Longueur minimale (14 caractères)
        if (strlen($value) < 14) {
            $fail(__('validation.password_min_length')); // Utilise la clé de traduction

            return;
        }

        // 2. Doit contenir une majuscule
        if (! preg_match('/[A-Z]/', $value)) {
            $fail(__('validation.password_uppercase'));

            return;
        }

        // 3. Doit contenir une minuscule
        if (! preg_match('/[a-z]/', $value)) {
            $fail(__('validation.password_lowercase'));

            return;
        }

        // 4. Doit contenir un chiffre
        if (! preg_match('/[0-9]/', $value)) {
            $fail(__('validation.password_number'));

            return;
        }

        // 5. Doit contenir un caractère spécial
        if (! preg_match('/[@$!%*#?&\W_]/', $value)) {
            $fail(__('validation.password_special'));

            return;
        }
    }
}
