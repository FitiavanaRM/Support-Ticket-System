<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exceptions\ValidationException;

/** Valide l'inscription sans accumuler d'erreurs indépendantes. */
final class RegisterValidator
{
    public function validate(array $data): void
    {
        $fullName = trim((string) ($data['full_name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($fullName === '') {
            throw new ValidationException('Données d’inscription invalides.', ['full_name' => 'Le nom complet est requis.']);
        }

        if (str_contains($fullName, ' ') === false) {
            throw new ValidationException('Données d’inscription invalides.', ['full_name' => 'Veuillez indiquer votre nom complet.']);
        }

        if ($email === '') {
            throw new ValidationException('Données d’inscription invalides.', ['email' => 'L’adresse e-mail est requise.']);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new ValidationException('Données d’inscription invalides.', ['email' => 'L’adresse e-mail n’est pas valide.']);
        }

        if ($password === '') {
            throw new ValidationException('Données d’inscription invalides.', ['password' => 'Le mot de passe est requis.']);
        }

        if (mb_strlen($password) < 8) {
            throw new ValidationException('Données d’inscription invalides.', ['password' => 'Le mot de passe doit contenir au moins 8 caractères.']);
        }
    }
}
