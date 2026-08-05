<?php

declare(strict_types=1);

namespace App\States;

use App\Exceptions\InvalidStateTransitionException;
use App\Models\Ticket;

final class ClosedState
{
    /** @return list<string> */
    public static function allowedTransitions(): array
    {
        return [];
    }

    public static function canTransitionTo(string $targetStatus): bool
    {
        return false;
    }

    public static function assertTransitionTo(string $targetStatus): void
    {
        if (!self::canTransitionTo($targetStatus)) {
            throw new InvalidStateTransitionException(Ticket::STATUS_CLOSED, $targetStatus);
        }
    }
}
