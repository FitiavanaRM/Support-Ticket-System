<?php

declare(strict_types=1);

namespace App\Exceptions;

// erreur liée aux tickets

class TicketException extends BusinessException
{
    public function httpStatusCode(): int
    {
        return 400;
    }

    protected function defaultMessage(): string
    {
        return 'Une erreur est survenue sur ce ticket.';
    }
}