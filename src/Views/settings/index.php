<?php

declare(strict_types=1);

// Vue de la configuration de l'assignation. Contient un formulaire Bootstrap
// pour choisir la stratégie active de distribution des tickets.

$title = 'Paramètres d’assignation';
$pageTitle = 'Paramètres d’assignation';
$pageDescription = 'Sélectionnez la stratégie d’assignation des tickets pour l’équipe.';

ob_start();
?>
<div class="card p-4">
    <div class="mb-4">
        <h5 class="mb-0">Stratégie d’assignation</h5>
        <p class="text-muted small mb-0">Choisissez comment les tickets sont distribués aux agents.</p>
    </div>

    <form action="/assignment-settings" method="post">
        <div class="row g-3">
            <div class="col-12 col-md-8">
                <label for="strategy_code" class="form-label">Stratégie</label>
                <select id="strategy_code" name="strategy_code" class="form-select">
                    <option value="MANUAL">MANUAL</option>
                    <option value="ROUND_ROBIN">ROUND_ROBIN</option>
                    <option value="WORKLOAD">WORKLOAD</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
            </div>
        </div>
    </form>

    <div class="mt-4 p-3 bg-body rounded-4 border">
        <h6 class="fw-semibold">Informations</h6>
        <p class="text-muted small mb-0">La stratégie Round Robin utilise l’agent affecté précédemment pour répartir équitablement la charge.</p>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
