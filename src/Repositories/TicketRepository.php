<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Models\Ticket;
use InvalidArgumentException;
use PDO;
use RuntimeException;

// Implémentation PDO du dépôt des tickets
final class TicketRepository
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

        $statement = $this->pdo->prepare(
            'UPDATE tickets SET status = :status, updated_at = NOW() WHERE id = :id'
        );
        $statement->execute([
            'status' => $status,
            'id' => $ticketId,
        ]);

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
        return 'SELECT id, subject, description, client_id, agent_id, category_id, priority_id, status,
                created_at, updated_at, resolved_at, closed_at FROM tickets';
    }
}