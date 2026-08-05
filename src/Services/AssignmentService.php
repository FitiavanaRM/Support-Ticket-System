<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Strategies\AssignmentStrategyInterface;
use App\Strategies\ManualAssignmentStrategy;
use App\Strategies\RoundRobinAssignmentStrategy;
use App\Strategies\WorkloadAssignmentStrategy;
use InvalidArgumentException;

/**
 * Service centralisant le choix d'un agent selon la stratégie active.
 */
final class AssignmentService
{
    /**
     * @param list<int> $availableAgentIds
     */
    public function assign(
        string $strategyCode,
        array $availableAgentIds,
        ?int $lastAssignedAgentId = null,
        ?int $preferredAgentId = null,
    ): ?int {
        $strategy = $this->strategyFor($strategyCode);

        if ($preferredAgentId !== null && in_array($preferredAgentId, $availableAgentIds, true)) {
            return $preferredAgentId;
        }

        return $strategy->assign($availableAgentIds, $lastAssignedAgentId);
    }

    private function strategyFor(string $strategyCode): AssignmentStrategyInterface
    {
        return match (strtoupper($strategyCode)) {
            'MANUAL' => new ManualAssignmentStrategy(),
            'ROUND_ROBIN', 'ROUNDROBIN' => new RoundRobinAssignmentStrategy(),
            'WORKLOAD' => new WorkloadAssignmentStrategy(),
            default => throw new InvalidArgumentException("Stratégie d'assignation inconnue : {$strategyCode}"),
        };
    }
}
