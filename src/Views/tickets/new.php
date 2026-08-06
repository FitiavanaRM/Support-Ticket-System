<?php

declare(strict_types=1);

use App\Models\Ticket;

$title = 'Nouveau ticket';
$pageTitle = 'Nouveau ticket';
$pageDescription = 'Créer une nouvelle demande de support.';

/** @var list<array{name: string, id: int}> $categories */
/** @var list<array{name: string, id: int}> $priorities */
/** @var array<string, string> $errors */
/** @var array<string, mixed> $old */

ob_start();
?>
<div class="card p-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div class="text-center text-md-start w-100">
            <h4 class="mb-1">Créer un nouveau ticket</h4>
            <p class="text-muted mb-0">Décrivez votre problème et l’équipe vous répondra rapidement.</p>
        </div>
        <a href="/tickets" class="btn btn-outline-secondary btn-sm ms-auto">Retour</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/tickets" method="post" class="mx-auto" style="max-width: 720px;">
        <div class="mb-3 text-center text-md-start">
            <label for="subject" class="form-label">Sujet</label>
            <input type="text" class="form-control" id="subject" name="subject" maxlength="150" value="<?= htmlspecialchars((string) ($old['subject'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="mb-3 text-center text-md-start">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="row g-3 text-center text-md-start">
            <div class="col-md-6">
                <label for="category_id" class="form-label">Catégorie</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>" <?= (string) ($old['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="priority_id" class="form-label">Priorité</label>
                <select class="form-select" id="priority_id" name="priority_id" required>
                    <option value="">Sélectionner une priorité</option>
                    <?php foreach ($priorities as $priority): ?>
                        <option value="<?= (int) $priority['id'] ?>" <?= (string) ($old['priority_id'] ?? '') === (string) $priority['id'] ? 'selected' : '' ?>><?= htmlspecialchars($priority['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2 justify-content-center justify-content-md-start">
            <button type="submit" class="btn btn-primary">Créer le ticket</button>
            <a href="/tickets" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
