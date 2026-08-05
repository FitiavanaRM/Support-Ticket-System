<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Interfaces\Repositories\MessageRepositoryInterface;
use App\Models\Message;

/**
 * Service métier chargé de la gestion des messages attachés à un ticket.
 *
 * Ce service est nécessaire car le dépôt de messages existe déjà, mais la logique
 * de validation et de coordination métier n’est pas encore encapsulée. Il fait
 * le lien entre la couche repositorielle et les contrôleurs HTTP qui pourront
 * ensuite consommer ces messages sans mélange de logique applicative.
 */
final class MessageService
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
    ) {
    }

    /**
     * @return list<Message>
     */
    public function listForTicket(int $ticketId): array
    {
        return $this->messageRepository->findByTicketId($ticketId);
    }

    public function createForTicket(int $ticketId, int $authorId, string $content): Message
    {
        $cleanContent = trim($content);

        if ($cleanContent === '') {
            throw new ValidationException('Données de message invalides.', [
                'content' => 'Le contenu du message est requis.',
            ]);
        }

        if (mb_strlen($cleanContent) > 2000) {
            throw new ValidationException('Données de message invalides.', [
                'content' => 'Le message ne doit pas dépasser 2000 caractères.',
            ]);
        }

        $message = new Message(
            id: null,
            ticketId: $ticketId,
            authorId: $authorId,
            content: $cleanContent,
        );

        return $this->messageRepository->create($message);
    }
}
