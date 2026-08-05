<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Services\AssignmentService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie le comportement métier des stratégies d'assignation.
 *
 * Ce test est utile car l'assignation est un point de décision central dans le
 * système : elle conditionne la répartition des tickets entre agents et la
 * cohérence des règles métier sans dépendre directement d'une base de données.
 */
final class AssignmentServiceTest extends TestCase
{
    public function test_it_prefers_the_requested_agent_when_available(): void
    {
        $service = new AssignmentService();

        $result = $service->assign(
            strategyCode: 'MANUAL',
            availableAgentIds: [1, 4, 9],
            lastAssignedAgentId: 1,
            preferredAgentId: 9,
        );

        $this->assertSame(9, $result);
    }

    public function test_round_robin_moves_to_the_next_agent_in_circular_order(): void
    {
        $service = new AssignmentService();

        $this->assertSame(2, $service->assign('ROUND_ROBIN', [1, 2, 3], 1));
        $this->assertSame(3, $service->assign('ROUND_ROBIN', [1, 2, 3], 2));
        $this->assertSame(1, $service->assign('ROUND_ROBIN', [1, 2, 3], 3));
    }

    public function test_round_robin_starts_from_the_first_available_agent_when_last_agent_is_unknown(): void
    {
        $service = new AssignmentService();

        $this->assertSame(1, $service->assign('ROUND_ROBIN', [1, 2, 3], 99));
    }

    public function test_it_rejects_an_unknown_assignment_strategy(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Stratégie d'assignation inconnue : UNKNOWN");

        (new AssignmentService())->assign('UNKNOWN', [1, 2, 3]);
    }
}
