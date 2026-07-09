<?php

declare(strict_types=1);

namespace App\Exceptions;

final class ValidationException extends BusinessException
{
    /** @param array<string, string> $errors cle = nom du champ, valeur = message d'erreur */
    public function __construct(
        private readonly array $errors,
        string $message = '',
    ) {
        parent::__construct($message);
    }

    protected function defaultMessage(): string
    {
        return 'Les donnees fournies ne sont pas valides.';
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function context(): array
    {
        return ['errors' => $this->errors];
    }
}