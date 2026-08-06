<?php

declare(strict_types=1);

/** @var \App\Models\User $user */
$title = 'Utilisateur';
$pageTitle = 'Détails utilisateur';

ob_start();
?>
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1"><?= htmlspecialchars($user->name(), ENT_QUOTES, 'UTF-8') ?></h5>
            <p class="text-muted small mb-0"><?= htmlspecialchars($user->email(), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="text-end">
            <span class="badge bg-secondary"><?= htmlspecialchars($user->toArray()['role'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>

    <div class="mb-3">
        <strong>Statut :</strong> <?= $user->isActive() ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-danger">Inactif</span>' ?>
    </div>

    <div class="mt-4">
        <a href="/users" class="btn btn-outline-secondary">Retour</a>
        <button class="btn btn-primary">Actions (à implémenter)</button>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
