<?php

declare(strict_types=1);

namespace App\States;

use App\Exceptions\InvalidStateTransitionException;
use App\Models\Ticket;

final class InProgressState
{
    /** @return list<string> */
    public static function allowedTransitions(): array
    {
        return [
            Ticket::STATUS_RESOLVED,
            Ticket::STATUS_CLOSED,
        ];
    }

    public static function canTransitionTo(string $targetStatus): bool
    {
        return in_array($targetStatus, self::allowedTransitions(), true);
    }

    public static function assertTransitionTo(string $targetStatus): void
    {
        if (!self::canTransitionTo($targetStatus)) {
            throw new InvalidStateTransitionException(Ticket::STATUS_IN_PROGRESS, $targetStatus);
        }
    }
}
