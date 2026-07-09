<?php

declare(strict_types=1);

namespace App\Exceptions;

//  classe pour gerer les erreurs
use Exception;
use Throwable;

abstract class BusinessException extends Exception
{
    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct(
            $message !== '' ? $message : $this->defaultMessage(),
            previous: $previous
        );
    }

    abstract public function httpStatusCode(): int;

    protected function defaultMessage(): string
    {
        return 'Une erreur metier est survenue.';
    }

    /** @return array<string, mixed> */
    public function context(): array
    {
        return [];
    }
}