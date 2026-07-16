<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exceptions\ValidationException;

// Valide les données d'inscription avant création d'un compte

final class RegisterValidator
{
    public function validate(array $data): void
    {
        $errors = [];

        $fullName = trim((string) ($data['full_name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($fullName === '') {
            $errors['full_name'] = 'Le nom complet est requis.';
        } elseif (str_contains($fullName, ' ') === false) {
            $errors['full_name'] = 'Veuillez indiquer votre nom complet.';
        }

        if ($email === '') {
            $errors['email'] = 'L’adresse e-mail est requise.';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'L’adresse e-mail n’est pas valide.';
        }

        if ($password === '') {
            $errors['password'] = 'Le mot de passe est requis.';
        } elseif (mb_strlen($password) < 8) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if ($errors !== []) {
            throw new ValidationException('Données d inscription invalides.', $errors);
        }
    }
}