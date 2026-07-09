<?php

declare(strict_types=1);

namespace App\Exceptions;

final class TicketNotFoundException extends TicketException
{
    public function __construct(private readonly int $ticketId)
    {
        parent::__construct("Ticket #{$this->ticketId} introuvable.");
    }

    public function httpStatusCode(): int
    {
        return 404;
    }

    public function ticketId(): int
    {
        return $this->ticketId;
    }

    public function context(): array
    {
        return ['ticket_id' => $this->ticketId];
    }
}