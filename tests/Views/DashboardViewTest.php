<?php

declare(strict_types=1);

namespace Tests\Views;

use App\Models\Ticket;
use App\Support\View;
use PHPUnit\Framework\TestCase;

final class DashboardViewTest extends TestCase
{
    public function testDashboardViewRendersRealTicketDataFromPassedData(): void
    {
        $ticket = new Ticket(
            id: 42,
            subject: 'Problème d’accès',
            description: 'Je ne peux plus me connecter',
            clientId: 7,
            agentId: 3,
            categoryId: 1,
            priorityId: 2,
            status: Ticket::STATUS_OPEN,
            categoryName: 'Logiciel',
            priorityName: 'Haute',
            createdAt: new \DateTimeImmutable('2024-01-01 10:00:00'),
            updatedAt: new \DateTimeImmutable('2024-01-01 10:30:00'),
        );

        $html = View::render(__DIR__ . '/../../src/Views/dashboard/index.php', [
            'stats' => [
                'total' => 1,
                'open' => 1,
                'assigned' => 0,
                'in_progress' => 0,
                'resolved' => 0,
                'closed' => 0,
            ],
            'recentTickets' => [
                ['ticket' => $ticket, 'agentName' => 'Agent test'],
            ],
            'currentUser' => null,
        ]);

        $this->assertStringContainsString('Problème d’accès', $html);
        $this->assertStringContainsString('Logiciel', $html);
        $this->assertStringContainsString('Agent test', $html);
    }
}
