<?php

declare(strict_types=1);

// Vue du détail d'un ticket. Elle présente les informations principales, le
// fil de discussion et une timeline simple des changements de statut.

$title = 'Détail du ticket';
$pageTitle = 'Détail du ticket';
$pageDescription = 'Consultez les informations du ticket, les messages et l’historique.';

ob_start();
?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div>
                    <span class="badge badge-status open">Open</span>
                    <h4 class="mt-3 mb-2">Impossible d'accéder à mon compte</h4>
                    <p class="text-muted mb-0">Catégorie : Logiciel • Priorité : Haute</p>
                </div>
                <div>
                    <a href="/tickets" class="btn btn-outline-secondary btn-sm">Retour</a>
                </div>
            </div>
            <div class="mb-4">
                <p class="mb-0">Le client rencontre une erreur lors de la connexion. Le ticket doit être investigué par l’équipe en charge du support.</p>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-body rounded-4 border">
                        <div class="text-muted small mb-2">Client</div>
                        <div class="fw-semibold">Rotsy Client</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-body rounded-4 border">
                        <div class="text-muted small mb-2">Assigné à</div>
                        <div class="fw-semibold">Agent 1</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-4 mb-4">
            <h5 class="mb-3">Messages</h5>
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">R</div>
                    <div>
                        <div class="fw-semibold">Rotsy Client</div>
                        <p class="text-muted small mb-1">Bonjour, je ne peux plus me connecter depuis ce matin.</p>
                        <div class="text-muted small">Il y a 2h</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <div class="avatar rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">A</div>
                    <div>
                        <div class="fw-semibold">Agent 1</div>
                        <p class="text-muted small mb-1">Nous vérifions le backend et revenons vers vous.</p>
                        <div class="text-muted small">Il y a 1h</div>
                    </div>
                </div>
            </div>

            <form action="/tickets/1/messages" method="post">
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
                <div class="fw-semibold">Ticket créé</div>
                <div class="text-muted small">Il y a 4h</div>
            </div>
            <div class="timeline-item">
                <div class="fw-semibold">Assigné à Agent 1</div>
                <div class="text-muted small">Il y a 3h</div>
            </div>
            <div class="timeline-item">
                <div class="fw-semibold">Statut changé en In progress</div>
                <div class="text-muted small">Il y a 2h</div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
