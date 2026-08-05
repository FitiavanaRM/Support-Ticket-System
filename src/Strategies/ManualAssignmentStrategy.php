<?php

declare(strict_types=1);

namespace App\Strategies;

use App\Interfaces\Strategies\AssignmentStrategyInterface;

/**
 * Choisit explicitement un agent parmi les candidats disponibles.
 * Cette stratégie est utile quand l'assignation dépend d'une décision humaine.
 */
final class ManualAssignmentStrategy implements AssignmentStrategyInterface
{
    /**
     * @param list<int> $availableAgentIds
     */
    public function assign(array $availableAgentIds, ?int $lastAssignedAgentId = null): ?int
    {
        if ($availableAgentIds === []) {
            return null;
        }

        return $availableAgentIds[0];
    }
}
