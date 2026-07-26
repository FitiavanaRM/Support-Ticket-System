<?php

declare(strict_types=1);

namespace App\Factories;

use App\Models\Ticket;

// creer un nouveau factory du ticket
final class TicketFactory
{
    /**
     * @param array<string, mixed> $data Données déjà validées par TicketValidator.
     */
    public function createForClient(array $data, int $clientId): Ticket
    {
        return new Ticket(
            id: null,
            subject: trim((string) $data['subject']),
            description: trim((string) $data['description']),
            clientId: $clientId,
            agentId: null,
            categoryId: (int) $data['category_id'],
            priorityId: (int) $data['priority_id'],
            status: Ticket::STATUS_OPEN,
        );
    }
}