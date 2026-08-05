<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\AssignmentSettingsRepositoryInterface;
use App\Models\AssignmentSettings;
use InvalidArgumentException;

// Service métier qui lit et met à jour la stratégie d'assignation globale.
// Il permet d'isoler la logique de configuration de la couche repository et de
// garder le code métier lisible pour les contrôleurs et les services applicatifs.
final class AssignmentSettingsService
{
    public function __construct(
        private readonly AssignmentSettingsRepositoryInterface $assignmentSettingsRepository,
    ) {
    }

    public function current(): AssignmentSettings
    {
        return $this->assignmentSettingsRepository->find();
    }

    public function currentStrategyCode(): string
    {
        return $this->current()->strategyCode();
    }

    public function setStrategy(string $strategyCode): AssignmentSettings
    {
        return $this->assignmentSettingsRepository->updateStrategy($strategyCode);
    }

    public function updateLastAgent(?int $agentId): AssignmentSettings
    {
        return $this->assignmentSettingsRepository->updateLastAgent($agentId);
    }

    /**
     * @param list<int> $availableAgentIds
     */
    public function chooseAgent(array $availableAgentIds, ?int $lastAssignedAgentId = null): ?int
    {
        if ($availableAgentIds === []) {
            return null;
        }

        $strategyCode = $this->currentStrategyCode();
        $assignmentService = new AssignmentService();

        return $assignmentService->assign(
            strategyCode: $strategyCode,
            availableAgentIds: $availableAgentIds,
            lastAssignedAgentId: $lastAssignedAgentId,
        );
    }

    public function isValidStrategyCode(string $strategyCode): bool
    {
        $normalized = strtoupper(trim($strategyCode));

        return in_array($normalized, ['MANUAL', 'ROUND_ROBIN', 'ROUNDROBIN', 'WORKLOAD'], true);
    }

    public function normalizeStrategyCode(string $strategyCode): string
    {
        $normalized = strtoupper(trim($strategyCode));

        if (!in_array($normalized, ['MANUAL', 'ROUND_ROBIN', 'ROUNDROBIN', 'WORKLOAD'], true)) {
            throw new InvalidArgumentException("Stratégie d'assignation invalide : {$strategyCode}");
        }

        return $normalized;
    }
}
