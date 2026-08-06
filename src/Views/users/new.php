<?php

declare(strict_types=1);

$title = 'Ajouter un utilisateur';
$pageTitle = 'Ajouter un utilisateur';

$errors = $errors ?? [];
$old = $old ?? [];

ob_start();
?>
<div class="card p-4">
    <h5 class="mb-3">Créer un nouvel utilisateur</h5>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/users" method="post">
        <div class="mb-3">
            <label class="form-label">Nom complet</label>
            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($old['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select name="role" class="form-select">
                <?php $sel = $old['role'] ?? 'AGENT'; ?>
                <option value="CLIENT" <?= $sel === 'CLIENT' ? 'selected' : '' ?>>CLIENT</option>
                <option value="AGENT" <?= $sel === 'AGENT' ? 'selected' : '' ?>>AGENT</option>
                <option value="SUPERVISOR" <?= $sel === 'SUPERVISOR' ? 'selected' : '' ?>>SUPERVISOR</option>
                <option value="ADMIN" <?= $sel === 'ADMIN' ? 'selected' : '' ?>>ADMIN</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Créer</button>
            <a href="/users" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
