<?php

declare(strict_types=1);

namespace App\States;

use App\Models\Ticket;
use InvalidArgumentException;

final class TicketState
{
    /** @return list<string> */
    public static function allowedTransitions(string $status): array
    {
        return self::resolve($status)::allowedTransitions();
    }

    public static function canTransitionTo(string $currentStatus, string $targetStatus): bool
    {
        return self::resolve($currentStatus)::canTransitionTo($targetStatus);
    }

    public static function assertTransition(string $currentStatus, string $targetStatus): void
    {
        self::resolve($currentStatus)::assertTransitionTo($targetStatus);
    }

    /** @return class-string */
    private static function resolve(string $status): string
    {
        return match ($status) {
            Ticket::STATUS_OPEN => OpenState::class,
            Ticket::STATUS_ASSIGNED => AssignedState::class,
            Ticket::STATUS_IN_PROGRESS => InProgressState::class,
            Ticket::STATUS_RESOLVED => ResolvedState::class,
            Ticket::STATUS_CLOSED => ClosedState::class,
            default => throw new InvalidArgumentException("Statut de ticket inconnu : {$status}"),
        };
    }
}
