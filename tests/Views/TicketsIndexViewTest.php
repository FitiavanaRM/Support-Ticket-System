<?php

declare(strict_types=1);

namespace Tests\Views;

use App\Models\Ticket;
use App\Support\View;
use PHPUnit\Framework\TestCase;

final class TicketsIndexViewTest extends TestCase
{
    public function testTicketsIndexViewUsesProvidedAgentNames(): void
    {
        $ticket = new Ticket(
            id: 10,
            subject: 'Problème de connexion',
            description: 'Je ne peux plus me connecter',
            clientId: 3,
            agentId: 4,
            categoryId: 1,
            priorityId: 2,
            status: Ticket::STATUS_IN_PROGRESS,
            categoryName: 'Logiciel',
            priorityName: 'Haute',
            createdAt: new \DateTimeImmutable('2024-02-01 10:00:00'),
            updatedAt: new \DateTimeImmutable('2024-02-01 11:00:00'),
        );

        $html = View::render(__DIR__ . '/../../src/Views/tickets/index.php', [
            'tickets' => [$ticket],
            'ticketAgentNames' => [4 => 'Agent fourni'],
            'currentUser' => null,
        ]);

        $this->assertStringContainsString('Problème de connexion', $html);
        $this->assertStringContainsString('Agent fourni', $html);
    }
}
