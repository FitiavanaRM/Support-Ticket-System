<?php

declare(strict_types=1);

// Vue de gestion des utilisateurs. Affiche un tableau utilisateur clair et
// simple avec rôle et statut.

 $title = 'Utilisateurs';
 $pageTitle = 'Utilisateurs';
 $pageDescription = 'Liste des agents et administrateurs du système.';

 $userRepo = new \App\Repositories\UserRepository();
 $users = $userRepo->findAll();

 ob_start();
 ?>
 <div class="card activity-card p-3">
     <div class="d-flex align-items-center justify-content-between mb-3">
         <div class="d-flex align-items-center gap-2">
             <i class="bi bi-people fs-4"></i>
             <h5 class="mb-0">Utilisateurs</h5>
         </div>
         <a href="/users/new" class="btn btn-primary btn-sm">Ajouter un utilisateur</a>
     </div>

     <ul class="list-unstyled activity-list mb-0">
         <?php foreach ($users as $u): ?>
             <li class="activity-item d-flex align-items-center justify-content-between py-3">
                 <div class="d-flex align-items-center gap-3">
                     <div class="act-icon act-icon--assigned d-flex align-items-center justify-content-center">
                         <i class="bi bi-person-circle"></i>
                     </div>
                     <div class="activity-body">
                         <div class="activity-title fw-bold"><?= htmlspecialchars($u->name(), ENT_QUOTES, 'UTF-8') ?> <small class="text-muted">(<?= htmlspecialchars($u->toArray()['role'], ENT_QUOTES, 'UTF-8') ?>)</small></div>
                         <div class="activity-desc text-muted small"><?= htmlspecialchars($u->email(), ENT_QUOTES, 'UTF-8') ?></div>
                     </div>
                 </div>
                 <div class="activity-time text-muted small"><?= $u->isActive() ? 'Actif' : 'Inactif' ?></div>
             </li>
         <?php endforeach; ?>
     </ul>
 </div>
 <?php
 $content = ob_get_clean();
 require __DIR__ . '/../layouts/main.php';
