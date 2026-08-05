<?php

declare(strict_types=1);

$title = 'Accès refusé';
$pageTitle = 'Accès refusé';
$pageDescription = 'Vous n’êtes pas autorisé à accéder à cette page.';

ob_start();
?>
<div class="card p-4">
    <div class="text-center py-5">
        <div class="mb-4">
            <span class="badge bg-danger rounded-pill px-3 py-2">403</span>
        </div>
        <h2 class="mb-3">Accès refusé</h2>
        <p class="text-muted mb-4"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <a href="/" class="btn btn-primary">Retour au tableau de bord</a>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
