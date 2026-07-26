<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exceptions\ValidationException;

// valide les informations du creation d'un ticket
final class TicketValidator
{
    /** @param array<string, mixed> $data */
    public function validateForCreation(array $data): void
    {
        $subject = $this->requiredText($data, 'subject', 'Le sujet est requis.');
        if (mb_strlen($subject) > 150) {
            throw new ValidationException('Données de ticket invalides.', [
                'subject' => 'Le sujet ne doit pas dépasser 150 caractères.',
            ]);
        }

        $this->requiredText($data, 'description', 'La description est requise.');
        $this->positiveInteger($data, 'category_id', 'La catégorie sélectionnée est invalide.');
        $this->positiveInteger($data, 'priority_id', 'La priorité sélectionnée est invalide.');
    }

    /** @param array<string, mixed> $data */
    private function requiredText(array $data, string $field, string $message): string
    {
        $value = $data[$field] ?? null;

        if (!is_string($value) || trim($value) === '') {
            throw new ValidationException('Données de ticket invalides.', [$field => $message]);
        }

        return trim($value);
    }

    /** @param array<string, mixed> $data */
    private function positiveInteger(array $data, string $field, string $message): int
    {
        $value = $data[$field] ?? null;
        $isPositiveInteger = is_int($value) || (is_string($value) && ctype_digit($value));

        if (!$isPositiveInteger || (int) $value < 1) {
            throw new ValidationException('Données de ticket invalides.', [$field => $message]);
        }

        return (int) $value;
    }
}