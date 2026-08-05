<?php

declare(strict_types=1);

namespace App\Interfaces\Strategies;

interface AssignmentStrategyInterface
{
    /**
     * Retourne l'ID de l'agent choisi parmi les agents disponibles.
     *
     * @param list<int> $availableAgentIds
     */
    public function assign(array $availableAgentIds, ?int $lastAssignedAgentId = null): ?int;
}
