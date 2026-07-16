<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exceptions\ValidationException;

// Valide les données de connexion.
final class LoginValidator
{
    public function validate(array $data): void
    {
        $errors = [];
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($email === '') {
            $errors['email'] = 'L’adresse e-mail est requise.';
        }

        if ($password === '') {
            $errors['password'] = 'Le mot de passe est requis.';
        }

        if ($errors !== []) {
            throw new ValidationException('Données de connexion invalides.', $errors);
        }
    }
}