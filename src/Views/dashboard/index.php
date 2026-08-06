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
        <div class="row g-3 dashboard-stats">
            <div class="col-12 col-md-6 col-lg-3 stat-col">
                <div class="stat-card h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="stat-icon bg-total mb-3">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                    <div class="stat-number stat-total mb-1">54</div>
                    <div class="stat-label mb-2">Total tickets</div>
                    <div class="text-muted small">Total des tickets créés dans le système</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 stat-col">
                <div class="stat-card h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="stat-icon bg-open mb-3">
                        <i class="bi bi-envelope-open-fill"></i>
                    </div>
                    <div class="stat-number stat-open mb-1">12</div>
                    <div class="stat-label mb-2">Tickets ouverts</div>
                    <div class="text-muted small">Tickets en attente d’intervention</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 stat-col">
                <div class="stat-card h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="stat-icon bg-progress mb-3">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div class="stat-number stat-progress mb-1">8</div>
                    <div class="stat-label mb-2">Tickets en cours</div>
                    <div class="text-muted small">Tickets actuellement traités</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 stat-col">
                <div class="stat-card h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="stat-icon bg-resolved mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-number stat-resolved mb-1">26</div>
                    <div class="stat-label mb-2">Tickets résolus</div>
                    <div class="text-muted small">Tickets fermés avec satisfaction</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent activity - modern card list -->
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
                <li class="activity-item d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="act-icon act-icon--created d-flex align-items-center justify-content-center">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <div class="activity-body">
                            <div class="activity-title fw-bold">Ticket créé: Impossible de me connecter</div>
                            <div class="activity-desc text-muted small">Nouvel utilisateur signalé une impossibilité de connexion.</div>
                        </div>
                    </div>
                    <div class="activity-time text-muted small">Il y a 1h</div>
                </li>

                <li class="activity-item d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="act-icon act-icon--assigned d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div class="activity-body">
                            <div class="activity-title fw-bold">Ticket assigné: Erreur d’envoi</div>
                            <div class="activity-desc text-muted small">Le ticket a été assigné à Marie.</div>
                        </div>
                    </div>
                    <div class="activity-time text-muted small">Il y a 3h</div>
                </li>

                <li class="activity-item d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="act-icon act-icon--message d-flex align-items-center justify-content-center">
                            <i class="bi bi-chat-dots-fill"></i>
                        </div>
                        <div class="activity-body">
                            <div class="activity-title fw-bold">Message ajouté: Demande de précision</div>
                            <div class="activity-desc text-muted small">Le client a ajouté un message au ticket.</div>
                        </div>
                    </div>
                    <div class="activity-time text-muted small">Il y a 4h</div>
                </li>

                <li class="activity-item d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="act-icon act-icon--resolved d-flex align-items-center justify-content-center">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="activity-body">
                            <div class="activity-title fw-bold">Ticket résolu: Mot de passe réinitialisé</div>
                            <div class="activity-desc text-muted small">Le ticket a été résolu et clôturé.</div>
                        </div>
                    </div>
                    <div class="activity-time text-muted small">Hier</div>
                </li>

                <li class="activity-item d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="act-icon act-icon--closed d-flex align-items-center justify-content-center">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div class="activity-body">
                            <div class="activity-title fw-bold">Ticket fermé: Problème non reproductible</div>
                            <div class="activity-desc text-muted small">Fermeture du ticket après investigation.</div>
                        </div>
                    </div>
                    <div class="activity-time text-muted small">Il y a 2 jours</div>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
