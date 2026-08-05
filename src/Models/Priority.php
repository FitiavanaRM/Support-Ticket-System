<?php

declare(strict_types=1);

namespace App\Models;

// Représente le niveau de priorité d'un ticket.
final class Priority
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly int $level,
    ) {
    }

    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            id: (int) ($row['id'] ?? 0),
            name: (string) ($row['name'] ?? ''),
            level: (int) ($row['level'] ?? 0),
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

    public function level(): int
    {
        return $this->level;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
        ];
    }
}
