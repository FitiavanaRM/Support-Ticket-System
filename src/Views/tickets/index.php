<?php

declare(strict_types=1);

// Vue pour la liste des tickets. Affiche un tableau Bootstrap moderne avec des
// badges de statut et de priorité.

$title = 'Tickets';
$pageTitle = 'Tickets';
$pageDescription = 'Liste complète des demandes de support et leur état actuel.';

ob_start();
?>
<div class="card p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="mb-0">Tickets</h5>
            <p class="text-muted small mb-0">Toutes les demandes classées par priorité et statut.</p>
        </div>
        <a href="/tickets" class="btn btn-primary btn-sm">Nouveau ticket</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sujet</th>
                    <th>Client</th>
                    <th>Agent</th>
                    <th>Statut</th>
                    <th>Priorité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td>Impossible d'accéder à mon compte</td>
                    <td>Rotsy Client</td>
                    <td>Agent 1</td>
                    <td><span class="badge badge-status open">Open</span></td>
                    <td><span class="badge badge-priority-1">Critique</span></td>
                    <td>
                        <a href="/tickets/1" class="btn btn-sm btn-outline-primary">Voir</a>
                    </td>
                </tr>
                <tr>
                    <td>002</td>
                    <td>Problème d’envoi d’emails</td>
                    <td>Maria B.</td>
                    <td>Agent 2</td>
                    <td><span class="badge badge-status in_progress">In progress</span></td>
                    <td><span class="badge badge-priority-2">Haute</span></td>
                    <td>
                        <a href="/tickets/2" class="btn btn-sm btn-outline-primary">Voir</a>
                    </td>
                </tr>
                <tr>
                    <td>003</td>
                    <td>Signalement d’un bug d’affichage</td>
                    <td>Paul T.</td>
                    <td>Agent 1</td>
                    <td><span class="badge badge-status resolved">Resolved</span></td>
                    <td><span class="badge badge-priority-3">Moyenne</span></td>
                    <td>
                        <a href="/tickets/3" class="btn btn-sm btn-outline-primary">Voir</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
