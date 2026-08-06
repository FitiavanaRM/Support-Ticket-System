<?php

declare(strict_types=1);

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;

// Vue du détail d'un ticket. Elle présente les informations principales, le
// fil de discussion et une timeline simple des changements de statut.

$title = 'Détail du ticket';
$pageTitle = 'Détail du ticket';
$pageDescription = 'Consultez les informations du ticket, les messages et l’historique.';

/** @var Ticket $ticket */
/** @var list<Message> $messages */
/** @var User|null $agent */
/** @var User $client */
/** @var array<int, string> $authorNames */

function ticketStatusLabel(string $status): string
{
    return match ($status) {
        Ticket::STATUS_OPEN => 'Ouvert',
        Ticket::STATUS_ASSIGNED => 'Assigné',
        Ticket::STATUS_IN_PROGRESS => 'En cours',
        Ticket::STATUS_RESOLVED => 'Résolu',
        Ticket::STATUS_CLOSED => 'Fermé',
        default => 'Inconnu',
    };
}

function ticketStatusBadgeClass(string $status): string
{
    return match ($status) {
        Ticket::STATUS_OPEN => 'bg-info',
        Ticket::STATUS_ASSIGNED => 'bg-primary',
        Ticket::STATUS_IN_PROGRESS => 'bg-warning text-dark',
        Ticket::STATUS_RESOLVED => 'bg-success',
        Ticket::STATUS_CLOSED => 'bg-secondary',
        default => 'bg-dark',
    };
}

function formatRelativeTime(?string $dateString): string
{
    if ($dateString === null) {
        return 'À l’instant';
    }

    $date = new DateTimeImmutable($dateString);
    $diff = $date->diff(new DateTimeImmutable());

    if ($diff->d > 0) {
        return sprintf('Il y a %d jour%s', $diff->d, $diff->d > 1 ? 's' : '');
    }

    if ($diff->h > 0) {
        return sprintf('Il y a %d h', $diff->h);
    }

    if ($diff->i > 0) {
        return sprintf('Il y a %d min', $diff->i);
    }

    return 'À l’instant';
}

ob_start();
?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div>
                    <span class="badge <?= htmlspecialchars(ticketStatusBadgeClass($ticket->status()), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ticketStatusLabel($ticket->status()), ENT_QUOTES, 'UTF-8') ?></span>
                    <h4 class="mt-3 mb-2"><?= htmlspecialchars($ticket->subject(), ENT_QUOTES, 'UTF-8') ?></h4>
                    <p class="text-muted mb-0">Catégorie : <?= htmlspecialchars($ticket->categoryName() ?? 'Inconnue', ENT_QUOTES, 'UTF-8') ?> • Priorité : <?= htmlspecialchars($ticket->priorityName() ?? 'Inconnue', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div>
                    <a href="/tickets" class="btn btn-outline-secondary btn-sm">Retour</a>
                </div>
            </div>
            <div class="mb-4">
                <p class="mb-0"><?= nl2br(htmlspecialchars($ticket->description(), ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-body rounded-4 border">
                        <div class="text-muted small mb-2">Client</div>
                        <div class="fw-semibold"><?= htmlspecialchars($client->name(), ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-body rounded-4 border">
                        <div class="text-muted small mb-2">Assigné à</div>
                        <div class="fw-semibold"><?= htmlspecialchars($agent?->name() ?? 'Non assigné', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-4 mb-4">
            <h5 class="mb-3">Messages</h5>
            <?php if (empty($messages)): ?>
                <div class="text-muted">Aucun message pour ce ticket.</div>
            <?php else: ?>
                <div class="border-bottom pb-3 mb-3">
                    <?php foreach ($messages as $message): ?>
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <?= htmlspecialchars(substr(($authorNames[$message->authorId()] ?? 'U'), 0, 1), ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars($authorNames[$message->authorId()] ?? 'Utilisateur inconnu', ENT_QUOTES, 'UTF-8') ?></div>
                                <p class="text-muted small mb-1"><?= nl2br(htmlspecialchars($message->content(), ENT_QUOTES, 'UTF-8')) ?></p>
                                <div class="text-muted small"><?= htmlspecialchars(formatRelativeTime($message->createdAt()?->format('Y-m-d H:i:s')), ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="/tickets/<?= (int) $ticket->id() ?>/messages" method="post">
                <div class="mb-3">
                    <label for="message" class="form-label">Répondre au ticket</label>
                    <textarea id="message" name="content" class="form-control" rows="5" placeholder="Écrire une réponse..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4 mb-4">
            <h5 class="mb-4">Historique</h5>
            <div class="timeline-item">
                <div class="fw-semibold">Créé le <?= htmlspecialchars($ticket->createdAt()?->format('d/m/Y H:i') ?? 'Date inconnue', ENT_QUOTES, 'UTF-8') ?></div>
                <div class="text-muted small">Ticket créé</div>
            </div>
            <?php if ($ticket->updatedAt() !== null): ?>
                <div class="timeline-item">
                    <div class="fw-semibold">Dernière mise à jour</div>
                    <div class="text-muted small"><?= htmlspecialchars($ticket->updatedAt()?->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php endif; ?>
            <?php if ($ticket->resolvedAt() !== null): ?>
                <div class="timeline-item">
                    <div class="fw-semibold">Résolu le <?= htmlspecialchars($ticket->resolvedAt()?->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="text-muted small">Statut résolu</div>
                </div>
            <?php endif; ?>
            <?php if ($ticket->closedAt() !== null): ?>
                <div class="timeline-item">
                    <div class="fw-semibold">Fermé le <?= htmlspecialchars($ticket->closedAt()?->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="text-muted small">Statut fermé</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
