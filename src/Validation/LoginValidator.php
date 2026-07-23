<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exceptions\ValidationException;

/** Valide la forme de la connexion avant toute consultation de la base. */
final class LoginValidator
{
    public function validate(array $data): void
    {
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($email === '') {
            throw new ValidationException('Données de connexion invalides.', ['email' => 'L’adresse e-mail est requise.']);
        }

        if ($password === '') {
            throw new ValidationException('Données de connexion invalides.', ['password' => 'Le mot de passe est requis.']);
        }
    }
}
