<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Notification;

/**
 * Contrat du dépôt des notifications.
 *
 * Il permet à l'observer de notifier sans dépendre directement de la couche PDO,
 * en gardant ainsi le code métier indépendant de l'implémentation technique.
 */
interface NotificationRepositoryInterface
{
    public function create(Notification $notification): Notification;

    public function findById(int $id): ?Notification;

    /** @return list<Notification> */
    public function findUnreadForUser(int $userId): array;
}
