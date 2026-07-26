<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\TicketNotFoundException;
use App\Factories\TicketFactory;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use App\Validation\TicketValidator;

/** Coordonne les cas d'utilisation liés à la création et à la consultation des tickets. */
final class TicketService
{
    public function __construct(
        private readonly TicketRepository $ticketRepository,
        private readonly TicketFactory $ticketFactory,
        private readonly TicketValidator $ticketValidator,
    ) {
    }

    /** @param array<string, mixed> $data */
    public function createForClient(array $data, int $clientId): Ticket
    {
        $this->ticketValidator->validateForCreation($data);

        $ticket = $this->ticketFactory->createForClient($data, $clientId);

        return $this->ticketRepository->create($ticket);
    }

    public function findOrFail(int $ticketId): Ticket
    {
        $ticket = $this->ticketRepository->findById($ticketId);

        if ($ticket === null) {
            throw new TicketNotFoundException($ticketId);
        }

        return $ticket;
    }

    /** @return list<Ticket> */
    public function listForClient(int $clientId): array
    {
        return $this->ticketRepository->findByClientId($clientId);
    }
}
