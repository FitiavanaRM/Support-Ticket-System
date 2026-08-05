<?php

declare(strict_types=1);

namespace App\Strategies;

use App\Interfaces\Strategies\AssignmentStrategyInterface;
use App\Repositories\TicketRepository;

// Sélectionne l'agent ayant actuellement la charge la plus faible.
final class WorkloadAssignmentStrategy implements AssignmentStrategyInterface
{
    /**
     * @param list<int> $availableAgentIds
     */
    public function assign(array $availableAgentIds, ?int $lastAssignedAgentId = null): ?int
    {
        if ($availableAgentIds === []) {
            return null;
        }

        $ticketRepository = new TicketRepository();
        $bestAgentId = null;
        $bestLoad = PHP_INT_MAX;

        foreach ($availableAgentIds as $agentId) {
            $load = count($ticketRepository->findByAgentId($agentId));

            if ($load < $bestLoad) {
                $bestLoad = $load;
                $bestAgentId = $agentId;
            }
        }

        return $bestAgentId;
    }
}
