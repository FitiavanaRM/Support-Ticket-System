<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Message;

/**
 * Contrat du dépôt des messages de ticket.
 *
 * Il isole la logique de lecture et d'écriture des messages de l'implémentation
 * technique PDO, ce qui permet de respecter les principes SOLID et le style
 * architectural déjà adopté dans le projet.
 */
interface MessageRepositoryInterface
{
    public function create(Message $message): Message;

    public function findById(int $id): ?Message;

    /** @return list<Message> */
    public function findByTicketId(int $ticketId): array;
}
