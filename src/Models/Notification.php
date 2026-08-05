<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

final class Notification
{
    public function __construct(
        private readonly ?int $id,
        private readonly int $recipientId,
        private readonly ?int $ticketId,
        private readonly string $message,
        private readonly bool $isRead,
        private readonly ?DateTimeImmutable $createdAt = null,
    ) {
    }

    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            id: isset($row['id']) ? (int) $row['id'] : null,
            recipientId: (int) $row['recipient_id'],
            ticketId: isset($row['ticket_id']) && $row['ticket_id'] !== null ? (int) $row['ticket_id'] : null,
            message: (string) $row['message'],
            isRead: (bool) $row['is_read'],
            createdAt: isset($row['created_at']) && $row['created_at'] !== null ? new DateTimeImmutable((string) $row['created_at']) : null,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function recipientId(): int
    {
        return $this->recipientId;
    }

    public function ticketId(): ?int
    {
        return $this->ticketId;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }
}
