<?php

declare(strict_types=1);

namespace App\Observers;

final class LogginObserver
{
    public function onTicketStatusChanged(int $actorId, int $ticketId, string $fromStatus, string $toStatus): void
    {
        $log = sprintf(
            '[%s] user=%d ticket=%d status=%s -> %s',
            date('Y-m-d H:i:s'),
            $actorId,
            $ticketId,
            $fromStatus,
            $toStatus
        );

        file_put_contents(
            dirname(__DIR__, 2) . '/storage/logs/app.log',
            $log . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
