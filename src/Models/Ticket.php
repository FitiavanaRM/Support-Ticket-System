<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use InvalidArgumentException;

/** Représente un ticket de support persisté ou en cours de création. */
final class Ticket
{
    public const STATUS_OPEN = 'open';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    /** @var list<string> */
    private const ALLOWED_STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_RESOLVED,
        self::STATUS_CLOSED,
    ];

    public function __construct(
        private readonly ?int $id,
        private readonly string $subject,
        private readonly string $description,
        private readonly int $clientId,
        private readonly ?int $agentId,
        private readonly int $categoryId,
        private readonly int $priorityId,
        private readonly string $status,
        private readonly ?string $categoryName = null,
        private readonly ?string $priorityName = null,
        private readonly ?DateTimeImmutable $createdAt = null,
        private readonly ?DateTimeImmutable $updatedAt = null,
        private readonly ?DateTimeImmutable $resolvedAt = null,
        private readonly ?DateTimeImmutable $closedAt = null,
    ) {
        if (!in_array($this->status, self::ALLOWED_STATUSES, true)) {
            throw new InvalidArgumentException("Statut de ticket invalide : {$this->status}");
        }
    }

    /** @param array<string, mixed> $row Ligne retournée par TicketRepository. */
    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            subject: (string) $row['subject'],
            description: (string) $row['description'],
            clientId: (int) $row['client_id'],
            agentId: $row['agent_id'] === null ? null : (int) $row['agent_id'],
            categoryId: (int) $row['category_id'],
            priorityId: (int) $row['priority_id'],
            status: (string) $row['status'],
            categoryName: isset($row['category_name']) ? (string) $row['category_name'] : null,
            priorityName: isset($row['priority_name']) ? (string) $row['priority_name'] : null,
            createdAt: self::dateFromRow($row, 'created_at'),
            updatedAt: self::dateFromRow($row, 'updated_at'),
            resolvedAt: self::dateFromRow($row, 'resolved_at'),
            closedAt: self::dateFromRow($row, 'closed_at'),
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function clientId(): int
    {
        return $this->clientId;
    }

    public function agentId(): ?int
    {
        return $this->agentId;
    }

    public function categoryId(): int
    {
        return $this->categoryId;
    }

    public function priorityId(): int
    {
        return $this->priorityId;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function categoryName(): ?string
    {
        return $this->categoryName;
    }

    public function priorityName(): ?string
    {
        return $this->priorityName;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function resolvedAt(): ?DateTimeImmutable
    {
        return $this->resolvedAt;
    }

    public function closedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    /** @return array<string, int|string|null> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'client_id' => $this->clientId,
            'agent_id' => $this->agentId,
            'category_id' => $this->categoryId,
            'priority_id' => $this->priorityId,
            'status' => $this->status,
            'category_name' => $this->categoryName,
            'priority_name' => $this->priorityName,
            'created_at' => $this->formatDate($this->createdAt),
            'updated_at' => $this->formatDate($this->updatedAt),
            'resolved_at' => $this->formatDate($this->resolvedAt),
            'closed_at' => $this->formatDate($this->closedAt),
        ];
    }

    /** @param array<string, mixed> $row */
    private static function dateFromRow(array $row, string $column): ?DateTimeImmutable
    {
        if (!isset($row[$column]) || $row[$column] === '') {
            return null;
        }

        return new DateTimeImmutable((string) $row[$column]);
    }

    private function formatDate(?DateTimeImmutable $date): ?string
    {
        return $date?->format('Y-m-d H:i:s');
    }
}
