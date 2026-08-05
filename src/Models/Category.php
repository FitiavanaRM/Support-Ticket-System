<?php

declare(strict_types=1);

namespace App\Models;

// Représente une catégorie de ticket de support.
final class Category
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly ?string $description = null,
    ) {
    }

    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            id: (int) ($row['id'] ?? 0),
            name: (string) ($row['name'] ?? ''),
            description: isset($row['description']) && $row['description'] !== null
                ? (string) $row['description']
                : null,
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
