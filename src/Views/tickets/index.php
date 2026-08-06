<?php

declare(strict_types=1);

use App\Models\Ticket;
use App\Repositories\UserRepository;

// Vue pour la liste des tickets. Affiche un tableau Bootstrap moderne avec des
// badges de statut et de priorité.

$title = 'Tickets';
$pageTitle = 'Tickets';
$pageDescription = 'Liste complète des demandes de support et leur état actuel.';

/** @var list<Ticket> $tickets */
/** @var \App\Models\User|null $currentUser */

$ticketRepository = new UserRepository();

function ticketStatusBadge(string $status): string
{
    return match ($status) {
        Ticket::STATUS_OPEN => '<span class="badge bg-info">Ouvert</span>',
        Ticket::STATUS_ASSIGNED => '<span class="badge bg-primary">Assigné</span>',
        Ticket::STATUS_IN_PROGRESS => '<span class="badge bg-warning text-dark">En cours</span>',
        Ticket::STATUS_RESOLVED => '<span class="badge bg-success">Résolu</span>',
        Ticket::STATUS_CLOSED => '<span class="badge bg-secondary">Fermé</span>',
        default => '<span class="badge bg-dark">Inconnu</span>',
    };
}

ob_start();
?>
<div class="card activity-card p-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-ticket-detailed fs-4"></i>
            <h5 class="mb-0">Tickets</h5>
        </div>
        <a href="/tickets" class="btn btn-primary btn-sm">Nouveau ticket</a>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="text-center text-muted py-5">Aucun ticket trouvé pour votre compte.</div>
    <?php else: ?>
        <?php $userRepository = new UserRepository(); ?>
        <ul class="list-unstyled activity-list mb-0">
            <?php foreach ($tickets as $ticket): ?>
                <?php $agentName = $ticket->agentId() !== null ? ($userRepository->findById($ticket->agentId())?->name() ?? 'Non assigné') : 'Non assigné'; ?>
                <li class="activity-item d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="act-icon act-icon--created d-flex align-items-center justify-content-center">
                            <i class="bi bi-ticket-fill"></i>
                        </div>
                        <div class="activity-body">
                            <div class="activity-title fw-bold">#<?= htmlspecialchars((string) $ticket->id(), ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($ticket->subject(), ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="activity-desc text-muted small"><?= htmlspecialchars($agentName, ENT_QUOTES, 'UTF-8') ?> • <?= htmlspecialchars($ticket->categoryName() ?? 'Catégorie inconnue', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    </div>
                    <div class="activity-time text-muted small"><?= $ticketStatusBadge($ticket->status()) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
