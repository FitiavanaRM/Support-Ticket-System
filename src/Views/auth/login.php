<?php

declare(strict_types=1);

// Vue de la page de connexion. Elle utilise un style SaaS moderne et centré,
// avec des icônes Bootstrap, un dégradé plein écran et une carte élégante.

$title = 'Connexion';
$pageTitle = 'Connexion';
$pageDescription = 'Connectez-vous pour accéder à votre espace support.';

$errors = $errors ?? [];
$old = $old ?? [];

ob_start();
?>
<div class="auth-page min-vh-100 d-flex align-items-center justify-content-center px-3 py-5">
    <div class="auth-card shadow-xl rounded-4 overflow-hidden border border-white border-opacity-10">
        <div class="auth-card__brand text-center px-5 pt-5">
            <div class="auth-logo mb-3">
                <span class="auth-logo__icon"><i class="bi bi-ticket-detailed-fill"></i></span>
            </div>
            <h1 class="h3 fw-bold mb-1">Support Ticket Pro</h1>
            <p class="text-muted mb-4">Bienvenue, connectez-vous pour gérer vos demandes de support rapidement.</p>
        </div>

        <div class="auth-card__body px-5 pb-5">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger rounded-4 shadow-sm mb-4">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/login" method="post" class="row g-3">
                <div class="col-12">
                    <label for="email" class="form-label text-secondary">Email</label>
                    <div class="input-group input-group-lg auth-input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" class="form-control form-control-lg rounded-start-pill rounded-end-pill border-start-0" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Entrer votre email" required>
                    </div>
                </div>
                <div class="col-12">
                    <label for="password" class="form-label text-secondary">Mot de passe</label>
                    <div class="input-group input-group-lg auth-input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control form-control-lg rounded-start-pill rounded-end-pill border-start-0" id="password" name="password" placeholder="Entrer votre mot de passe" required>
                    </div>
                </div>
                <div class="col-12 d-flex align-items-center justify-content-between">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                    </div>
                    <a href="#" class="text-primary text-decoration-none">Mot de passe oublié ?</a>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">SE CONNECTER</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="text-muted mb-0">Vous n'avez pas encore de compte ? <a href="/register" class="fw-semibold text-primary text-decoration-none">Créer un compte</a></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
