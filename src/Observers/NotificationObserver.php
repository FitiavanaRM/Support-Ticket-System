<?php

declare(strict_types=1);

namespace App\Observers;

use App\Factories\NotificationFactory;
use App\Interfaces\Repositories\NotificationRepositoryInterface;

final class NotificationObserver
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notificationRepository,
        private readonly NotificationFactory $notificationFactory,
    ) {
    }

    public function onTicketStatusChanged(int $recipientId, int $ticketId, string $status): void
    {
        $message = match ($status) {
            'assigned' => 'Un ticket a été affecté à un agent.',
            'in_progress' => 'Le ticket est en cours de traitement.',
            'resolved' => 'Le ticket a été résolu.',
            'closed' => 'Le ticket a été fermé.',
            default => 'Le statut du ticket a changé.',
        };

        $notification = $this->notificationFactory->createForTicket($recipientId, $ticketId, $message);
        $this->notificationRepository->create($notification);
    }
}
