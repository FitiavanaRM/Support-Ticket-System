<?php

declare(strict_types=1);

// Vue de la page d'inscription. Elle affiche un formulaire moderne et gère
// l'affichage des erreurs ainsi que la conservation des valeurs saisies.

$title = 'Inscription';
$pageTitle = 'Inscription';
$pageDescription = 'Créez votre compte pour commencer à créer et suivre vos demandes.';

$errors = $errors ?? [];
$old = $old ?? [];

$flattenedErrors = [];
if (is_array($errors)) {
    foreach ($errors as $error) {
        if (is_string($error)) {
            $flattenedErrors[] = $error;
            continue;
        }

        if (is_array($error)) {
            foreach ($error as $message) {
                if (is_string($message)) {
                    $flattenedErrors[] = $message;
                }
            }
        }
    }
}

ob_start();
?>
<div class="auth-page min-vh-100 px-3 py-5">
    <div class="auth-card shadow-xl rounded-4 overflow-hidden border border-white border-opacity-10 w-100">
        <div class="auth-card__brand px-5 pt-5 pb-3">
            <div class="auth-logo mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 18px; background: linear-gradient(135deg, #2563eb, #4f46e5); box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25);">
                <span class="auth-logo__icon text-white fs-3"><i class="bi bi-person-plus-fill"></i></span>
            </div>
            <h1 class="h3 fw-bold mb-2">Créer un compte</h1>
            <p class="text-muted mb-3 mx-auto" style="max-width: 420px; line-height: 1.6;">Inscrivez-vous pour accéder au support et gérer vos tickets comme un pro.</p>
        </div>

        <div class="auth-card__body px-5 pb-5">
            <?php if (!empty($flattenedErrors)): ?>
                <div class="alert alert-danger rounded-4 shadow-sm mb-4">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($flattenedErrors as $error): ?>
                            <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/register" method="post" class="auth-card__form row g-3">
                <div class="col-12 text-start">
                    <label for="full_name" class="form-label text-secondary">Nom complet</label>
                    <div class="input-group input-group-lg auth-input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control form-control-lg rounded-start-pill rounded-end-pill border-start-0" id="full_name" name="full_name" value="<?= htmlspecialchars($old['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Jean Dupont" required>
                    </div>
                </div>
                <div class="col-12 text-start">
                    <label for="email" class="form-label text-secondary">Email</label>
                    <div class="input-group input-group-lg auth-input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" class="form-control form-control-lg rounded-start-pill rounded-end-pill border-start-0" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Entrer votre email" required>
                    </div>
                </div>
                <div class="col-md-6 text-start">
                    <label for="password" class="form-label text-secondary">Mot de passe</label>
                    <div class="input-group input-group-lg auth-input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control form-control-lg rounded-start-pill rounded-end-pill border-start-0" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="col-md-6 text-start">
                    <label for="password_confirmation" class="form-label text-secondary">Confirmation</label>
                    <div class="input-group input-group-lg auth-input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control form-control-lg rounded-start-pill rounded-end-pill border-start-0" id="password_confirmation" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
                    </div>
                </div>
                <div class="col-12 text-start">
                    <label class="form-label text-secondary d-block mb-2">Sélectionnez votre rôle</label>
                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                        <label class="btn btn-outline-secondary flex-fill rounded-pill me-2">
                            <input type="radio" name="role" id="role_client" value="CLIENT" autocomplete="off" <?= isset($old['role']) && $old['role'] === 'CLIENT' ? 'checked' : '' ?>> Client
                        </label>
                        <label class="btn btn-outline-secondary flex-fill rounded-pill">
                            <input type="radio" name="role" id="role_admin" value="ADMIN" autocomplete="off" <?= isset($old['role']) && $old['role'] === 'ADMIN' ? 'checked' : '' ?>> Administrateur
                        </label>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">S'INSCRIRE</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="text-muted mb-0">Vous avez déjà un compte ? <a href="/login" class="fw-semibold text-primary text-decoration-none">Se connecter</a></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
