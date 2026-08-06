<?php

declare(strict_types=1);

// Vue pour la liste des tickets. Affiche un tableau Bootstrap moderne avec des
// badges de statut et de priorité.

$title = 'Tickets';
$pageTitle = 'Tickets';
$pageDescription = 'Liste complète des demandes de support et leur état actuel.';

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

    <ul class="list-unstyled activity-list mb-0">
        <li class="activity-item d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="act-icon act-icon--created d-flex align-items-center justify-content-center">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div class="activity-body">
                    <div class="activity-title fw-bold">#001 — Impossible d'accéder à mon compte</div>
                    <div class="activity-desc text-muted small">Rotsy Client — Critique</div>
                </div>
            </div>
            <div class="activity-time text-muted small">Il y a 2h</div>
        </li>

        <li class="activity-item d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="act-icon act-icon--assigned d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="activity-body">
                    <div class="activity-title fw-bold">#002 — Problème d’envoi d’emails</div>
                    <div class="activity-desc text-muted small">Maria B. — Haute</div>
                </div>
            </div>
            <div class="activity-time text-muted small">Il y a 6h</div>
        </li>

        <li class="activity-item d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="act-icon act-icon--resolved d-flex align-items-center justify-content-center">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="activity-body">
                    <div class="activity-title fw-bold">#003 — Signalement d’un bug d’affichage</div>
                    <div class="activity-desc text-muted small">Paul T. — Moyenne</div>
                </div>
            </div>
            <div class="activity-time text-muted small">Hier</div>
        </li>
    </ul>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
