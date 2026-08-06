<?php

declare(strict_types=1);

namespace Tests\States;

use App\Exceptions\InvalidStateTransitionException;
use App\Models\Ticket;
use App\States\TicketState;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie la cohérence de la machine à états du ticket.
 * Cette couche est essentielle car les transitions de statut conditionnent la
 * validité des opérations métier et empêchent les états incohérents dans le cycle
 * de vie d'un ticket de support.
 */
final class TicketStateTest extends TestCase
{
    public function test_open_ticket_may_transition_to_assigned_in_progress_or_closed(): void
    {
        $this->assertSame([
            Ticket::STATUS_ASSIGNED,
            Ticket::STATUS_IN_PROGRESS,
            Ticket::STATUS_CLOSED,
        ], TicketState::allowedTransitions(Ticket::STATUS_OPEN));

        $this->assertTrue(TicketState::canTransitionTo(Ticket::STATUS_OPEN, Ticket::STATUS_ASSIGNED));
        $this->assertTrue(TicketState::canTransitionTo(Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS));
        $this->assertTrue(TicketState::canTransitionTo(Ticket::STATUS_OPEN, Ticket::STATUS_CLOSED));
        $this->assertFalse(TicketState::canTransitionTo(Ticket::STATUS_OPEN, Ticket::STATUS_RESOLVED));
    }

    public function test_assigned_ticket_may_transition_to_in_progress_resolved_or_closed(): void
    {
        $this->assertSame([
            Ticket::STATUS_IN_PROGRESS,
            Ticket::STATUS_RESOLVED,
            Ticket::STATUS_CLOSED,
        ], TicketState::allowedTransitions(Ticket::STATUS_ASSIGNED));

        $this->assertTrue(TicketState::canTransitionTo(Ticket::STATUS_ASSIGNED, Ticket::STATUS_IN_PROGRESS));
        $this->assertTrue(TicketState::canTransitionTo(Ticket::STATUS_ASSIGNED, Ticket::STATUS_RESOLVED));
        $this->assertTrue(TicketState::canTransitionTo(Ticket::STATUS_ASSIGNED, Ticket::STATUS_CLOSED));
        $this->assertFalse(TicketState::canTransitionTo(Ticket::STATUS_ASSIGNED, Ticket::STATUS_OPEN));
    }

    public function test_in_progress_ticket_may_only_transition_to_resolved_or_closed(): void
    {
        $this->assertSame([
            Ticket::STATUS_RESOLVED,
            Ticket::STATUS_CLOSED,
        ], TicketState::allowedTransitions(Ticket::STATUS_IN_PROGRESS));

        $this->assertTrue(TicketState::canTransitionTo(Ticket::STATUS_IN_PROGRESS, Ticket::STATUS_RESOLVED));
        $this->assertTrue(TicketState::canTransitionTo(Ticket::STATUS_IN_PROGRESS, Ticket::STATUS_CLOSED));
        $this->assertFalse(TicketState::canTransitionTo(Ticket::STATUS_IN_PROGRESS, Ticket::STATUS_OPEN));
    }

    public function test_resolved_ticket_may_only_transition_to_closed(): void
    {
        $this->assertSame([
            Ticket::STATUS_CLOSED,
        ], TicketState::allowedTransitions(Ticket::STATUS_RESOLVED));

        $this->assertTrue(TicketState::canTransitionTo(Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED));
        $this->assertFalse(TicketState::canTransitionTo(Ticket::STATUS_RESOLVED, Ticket::STATUS_OPEN));
    }

    public function test_closed_ticket_has_no_valid_transition(): void
    {
        $this->assertSame([], TicketState::allowedTransitions(Ticket::STATUS_CLOSED));
        $this->assertFalse(TicketState::canTransitionTo(Ticket::STATUS_CLOSED, Ticket::STATUS_OPEN));
    }

    public function test_it_throws_when_transition_is_not_allowed(): void
    {
        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage('Transition de statut invalide');

        TicketState::assertTransition(Ticket::STATUS_OPEN, Ticket::STATUS_RESOLVED);
    }
}
