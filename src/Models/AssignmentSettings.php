<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Représente la configuration de stratégie d'assignation globale du système.
 *
 * Ce modèle est nécessaire car la base contient déjà la table assignment_settings,
 * mais aucune classe métier n'encapsule encore cette configuration. La logique de
 * sélection d'agent s'appuie ensuite sur cette valeur pour décider de la stratégie
 * active sans dépendre de constantes dispersées dans le code.
 */
final class AssignmentSettings
{
    public const DEFAULT_STRATEGY = 'MANUAL';

    /** @var list<string> */
    private const ALLOWED_STRATEGIES = [
        'MANUAL',
        'ROUND_ROBIN',
        'ROUNDROBIN',
        'WORKLOAD',
    ];

    public function __construct(
        private readonly int $id,
        private readonly string $strategyCode,
        private readonly ?int $lastAgentId = null,
        private readonly ?DateTimeImmutable $updatedAt = null,
    ) {
        if (!in_array(strtoupper($this->strategyCode), self::ALLOWED_STRATEGIES, true)) {
            throw new InvalidArgumentException("Stratégie d'assignation invalide : {$this->strategyCode}");
        }
    }

    /** @param array<string, mixed> $row */
    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            id: (int) ($row['id'] ?? 0),
            strategyCode: (string) ($row['strategy_code'] ?? self::DEFAULT_STRATEGY),
            lastAgentId: isset($row['last_agent_id']) && $row['last_agent_id'] !== null
                ? (int) $row['last_agent_id']
                : null,
            updatedAt: isset($row['updated_at']) && $row['updated_at'] !== null
                ? new DateTimeImmutable((string) $row['updated_at'])
                : null,
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function strategyCode(): string
    {
        return strtoupper($this->strategyCode);
    }

    public function lastAgentId(): ?int
    {
        return $this->lastAgentId;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'strategy_code' => $this->strategyCode(),
            'last_agent_id' => $this->lastAgentId,
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
