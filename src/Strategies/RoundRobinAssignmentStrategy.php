<?php

declare(strict_types=1);

namespace App\Strategies;

use App\Interfaces\Strategies\AssignmentStrategyInterface;

/**
 * Choisit l'agent suivant dans l'ordre circulaire pour répartir équitablement la charge.
 */
final class RoundRobinAssignmentStrategy implements AssignmentStrategyInterface
{
    /**
     * @param list<int> $availableAgentIds
     */
    public function assign(array $availableAgentIds, ?int $lastAssignedAgentId = null): ?int
    {
        if ($availableAgentIds === []) {
            return null;
        }

        if ($lastAssignedAgentId === null || !in_array($lastAssignedAgentId, $availableAgentIds, true)) {
            return $availableAgentIds[0];
        }

        $index = array_search($lastAssignedAgentId, $availableAgentIds, true);
        $nextIndex = ($index + 1) % count($availableAgentIds);

        return $availableAgentIds[$nextIndex];
    }
}
