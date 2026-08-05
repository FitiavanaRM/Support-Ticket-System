<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Models\Message;
use PDO;
use RuntimeException;

// Dépôt des messages de ticket.
// Il complète le modèle Message et centralise les opérations sur la table
// ticket_messages sans mélanger le SQL dans les services ou contrôleurs.
final class MessageRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function create(Message $message): Message
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO ticket_messages (ticket_id, author_id, content)
             VALUES (:ticket_id, :author_id, :content)'
        );

        $statement->execute([
            'ticket_id' => $message->ticketId(),
            'author_id' => $message->authorId(),
            'content' => $message->content(),
        ]);

        $createdId = (int) $this->pdo->lastInsertId();
        $created = $this->findById($createdId);

        if ($created === null) {
            throw new RuntimeException('Message créé introuvable.');
        }

        return $created;
    }

    public function findById(int $id): ?Message
    {
        $statement = $this->pdo->prepare(
            'SELECT id, ticket_id, author_id, content, created_at
             FROM ticket_messages
             WHERE id = :id'
        );

        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : Message::fromDatabaseRow($row);
    }

    /**
     * @return list<Message>
     */
    public function findByTicketId(int $ticketId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, ticket_id, author_id, content, created_at
             FROM ticket_messages
             WHERE ticket_id = :ticket_id
             ORDER BY created_at ASC'
        );

        $statement->execute(['ticket_id' => $ticketId]);
        $rows = $statement->fetchAll();

        return array_map(
            static fn (array $row): Message => Message::fromDatabaseRow($row),
            $rows,
        );
    }
}
