<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\TicketNotFoundException;
use App\Exceptions\ValidationException;
use App\Factories\TicketFactory;
use App\Interfaces\Repositories\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Observers\LogginObserver;
use App\Observers\NotificationObserver;
use App\Repositories\AssignmentSettingsRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\Services\AssignmentService;
use App\Services\AssignmentSettingsService;
use App\States\TicketState;
use App\Validation\TicketValidator;

// Coordonne les cas d'utilisation liés à la création et à la consultation des tickets
final class TicketService
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly TicketFactory $ticketFactory,
        private readonly TicketValidator $ticketValidator,
    ) {
    }

    /** @param array<string, mixed> $data */
    public function createForClient(array $data, int $clientId): Ticket
    {
        $this->ticketValidator->validateForCreation($data);

        $ticket = $this->ticketFactory->createForClient($data, $clientId);

        return $this->ticketRepository->create($ticket);
    }

    public function findOrFail(int $ticketId): Ticket
    {
        $ticket = $this->ticketRepository->findById($ticketId);

        if ($ticket === null) {
            throw new TicketNotFoundException($ticketId);
        }

        return $ticket;
    }

    public function transitionStatus(int $ticketId, string $targetStatus): Ticket
    {
        $ticket = $this->findOrFail($ticketId);
        $previousStatus = $ticket->status();

        TicketState::assertTransition($previousStatus, $targetStatus);

        $updatedTicket = $this->ticketRepository->updateStatus($ticketId, $targetStatus);

        $actorId = $ticket->clientId();
        $observer = new LogginObserver();
        $observer->onTicketStatusChanged($actorId, $ticketId, $previousStatus, $targetStatus);

        // La notification doit être adressée au destinataire métier du ticket,
        // c'est-à-dire l'agent assigné si présent, sinon le client.
        $notificationRecipientId = $ticket->agentId() ?? $ticket->clientId();

        $notificationObserver = new NotificationObserver(
            new NotificationRepository(),
            new \App\Factories\NotificationFactory(),
        );
        $notificationObserver->onTicketStatusChanged($notificationRecipientId, $ticketId, $targetStatus);

        return $updatedTicket;
    }

    public function assignToAgent(int $ticketId, int $agentId): Ticket
    {
        $ticket = $this->findOrFail($ticketId);

        TicketState::assertTransition($ticket->status(), Ticket::STATUS_ASSIGNED);

        $agentIds = (new UserRepository())->findAgentIds();
        if (!in_array($agentId, $agentIds, true)) {
            throw new ValidationException('Assignation invalide.', [
                'agent_id' => 'Cet agent n’est pas disponible pour cette affectation.',
            ]);
        }

        return $this->ticketRepository->assignAgent($ticketId, $agentId, Ticket::STATUS_ASSIGNED);
    }

    public function autoAssign(int $ticketId, ?string $strategyCode = null, ?int $lastAssignedAgentId = null): Ticket
    {
        $ticket = $this->findOrFail($ticketId);

        TicketState::assertTransition($ticket->status(), Ticket::STATUS_ASSIGNED);

        $assignmentSettingsService = new AssignmentSettingsService(
            new AssignmentSettingsRepository(),
        );

        $strategyCode = $strategyCode !== null
            ? $assignmentSettingsService->normalizeStrategyCode($strategyCode)
            : $assignmentSettingsService->currentStrategyCode();

        $settings = $assignmentSettingsService->current();
        $lastAssignedAgentId = $lastAssignedAgentId ?? $settings->lastAgentId();

        $availableAgents = (new UserRepository())->findAgentIds();
        $agentId = (new AssignmentService())->assign($strategyCode, $availableAgents, $lastAssignedAgentId);

        if ($agentId === null) {
            throw new ValidationException('Assignation invalide.', [
                'agent_id' => 'Aucun agent disponible pour cette affectation.',
            ]);
        }

        $updatedTicket = $this->ticketRepository->assignAgent($ticketId, $agentId, Ticket::STATUS_ASSIGNED);

        if ($strategyCode === 'ROUND_ROBIN' || $strategyCode === 'ROUNDROBIN') {
            $assignmentSettingsService->updateLastAgent($agentId);
        }

        return $updatedTicket;
    }

    /** @return list<Ticket> */
    public function listForClient(int $clientId): array
    {
        return $this->ticketRepository->findByClientId($clientId);
    }
}