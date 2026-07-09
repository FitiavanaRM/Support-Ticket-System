<?php

declare(strict_types=1);

namespace App\Exceptions;

class StateException extends BusinessException
{
    public function httpStatusCode(): int
    {
        return 422;
    }

    protected function defaultMessage(): string
    {
        return "L'operation demandee n'est pas compatible avec l'etat actuel du ticket.";
    }
}