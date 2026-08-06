<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Interfaces\Repositories\TicketRepositoryInterface;
use App\Models\Ticket;
use InvalidArgumentException;
use PDO;
use RuntimeException;

// Implémentation PDO du dépôt des tickets
final class TicketRepository implements TicketRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function create(Ticket $ticket): Ticket
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO tickets (subject, description, client_id, category_id, priority_id, status)
             VALUES (:subject, :description, :client_id, :category_id, :priority_id, :status)'
        );

        $statement->execute([
            'subject' => $ticket->subject(),
            'description' => $ticket->description(),
            'client_id' => $ticket->clientId(),
            'category_id' => $ticket->categoryId(),
            'priority_id' => $ticket->priorityId(),
            'status' => $ticket->status(),
        ]);

        $createdTicket = $this->findById((int) $this->pdo->lastInsertId());
        if ($createdTicket === null) {
            throw new RuntimeException('Le ticket créé est introuvable.');
        }

        return $createdTicket;
    }

    public function findById(int $id): ?Ticket
    {
        $statement = $this->pdo->prepare($this->baseQuery() . ' WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : Ticket::fromDatabaseRow($row);
    }

    public function updateStatus(int $ticketId, string $status): Ticket
    {
        $allowedStatuses = [
            Ticket::STATUS_OPEN,
            Ticket::STATUS_ASSIGNED,
            Ticket::STATUS_IN_PROGRESS,
            Ticket::STATUS_RESOLVED,
            Ticket::STATUS_CLOSED,
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException("Statut de ticket invalide : {$status}");
        }

        $updates = [
            'status = :status',
            'updated_at = NOW()',
        ];
        $params = [
            'status' => $status,
            'id' => $ticketId,
        ];

        if ($status === Ticket::STATUS_RESOLVED) {
            $updates[] = 'resolved_at = NOW()';
        }

        if ($status === Ticket::STATUS_CLOSED) {
            $updates[] = 'closed_at = NOW()';
        }

        if ($status !== Ticket::STATUS_RESOLVED && $status !== Ticket::STATUS_CLOSED) {
            $updates[] = 'resolved_at = NULL';
            $updates[] = 'closed_at = NULL';
        }

        $statement = $this->pdo->prepare(
            'UPDATE tickets SET ' . implode(', ', $updates) . ' WHERE id = :id'
        );
        $statement->execute($params);

        $updatedTicket = $this->findById($ticketId);
        if ($updatedTicket === null) {
            throw new RuntimeException('Le ticket mis à jour est introuvable.');
        }

        return $updatedTicket;
    }

    public function assignAgent(int $ticketId, int $agentId, string $status = Ticket::STATUS_ASSIGNED): Ticket
    {
        $statement = $this->pdo->prepare(
            'UPDATE tickets SET agent_id = :agent_id, status = :status, updated_at = NOW() WHERE id = :id'
        );

        $statement->execute([
            'agent_id' => $agentId,
            'status' => $status,
            'id' => $ticketId,
        ]);

        $updatedTicket = $this->findById($ticketId);
        if ($updatedTicket === null) {
            throw new RuntimeException('Le ticket affecté est introuvable.');
        }

        return $updatedTicket;
    }

    /** @return list<Ticket> */
    public function findByClientId(int $clientId): array
    {
        return $this->findMany('client_id', $clientId);
    }

    /** @return list<Ticket> */
    public function findByAgentId(int $agentId): array
    {
        return $this->findMany('agent_id', $agentId);
    }

    /** @return list<Ticket> */
    public function findRecentForUser(int $userId, int $limit = 5): array
    {
        $query = $this->baseQuery() . ' WHERE t.client_id = :user_id OR t.agent_id = :user_id ORDER BY t.updated_at DESC LIMIT :limit';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();

        return array_map(
            static fn (array $row): Ticket => Ticket::fromDatabaseRow($row),
            $rows
        );
    }

    /** @return array<string, int> */
    public function aggregateStatusCountsForUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT
                 COUNT(*) AS total,
                 SUM(t.status = :open) AS open,
                 SUM(t.status = :assigned) AS assigned,
                 SUM(t.status = :in_progress) AS in_progress,
                 SUM(t.status = :resolved) AS resolved,
                 SUM(t.status = :closed) AS closed
             FROM tickets t
             WHERE t.client_id = :user_id OR t.agent_id = :user_id'
        );

        $statement->execute([
            'open' => Ticket::STATUS_OPEN,
            'assigned' => Ticket::STATUS_ASSIGNED,
            'in_progress' => Ticket::STATUS_IN_PROGRESS,
            'resolved' => Ticket::STATUS_RESOLVED,
            'closed' => Ticket::STATUS_CLOSED,
            'user_id' => $userId,
        ]);

        $counts = $statement->fetch();

        return [
            'total' => isset($counts['total']) ? (int) $counts['total'] : 0,
            'open' => isset($counts['open']) ? (int) $counts['open'] : 0,
            'assigned' => isset($counts['assigned']) ? (int) $counts['assigned'] : 0,
            'in_progress' => isset($counts['in_progress']) ? (int) $counts['in_progress'] : 0,
            'resolved' => isset($counts['resolved']) ? (int) $counts['resolved'] : 0,
            'closed' => isset($counts['closed']) ? (int) $counts['closed'] : 0,
        ];
    }

    /** @return list<Ticket> */
    private function findMany(string $column, int $userId): array
    {
        $statement = $this->pdo->prepare(
            $this->baseQuery() . " WHERE {$column} = :user_id ORDER BY created_at DESC"
        );
        $statement->execute(['user_id' => $userId]);
        $rows = $statement->fetchAll();

        return array_map(
            static fn (array $row): Ticket => Ticket::fromDatabaseRow($row),
            $rows
        );
    }

    private function baseQuery(): string
    {
        return 'SELECT t.id, t.subject, t.description, t.client_id, t.agent_id, t.category_id, t.priority_id, t.status,
                t.created_at, t.updated_at, t.resolved_at, t.closed_at,
                c.name AS category_name,
                p.name AS priority_name
            FROM tickets t
            INNER JOIN categories c ON c.id = t.category_id
            INNER JOIN priorities p ON p.id = t.priority_id';
    }
}