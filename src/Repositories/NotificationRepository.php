<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Models\Notification;
use PDO;
use RuntimeException;

final class NotificationRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function create(Notification $notification): Notification
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO notifications (recipient_id, ticket_id, message, is_read) VALUES (:recipient_id, :ticket_id, :message, :is_read)'
        );

        $statement->execute([
            'recipient_id' => $notification->recipientId(),
            'ticket_id' => $notification->ticketId(),
            'message' => $notification->message(),
            'is_read' => $notification->isRead() ? 1 : 0,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $created = $this->findById($id);

        if ($created === null) {
            throw new RuntimeException('Notification créée introuvable.');
        }

        return $created;
    }

    public function findById(int $id): ?Notification
    {
        $statement = $this->pdo->prepare('SELECT * FROM notifications WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : Notification::fromDatabaseRow($row);
    }

    /** @return list<Notification> */
    public function findUnreadForUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM notifications WHERE recipient_id = :recipient_id AND is_read = 0 ORDER BY created_at DESC'
        );
        $statement->execute(['recipient_id' => $userId]);

        $rows = $statement->fetchAll();

        return array_map(static fn (array $row): Notification => Notification::fromDatabaseRow($row), $rows);
    }
}
