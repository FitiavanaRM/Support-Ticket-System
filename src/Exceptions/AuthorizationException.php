<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Distingue l'absence d'autorisation d'un défaut d'authentification afin que
 * les contrôleurs puissent renvoyer le statut HTTP adapté sans logique dupliquée.
 */
final class AuthorizationException extends BusinessException
{
    public function httpStatusCode(): int
    {
        return 403;
    }

    protected function defaultMessage(): string
    {
        return 'Vous n’êtes pas autorisé à effectuer cette action.';
    }
}
