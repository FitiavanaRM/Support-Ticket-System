<?php

declare(strict_types=1);

namespace App\Strategies;

use App\Interfaces\Strategies\AssignmentStrategyInterface;

// Permet de choisir explicitement un agent disponible lors d'une affectation manuelle.
final class ManualAssignementStrategy implements AssignmentStrategyInterface
{
    /**
     * @param list<int> $availableAgentIds
     */
    public function assign(array $availableAgentIds, ?int $lastAssignedAgentId = null): ?int
    {
        if ($availableAgentIds === []) {
            return null;
        }

        // Si l'agent précédemment sélectionné reste disponible, on le garde.
        if ($lastAssignedAgentId !== null && in_array($lastAssignedAgentId, $availableAgentIds, true)) {
            return $lastAssignedAgentId;
        }

        return $availableAgentIds[0];
    }
}
