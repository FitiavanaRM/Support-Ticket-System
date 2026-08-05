<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Interfaces\Repositories\AssignmentSettingsRepositoryInterface;
use App\Models\AssignmentSettings;
use InvalidArgumentException;
use PDO;
use RuntimeException;

/**
 * Accès aux préférences de stratégie d'assignation persistées en base.
 *
 * Ce dépôt complète le modèle AssignmentSettings et permet au service de
 * configuration de lire la stratégie active sans dépendre directement de PDO.
 * Il centralise aussi la mise à jour du dernier agent traité pour le round-robin.
 */
final class AssignmentSettingsRepository implements AssignmentSettingsRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function find(): AssignmentSettings
    {
        $statement = $this->pdo->query(
            'SELECT id, strategy_code, last_agent_id, updated_at
             FROM assignment_settings
             ORDER BY id ASC LIMIT 1'
        );

        $row = $statement->fetch();

        if ($row === false) {
            return new AssignmentSettings(
                id: 1,
                strategyCode: AssignmentSettings::DEFAULT_STRATEGY,
                lastAgentId: null,
                updatedAt: null,
            );
        }

        return AssignmentSettings::fromDatabaseRow($row);
    }

    public function updateStrategy(string $strategyCode): AssignmentSettings
    {
        $normalizedCode = strtoupper(trim($strategyCode));

        if (!in_array($normalizedCode, ['MANUAL', 'ROUND_ROBIN', 'ROUNDROBIN', 'WORKLOAD'], true)) {
            throw new InvalidArgumentException("Stratégie d'assignation invalide : {$strategyCode}");
        }

        $statement = $this->pdo->prepare(
            'UPDATE assignment_settings
             SET strategy_code = :strategy_code, updated_at = NOW()
             WHERE id = 1'
        );

        $statement->execute(['strategy_code' => $normalizedCode]);

        if ($statement->rowCount() === 0) {
            throw new RuntimeException('Aucune configuration d’assignation trouvée pour la mise à jour.');
        }

        return $this->find();
    }

    public function updateLastAgent(?int $agentId): AssignmentSettings
    {
        $statement = $this->pdo->prepare(
            'UPDATE assignment_settings
             SET last_agent_id = :last_agent_id, updated_at = NOW()
             WHERE id = 1'
        );

        $statement->execute([
            'last_agent_id' => $agentId,
        ]);

        if ($statement->rowCount() === 0) {
            throw new RuntimeException('Aucune configuration d’assignation trouvée pour la mise à jour du dernier agent.');
        }

        return $this->find();
    }
}
