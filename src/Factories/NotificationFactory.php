<?php

declare(strict_types=1);

namespace App\Factories;

use App\Models\Notification;

final class NotificationFactory
{
    public function createForTicket(int $recipientId, int $ticketId, string $message): Notification
    {
        return new Notification(
            id: null,
            recipientId: $recipientId,
            ticketId: $ticketId,
            message: $message,
            isRead: false,
            createdAt: null,
        );
    }
}
