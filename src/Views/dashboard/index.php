<?php

declare(strict_types=1);

use App\Models\Ticket;
use App\Repositories\UserRepository;

// Vue du tableau de bord. Affiche des cartes de statistiques, une vue rapide des
// tickets récents et une navigation propre vers les autres sections.

$title = 'Tableau de bord';
$pageTitle = 'Tableau de bord';
$pageDescription = 'Vue d’ensemble de vos tickets et de l’activité récente.';

/** @var array<string, int> $stats */
/** @var list<array{ticket: Ticket, agentName: string}>|list<Ticket> $recentTickets */

$agentRepository = new UserRepository();

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

function statusIconClass(string $status): array
{
    return match ($status) {
        Ticket::STATUS_OPEN => ['bi-envelope-open-fill', 'act-icon--created'],
        Ticket::STATUS_ASSIGNED => ['bi-person-badge-fill', 'act-icon--assigned'],
        Ticket::STATUS_IN_PROGRESS => ['bi-arrow-repeat', 'act-icon--message'],
        Ticket::STATUS_RESOLVED => ['bi-check-circle-fill', 'act-icon--resolved'],
        Ticket::STATUS_CLOSED => ['bi-x-circle-fill', 'act-icon--closed'],
        default => ['bi-question-circle-fill', 'act-icon--created'],
    };
}

function statusLabel(string $status): string
{
    return match ($status) {
        Ticket::STATUS_OPEN => 'Nouveau ticket',
        Ticket::STATUS_ASSIGNED => 'Affecté',
        Ticket::STATUS_IN_PROGRESS => 'En cours',
        Ticket::STATUS_RESOLVED => 'Résolu',
        Ticket::STATUS_CLOSED => 'Fermé',
        default => 'Inconnu',
    };
}

ob_start();
?>
<div class="row g-4">
    <div class="col-12">
        <div class="row g-3 dashboard-stats">
            <div class="col-12 col-md-6 col-lg-3 stat-col">
                <div class="stat-card h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="stat-icon bg-total mb-3">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                    <div class="stat-number stat-total mb-1"><?= htmlspecialchars((string) $stats['total'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="stat-label mb-2">Total tickets</div>
                    <div class="text-muted small">Total des tickets créés ou assignés</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 stat-col">
                <div class="stat-card h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="stat-icon bg-open mb-3">
                        <i class="bi bi-envelope-open-fill"></i>
                    </div>
                    <div class="stat-number stat-open mb-1"><?= htmlspecialchars((string) $stats['open'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="stat-label mb-2">Tickets ouverts</div>
                    <div class="text-muted small">Tickets en attente d’intervention</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 stat-col">
                <div class="stat-card h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="stat-icon bg-progress mb-3">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div class="stat-number stat-progress mb-1"><?= htmlspecialchars((string) ($stats['assigned'] + $stats['in_progress']), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="stat-label mb-2">Tickets en cours</div>
                    <div class="text-muted small">Tickets actuellement traités</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 stat-col">
                <div class="stat-card h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="stat-icon bg-resolved mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-number stat-resolved mb-1"><?= htmlspecialchars((string) ($stats['resolved'] + $stats['closed']), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="stat-label mb-2">Tickets résolus</div>
                    <div class="text-muted small">Tickets fermés ou résolus</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card activity-card p-3 animate-fadeInUp">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history fs-4"></i>
                    <h5 class="mb-0">Activité récente</h5>
                </div>
                <a href="/tickets" class="btn btn-outline-secondary btn-sm">Voir tous</a>
            </div>

            <ul class="list-unstyled activity-list mb-0">
                <?php if (empty($recentTickets)): ?>
                    <li class="activity-item py-4 text-center text-muted">Aucune activité récente à afficher.</li>
                <?php else: ?>
                    <?php foreach ($recentTickets as $entry): ?>
                        <?php $ticket = $entry['ticket'] ?? $entry; ?>
                        <?php if (!($ticket instanceof Ticket)) { continue; } ?>
                        <?php [$iconClass, $badgeClass] = statusIconClass($ticket->status()); ?>
                        <?php $agentName = $entry['agentName'] ?? ($ticket->agentId() !== null ? ($agentRepository->findById($ticket->agentId())?->name() ?? 'Agent inconnu') : 'Non assigné'); ?>
                        <li class="activity-item d-flex align-items-center justify-content-between py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="act-icon <?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?> d-flex align-items-center justify-content-center">
                                    <i class="bi <?= htmlspecialchars($iconClass, ENT_QUOTES, 'UTF-8') ?>"></i>
                                </div>
                                <div class="activity-body">
                                    <div class="activity-title fw-bold">#<?= htmlspecialchars((string) $ticket->id(), ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($ticket->subject(), ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="activity-desc text-muted small"><?= htmlspecialchars($ticket->categoryName() ?? 'Catégorie inconnue', ENT_QUOTES, 'UTF-8') ?> • <?= htmlspecialchars($ticket->priorityName() ?? 'Priorité inconnue', ENT_QUOTES, 'UTF-8') ?> • <?= htmlspecialchars($agentName, ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            </div>
                            <div class="activity-time text-muted small"><?= htmlspecialchars(formatRelativeTime($ticket->updatedAt()?->format('Y-m-d H:i:s')), ENT_QUOTES, 'UTF-8') ?></div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
