<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Ticket;

/**
 * Interface de contrat pour le dépôt des tickets.
 *
 * Ce contrat est nécessaire pour séparer la logique métier de l'implémentation
 * technique PDO. Il permet au service TicketService de dépendre d'un contrat
 * abstrait au lieu de la classe concrète TicketRepository, ce qui respecte les
 * principes SOLID et facilite les tests unitaires.
 */
interface TicketRepositoryInterface
{
    public function create(Ticket $ticket): Ticket;

    public function findById(int $id): ?Ticket;

    public function updateStatus(int $ticketId, string $status): Ticket;

    public function assignAgent(int $ticketId, int $agentId, string $status = Ticket::STATUS_ASSIGNED): Ticket;

    /** @return list<Ticket> */
    public function findByClientId(int $clientId): array;

    /** @return list<Ticket> */
    public function findByAgentId(int $agentId): array;
}
