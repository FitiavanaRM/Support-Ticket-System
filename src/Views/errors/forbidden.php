<?php

declare(strict_types=1);

$title = 'Accès refusé';
$pageTitle = 'Accès refusé';
$pageDescription = '';

ob_start();
?>
<div class="card p-4 border-0 shadow-sm">
    <div class="text-center py-5">
        <p class="text-muted mb-4">Vous n’avez pas les droits nécessaires pour accéder à cette page.</p>
        <a href="/" class="btn btn-primary">Retour au tableau de bord</a>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
