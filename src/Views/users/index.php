<?php

declare(strict_types=1);

// Vue de gestion des utilisateurs. Affiche un tableau utilisateur clair et
// simple avec rôle et statut.

$title = 'Utilisateurs';
$pageTitle = 'Utilisateurs';
$pageDescription = 'Liste des agents et administrateurs du système.';

ob_start();
?>
<div class="card p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="mb-0">Utilisateurs</h5>
            <p class="text-muted small mb-0">Contrôlez les rôles des membres et consultez leur activité.</p>
        </div>
        <button class="btn btn-primary btn-sm">Ajouter un utilisateur</button>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Rotsy Client</td>
                    <td>client@demo.test</td>
                    <td><span class="badge badge-priority-4">CLIENT</span></td>
                    <td><span class="badge bg-success">Actif</span></td>
                    <td><button class="btn btn-sm btn-outline-primary">Voir</button></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Nomena Agent</td>
                    <td>agent@demo.test</td>
                    <td><span class="badge badge-priority-3">AGENT</span></td>
                    <td><span class="badge bg-success">Actif</span></td>
                    <td><button class="btn btn-sm btn-outline-primary">Voir</button></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Hery Superviseur</td>
                    <td>superviseur@demo.test</td>
                    <td><span class="badge badge-priority-2">SUPERVISOR</span></td>
                    <td><span class="badge bg-success">Actif</span></td>
                    <td><button class="btn btn-sm btn-outline-primary">Voir</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
