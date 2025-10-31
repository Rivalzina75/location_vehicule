<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class PasswordRobustness implements ValidationRule
{
    /**
     * Exécute la règle de validation.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $errors = [];

        if (strlen($value) < 14) {
            $errors[] = "avoir une longueur d'au moins 14 caractères";
        }
        if (!preg_match('/[a-z]/', $value)) {
            $errors[] = "contenir au moins une minuscule";
        }
        if (!preg_match('/[A-Z]/', $value)) {
            $errors[] = "contenir au moins une majuscule";
        }
        if (!preg_match('/[0-9]/', $value)) {
            $errors[] = "contenir au moins un chiffre";
        }
        if (!preg_match('/[^a-zA-Z0-9\s]/', $value)) {
            $errors[] = "contenir au moins un caractère spécial";
        }

        if (!empty($errors)) {
            $message = "Le mot de passe ne respecte pas les critères. Il doit : " . implode(', ', $errors) . ".";
            $fail($message);
        }
        
    }
}