<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

// Représente un message échangé dans un ticket de support.
final class Message
{
    public function __construct(
        private readonly ?int $id,
        private readonly int $ticketId,
        private readonly int $authorId,
        private readonly string $content,
        private readonly ?DateTimeImmutable $createdAt = null,
    ) {
    }

    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            id: isset($row['id']) && $row['id'] !== null ? (int) $row['id'] : null,
            ticketId: (int) ($row['ticket_id'] ?? 0),
            authorId: (int) ($row['author_id'] ?? 0),
            content: (string) ($row['content'] ?? ''),
            createdAt: isset($row['created_at']) && $row['created_at'] !== null
                ? new DateTimeImmutable((string) $row['created_at'])
                : null,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function ticketId(): int
    {
        return $this->ticketId;
    }

    public function authorId(): int
    {
        return $this->authorId;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticketId,
            'author_id' => $this->authorId,
            'content' => $this->content,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
