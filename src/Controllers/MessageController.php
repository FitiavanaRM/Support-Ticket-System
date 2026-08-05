<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\AuthenticationException;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Models\Message;
use App\Repositories\MessageRepository;
use App\Services\MessageService;
use App\Support\Session;

/**
 * Contrôleur HTTP pour la gestion des messages d'un ticket.
 *
 * Il sert d'interface entre la requête HTTP et la logique métier du service
 * MessageService. Le service garde la validation métier, tandis que le contrôleur
 * se charge uniquement de lire la requête, vérifier l'authentification et
 * transformer le résultat en réponse JSON.
 */
final class MessageController
{
    public function index(Request $request, string $ticketId): Response
    {
        $validTicketId = $this->validTicketId($ticketId);
        $messages = $this->messageService()->listForTicket($validTicketId);

        return Response::json([
            'status' => 'success',
            'data' => array_map(
                static fn (Message $message): array => $message->toArray(),
                $messages,
            ),
        ]);
    }

    public function store(Request $request, string $ticketId): Response
    {
        $validTicketId = $this->validTicketId($ticketId);
        $content = $request->input('content');

        $message = $this->messageService()->createForTicket(
            $validTicketId,
            $this->authenticatedUserId(),
            is_string($content) ? $content : '',
        );

        return Response::json([
            'status' => 'success',
            'data' => $message->toArray(),
        ], 201);
    }

    private function messageService(): MessageService
    {
        return new MessageService(
            new MessageRepository(),
        );
    }

    private function authenticatedUserId(): int
    {
        $userId = (new Session())->userId();

        if ($userId === null) {
            throw new AuthenticationException();
        }

        return $userId;
    }

    private function validTicketId(string $ticketId): int
    {
        if (!ctype_digit($ticketId) || (int) $ticketId < 1) {
            throw new ValidationException('Identifiant de ticket invalide.', [
                'ticket_id' => 'L’identifiant du ticket doit être un entier positif.',
            ]);
        }

        return (int) $ticketId;
    }
}
