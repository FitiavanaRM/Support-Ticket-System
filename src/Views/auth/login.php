<?php

declare(strict_types=1);

// Vue de la page de connexion. Elle utilise un design centré, un effet glassmorphism
// discret et une carte moderne pour une expérience professionnelle.

$title = 'Connexion';
$pageTitle = 'Connexion';
$pageDescription = 'Connectez-vous pour accéder à votre espace support.';

$errors = $errors ?? [];
$old = $old ?? [];

ob_start();
?>
<div class="min-vh-100 d-flex align-items-center justify-content-center px-3 py-5 auth-page">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 520px; width: 100%; background: rgba(255,255,255,0.92); backdrop-filter: blur(24px);">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 72px; height: 72px; box-shadow: 0 18px 35px rgba(37, 99, 235, 0.18);">
                    <i class="bi bi-shield-lock-fill fs-2"></i>
                </div>
                <h2 class="h4 fw-bold mb-2">Bienvenue</h2>
                <p class="text-muted mb-0">Connectez-vous à votre compte pour gérer vos tickets.</p>
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

            <form action="/login" method="post" class="mb-4">
                <div class="mb-3">
                    <label for="email" class="form-label text-secondary">Adresse e-mail</label>
                    <input type="email" class="form-control form-control-lg shadow-sm" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="votre@email.com" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label text-secondary">Mot de passe</label>
                    <input type="password" class="form-control form-control-lg shadow-sm" id="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">Se connecter</button>
            </form>

            <div class="text-center">
                <p class="text-muted mb-0">Pas encore de compte ? <a href="/register" class="text-primary text-decoration-none">Créer un compte</a></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
