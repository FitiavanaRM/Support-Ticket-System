<?php

declare(strict_types=1);

// Vue de la page d'inscription. Elle affiche un formulaire moderne et gère
// l'affichage des erreurs ainsi que la conservation des valeurs saisies.

$title = 'Inscription';
$pageTitle = 'Inscription';
$pageDescription = 'Créez votre compte pour commencer à créer et suivre vos demandes.';

$errors = $errors ?? [];
$old = $old ?? [];

ob_start();
?>
<div class="min-vh-100 d-flex align-items-center justify-content-center px-3 py-5 auth-page">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 580px; width: 100%; background: rgba(255,255,255,0.92); backdrop-filter: blur(24px);">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 72px; height: 72px; box-shadow: 0 18px 35px rgba(37, 99, 235, 0.18);">
                    <i class="bi bi-pencil-square fs-2"></i>
                </div>
                <h2 class="h4 fw-bold mb-2">Créer un compte</h2>
                <p class="text-muted mb-0">Inscrivez-vous pour accéder au support et suivre vos demandes.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger rounded-4 shadow-sm mb-4">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/register" method="post" class="mb-4">
                <div class="mb-3">
                    <label for="full_name" class="form-label text-secondary">Nom complet</label>
                    <input type="text" class="form-control form-control-lg shadow-sm" id="full_name" name="full_name" value="<?= htmlspecialchars($old['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Jean Dupont" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label text-secondary">Adresse e-mail</label>
                    <input type="email" class="form-control form-control-lg shadow-sm" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="votre@email.com" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label text-secondary">Mot de passe</label>
                        <input type="password" class="form-control form-control-lg shadow-sm" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label text-secondary">Confirmation</label>
                        <input type="password" class="form-control form-control-lg shadow-sm" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold mt-4">S'inscrire</button>
            </form>

            <div class="text-center">
                <p class="text-muted mb-0">Déjà un compte ? <a href="/login" class="text-primary text-decoration-none">Se connecter</a></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
