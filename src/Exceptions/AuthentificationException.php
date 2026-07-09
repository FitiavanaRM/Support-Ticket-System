<?php

declare(strict_types=1);

namespace App\Exceptions;

final class AuthenticationException extends BusinessException
{
    public function httpStatusCode(): int
    {
        return 401;
    }

    protected function defaultMessage(): string
    {
        return 'Authentification requise ou identifiants invalides.';
    }
}