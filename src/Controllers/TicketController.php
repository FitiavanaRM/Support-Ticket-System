<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\AuthenticationException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\ValidationException;
use App\Factories\TicketFactory;
use App\Http\Request;
use App\Http\Response;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use App\Services\TicketService;
use App\Support\Session;
use App\Validation\TicketValidator;

// Adaptateur HTTP des fonctionnalités accessibles aux clients
final class TicketController
{
    public function index(Request $request): Response
    {
        $tickets = $this->ticketService()->listForClient($this->authenticatedUserId());

        return Response::json([
            'status' => 'success',
            'data' => array_map(
                static fn (Ticket $ticket): array => $ticket->toArray(),
                $tickets
            ),
        ]);
    }

    public function create(Request $request): Response
    {
        $ticket = $this->ticketService()->createForClient(
            $request->all(),
            $this->authenticatedUserId(),
        );

        return Response::json([
            'status' => 'success',
            'data' => $ticket->toArray(),
        ], 201);
    }

    public function show(Request $request, string $ticketId): Response
    {
        $ticket = $this->ticketService()->findOrFail($this->validTicketId($ticketId));

        if ($ticket->clientId() !== $this->authenticatedUserId()) {
            throw new AuthorizationException();
        }

        return Response::json([
            'status' => 'success',
            'data' => $ticket->toArray(),
        ]);
    }

    public function updateStatus(Request $request, string $ticketId): Response
    {
        $ticket = $this->ticketService()->findOrFail($this->validTicketId($ticketId));

        if ($ticket->clientId() !== $this->authenticatedUserId()) {
            throw new AuthorizationException();
        }

        $status = trim((string) ($request->input('status') ?? ''));
        if ($status === '') {
            throw new ValidationException('Données de ticket invalides.', [
                'status' => 'Le statut est requis.',
            ]);
        }

        $updatedTicket = $this->ticketService()->transitionStatus($ticket->id() ?? 0, $status);

        return Response::json([
            'status' => 'success',
            'data' => $updatedTicket->toArray(),
        ]);
    }

    public function assign(Request $request, string $ticketId): Response
    {
        $ticket = $this->ticketService()->findOrFail($this->validTicketId($ticketId));

        if ($ticket->clientId() !== $this->authenticatedUserId()) {
            throw new AuthorizationException();
        }

        $agentId = $request->input('agent_id');
        if ($agentId === null || $agentId === '') {
            $updatedTicket = $this->ticketService()->autoAssign($ticket->id() ?? 0);
        } else {
            if (!is_numeric($agentId) || (int) $agentId < 1) {
                throw new ValidationException('Assignation invalide.', [
                    'agent_id' => 'L’identifiant de l’agent est requis.',
                ]);
            }

            $updatedTicket = $this->ticketService()->assignToAgent($ticket->id() ?? 0, (int) $agentId);
        }

        return Response::json([
            'status' => 'success',
            'data' => $updatedTicket->toArray(),
        ]);
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

    private function ticketService(): TicketService
    {
        return new TicketService(
            new TicketRepository(),
            new TicketFactory(),
            new TicketValidator(),
        );
    }
}