<?php

declare(strict_types=1);

// Vue du tableau de bord. Affiche des cartes de statistiques, une vue rapide des
// tickets récents et une navigation propre vers les autres sections.

$title = 'Tableau de bord';
$pageTitle = 'Tableau de bord';
$pageDescription = 'Vue d’ensemble de vos tickets et de l’activité récente.';

ob_start();
?>
<div class="row g-4">
    <div class="col-12">
        <div class="row g-3">
            <div class="col-sm-6 col-xl-3">
                <div class="card p-4 card-hero">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="text-muted mb-1">Tickets ouverts</h6>
                            <h3 class="mb-0">12</h3>
                        </div>
                        <span class="badge badge-status open">Open</span>
                    </div>
                    <p class="text-muted small mb-0">Tickets en attente d’interventions.</p>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card p-4 card-hero">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="text-muted mb-1">Tickets en cours</h6>
                            <h3 class="mb-0">8</h3>
                        </div>
                        <span class="badge badge-status in_progress">En cours</span>
                    </div>
                    <p class="text-muted small mb-0">Tickets actuellement traités par vos équipes.</p>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card p-4 card-hero">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="text-muted mb-1">Tickets résolus</h6>
                            <h3 class="mb-0">26</h3>
                        </div>
                        <span class="badge badge-status resolved">Résolu</span>
                    </div>
                    <p class="text-muted small mb-0">Tickets fermés avec satisfaction.</p>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card p-4 card-hero">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="text-muted mb-1">Nouveaux messages</h6>
                            <h3 class="mb-0">14</h3>
                        </div>
                        <span class="badge badge-status assigned">Nouveau</span>
                    </div>
                    <p class="text-muted small mb-0">Messages récents dans vos tickets actifs.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h5 class="mb-0">Derniers tickets</h5>
                    <p class="text-muted small mb-0">Suivi des tickets récents créés par vos clients.</p>
                </div>
                <a href="/tickets" class="btn btn-outline-secondary btn-sm">Voir tous</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Client</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Dernière mise à jour</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Impossible de me connecter</td>
                            <td>Rotsy Client</td>
                            <td><span class="badge badge-status open">Open</span></td>
                            <td><span class="badge badge-priority-2">Haute</span></td>
                            <td>Il y a 1h</td>
                            <td><a href="/tickets/1" class="btn btn-sm btn-outline-primary">Voir</a></td>
                        </tr>
                        <tr>
                            <td>Erreur lors de l’envoi d’un message</td>
                            <td>Marie L.</td>
                            <td><span class="badge badge-status in_progress">In progress</span></td>
                            <td><span class="badge badge-priority-3">Moyenne</span></td>
                            <td>Il y a 3h</td>
                            <td><a href="/tickets/2" class="btn btn-sm btn-outline-primary">Voir</a></td>
                        </tr>
                        <tr>
                            <td>Demande de changement de mot de passe</td>
                            <td>Paul T.</td>
                            <td><span class="badge badge-status resolved">Resolved</span></td>
                            <td><span class="badge badge-priority-4">Basse</span></td>
                            <td>Hier</td>
                            <td><a href="/tickets/3" class="btn btn-sm btn-outline-primary">Voir</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
