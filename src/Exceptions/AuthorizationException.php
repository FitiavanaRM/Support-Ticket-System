<?php

declare(strict_types=1);

namespace App\Exceptions;

// renvoye le statut http 
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
