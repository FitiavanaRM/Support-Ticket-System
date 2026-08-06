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
        <style>
            /* Scoped styles for the login card layout */
            .auth-card {
                width: 420px;
                max-width: calc(100% - 48px);
                background: var(--bg-surface);
                color: var(--text-primary);
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 2.25rem 1.75rem;
                gap: 0.75rem;
            }

            .auth-card__brand {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
                padding: 0;
                margin-bottom: 0.25rem;
            }

            .auth-logo {
                width: 92px;
                height: 92px;
                border-radius: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
                box-shadow: var(--shadow-md);
                color: #fff;
                font-size: 1.6rem;
            }

            .auth-card h1 {
                margin-top: 0.75rem;
                margin-bottom: 0.25rem;
                font-size: 1.25rem;
                font-weight: 700;
            }

            .auth-welcome {
                color: var(--text-secondary);
                max-width: 44ch;
                margin: 0.25rem auto 0.75rem auto;
                line-height: 1.5;
            }

            .auth-card__body {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                padding: 0;
                margin-top: 0.5rem;
            }

            .auth-form .form-control { height: calc(1.5em + 1.25rem); }

            .auth-actions { margin-top: 0.5rem; }

            @media (max-width: 480px) {
                .auth-card { padding: 1.25rem; }
                .auth-logo { width: 76px; height: 76px; }
            }
        </style>

        <div class="auth-card__brand">
            <div class="auth-logo" aria-hidden="true">
                <i class="bi bi-ticket-detailed-fill"></i>
            </div>
            <h1 class="mb-0">Support Ticket Pro</h1>
            <p class="auth-welcome small mb-0">Bienvenue — connectez-vous pour gérer vos demandes de support rapidement et efficacement.</p>
        </div>

        <div class="auth-card__body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger rounded-4 shadow-sm mb-4">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form action="/login" method="post" class="auth-form">
                <div class="mb-3">
                    <label for="email" class="form-label text-secondary d-block text-start">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Entrer votre email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label text-secondary d-block text-start">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Entrer votre mot de passe" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center auth-actions">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                    </div>
                    <a href="#" class="text-primary text-decoration-none small">Mot de passe oublié ?</a>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">SE CONNECTER</button>
                </div>
            </form>

            <div class="text-center mt-3 small">
                <p class="text-muted mb-0">Vous n'avez pas encore de compte ? <a href="/register" class="fw-semibold text-primary text-decoration-none">Créer un compte</a></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
